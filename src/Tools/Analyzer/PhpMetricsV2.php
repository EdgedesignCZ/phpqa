<?php

namespace Edge\QA\Tools\Analyzer;

class PhpMetricsV2 extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'composer' => 'phpmetrics/phpmetrics',
    );

    public function __invoke()
    {
        $args = array(
            $this->options->ignore->phpmetrics2(),
            'extensions' => $this->config->csv('phpqa.extensions')
        );
        if ($this->options->isSavedToFiles) {
            $this->tool->htmlReport = $this->options->rawFile('phpmetrics/index.html');
            $args['report-html'] = $this->options->toFile('phpmetrics/');
            $args['report-violations'] = $this->options->toFile('phpmetrics.xml');
        }

        $gitBinary = $this->config->value('phpmetrics.git');
        if ($gitBinary) {
            $args['git'] = is_file($gitBinary) ? $gitBinary : 'git';
        }

        $junit = $this->config->value('phpmetrics.junit');
        if ($junit) {
            $args['junit'] = $junit;
        }

        $analyzedDirs = $this->options->getAnalyzedDirs(',');
        $composer = $this->config->path('phpmetrics.composer');
        if ($composer) {
            $analyzedDirs .= ",{$composer}," . str_replace('composer.json', 'composer.lock', $composer);
        }
        $args[] = $analyzedDirs;
        return $args;
    }
}
