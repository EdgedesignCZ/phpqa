<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class Deptrac extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT,
        'composer' => 'deptrac/deptrac',
    );

    public function __invoke()
    {
        $args = array(
            'analyze',
        );
        if ($this->toolVersionIs('<', '0.20.0')) {
            $args[] = $this->config->path('deptrac.depfile');
        } else {
            $args['config-file'] = $this->config->path('deptrac.depfile');
        }
        $args += [
            'formatter' => 'console',
        ];
        if ($this->config->value('deptrac.reportUncovered')) {
            $args['report-uncovered'] = '';
        }
        return $args;
    }
}
