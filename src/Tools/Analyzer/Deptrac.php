<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class Deptrac extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT,
        'composer' => 'qossmic/deptrac-shim',
    );

    public function __invoke()
    {
        return array(
            'analyze',
            $this->config->path('deptrac.depfile'),
            'formatter' => 'console',
            'report-uncovered' => '',
        );
    }
}
