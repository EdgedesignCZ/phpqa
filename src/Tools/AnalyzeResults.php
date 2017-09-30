<?php

namespace Edge\QA\Tools;

use Edge\QA\Options;

class AnalyzeResults
{
    private $options;

    public function __construct(Options $o)
    {
        $this->options = $o;
    }

    /**
     * @param \Edge\QA\RunningTool[] $runningTools
     */
    public function __invoke(array $runningTools)
    {
        $totalErrors = 0;
        $failedTools = [];
        $notInstalledTools = [];
        $results = [];
        foreach ($runningTools as $tool) {
            if (!$tool->isExecutable) {
                $notInstalledTools[] = (string) $tool;
                continue;
            }
            list($isOk, $errorsCount) = $tool->analyzeResult(!$this->options->isSavedToFiles);
            $totalErrors += (int) $errorsCount;
            $results[(string) $tool] = array(
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
            'notInstalledTools' => $notInstalledTools,
            'tools' => $results,
        ];
    }
}
