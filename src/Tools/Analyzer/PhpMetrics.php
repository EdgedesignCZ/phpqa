<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class PhpMetrics extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => ' ',
        'composer' => 'phpmetrics/phpmetrics',
        'outputMode' => OutputMode::CUSTOM_OUTPUT_AND_EXIT_CODE,
        'internalClass' => 'Hal\Application\Command\RunMetricsCommand',
    );

    public function __invoke()
    {
        $analyzedDirs = $this->options->getAnalyzedDirs();
        $analyzedDir = reset($analyzedDirs);
        if (count($analyzedDirs) > 1) {
            $this->writeln("<error>phpmetrics analyzes only first directory {$analyzedDir}</error>");
        }
        $args = array(
            $analyzedDir,
            $this->options->ignore->phpmetrics(),
            'extensions' => \Edge\QA\escapePath($this->config->csv('phpqa.extensions', '|')),
        );
        if ($this->options->isSavedToFiles) {
            $this->tool->htmlReport = $this->options->rawFile('phpmetrics.html');
            $args['offline'] = '';
            $args['report-html'] = \Edge\QA\escapePath($this->tool->htmlReport);
            $args['report-xml'] = $this->options->toFile('phpmetrics.xml');
            $configFile = $this->config->value('phpmetrics.config');
            if ($configFile) {
                $args['config'] = $configFile;
            }
        } else {
            $args['report-cli'] = '';
        }
        return $args;
    }
}
