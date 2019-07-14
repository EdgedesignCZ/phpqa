<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class ParallelLint extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => ' ',
        'internalClass' => 'JakubOnderka\PhpParallelLint\ParallelLint',
        'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT,
        'composer' => 'jakub-onderka/php-parallel-lint',
    );

    public function __invoke()
    {
        return array(
            $this->options->ignore->parallelLint(),
            "-e {$this->config->csv('phpqa.extensions')}",
            $this->options->getAnalyzedDirs(' '),
        );
    }
}
