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
            'extensions' => $this->config->csv('extensions')
        );
        if ($this->options->isSavedToFiles) {
            $this->tool->htmlReport = $this->options->rawFile('phpmetrics/index.html');
            $args['report-html'] = $this->options->toFile('phpmetrics/');
            $args['report-violations'] = $this->options->toFile('phpmetrics.xml');
        }
        if ($git = $this->config->value('phpmetrics.git')) {
            if (is_bool($git)) {
                $args[] = '--git';
            } else {
                $args['git'] = $git;
            }
        }
        $args[] = $this->options->getAnalyzedDirs(',');
        return $args;
    }
}
