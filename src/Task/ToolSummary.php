<?php

namespace Edge\QA\Task;

use Edge\QA\Options;

class ToolSummary
{
    private $options;
    /** @var \Edge\QA\RunningTool[] */
    private $usedTools;
    /** @var string[] $this->skippedTools */
    private $skippedTools;

    public function __construct(Options $o, array $usedTools, array $skippedTools)
    {
        $this->options = $o;
        $this->usedTools = $usedTools;
        $this->skippedTools = $skippedTools;
    }

    public function __invoke()
    {
        $totalErrors = 0;
        $failedTools = [];
        $results = [];
        foreach ($this->usedTools as $tool) {
            list($isOk, $errorsCount) = $tool->analyzeResult(!$this->options->isSavedToFiles);
            $totalErrors += (int) $errorsCount;
            $results[$tool->binary] = array(
                'areErrorsAnalyzed' => $tool->getAllowedErrorsCount() !== null,
                'allowedErrorsCount' => $tool->getAllowedErrorsCount(),
                'errorsCount' => $errorsCount,
                'hasSucceeded' => $isOk,
                'htmlReport' => $this->options->isSavedToFiles ? $tool->htmlReport : null,
                'otherReports' => $tool->getHtmlRootReports(),
            );
            if (!$isOk) {
                $failedTools[] = (string) $tool;
            }
        }
        $results['phpqa'] = array(
            'areErrorsAnalyzed' => true,
            'allowedErrorsCount' => null,
            'errorsCount' => $totalErrors,
            'hasSucceeded' => count($failedTools) == 0,
            'htmlReport' => $this->options->isSavedToFiles ? $this->options->rawFile("phpqa.html") : null,
            'otherReports' => [],
        );
        return [
            'isErrorsCountAnalyzed' => $this->options->isSavedToFiles,
            'failedTools' => $failedTools,
            'skippedTools' => $this->skippedTools,
            'tools' => $results,
        ];
    }
}
