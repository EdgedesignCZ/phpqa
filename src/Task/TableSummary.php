<?php

namespace Edge\QA\Task;

use Edge\QA\Options;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class TableSummary
{
    private $options;
    private $output;
    
    public function __construct(Options $o, OutputInterface $p)
    {
        $this->options = $o;
        $this->output = $p;
    }

    /**
     * @param \Edge\QA\RunningTool[] $usedTools
     * @return int
     */
    public function __invoke(array $usedTools)
    {
        $this->writeln('', 'cyan');
        $table = new Table($this->output);
        if ($this->options->isSavedToFiles) {
            $table->setHeaders(array('Tool', 'Allowed Errors', 'Errors count', 'Is OK?', 'HTML report'));
        } else {
            $table->setHeaders(array('Tool', 'Allowed exit code', 'Exit code', 'Is OK?'));
        }
        $totalErrors = 0;
        $failedTools = [];
        foreach ($usedTools as $tool) {
            list($isOk, $errorsCount) = $tool->analyzeResult(!$this->options->isSavedToFiles);
            $totalErrors += (int) $errorsCount;
            $row = array(
                "<comment>{$tool}</comment>",
                $tool->getAllowedErrorsCount(),
                $errorsCount,
                $this->getStatus($isOk),
            );
            if ($this->options->isSavedToFiles) {
                $row[] = $tool->htmlReport;
            }
            $table->addRow($row);
            if (!$isOk) {
                $failedTools[] = (string) $tool;
            }
        }
        $table->addRow(new TableSeparator());
        $row = array(
            '<comment>phpqa</comment>',
            '',
            $failedTools ? "<error>{$totalErrors}</error>" : $totalErrors,
            $this->getStatus(empty($failedTools)),
        );
        if ($this->options->isSavedToFiles) {
            $row[] = $this->options->hasReport ? $this->options->rawFile("phpqa.html") : '';
        }
        $table->addRow($row);
        $table->render();
        return $this->result($failedTools);
    }

    private function result(array $failedTools)
    {
        if ($failedTools) {
            $this->writeln('Failed tools: <comment>' . implode(', ', $failedTools) . '</comment>', 'red');
            return 1;
        } else {
            $this->writeln('No failed tools', 'green');
            return 0;
        }
    }

    private function getStatus($isOk)
    {
        return $isOk ? '<info>✓</info>' : '<error>x</error>';
    }

    // copy-paste from \Robo\Common\TaskIO
    private function writeln($text, $color)
    {
        $this->output->writeln(
            "\n<fg=white;bg={$color};options=bold>[phpqa]</fg=white;bg={$color};options=bold> {$text}"
        );
    }
}
