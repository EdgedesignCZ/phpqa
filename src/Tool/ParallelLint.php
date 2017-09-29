<?php

namespace Edge\QA\Tool;

use Edge\QA\OutputMode;

class ParallelLint extends Tool
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
            $this->options->getAnalyzedDirs(' '),
        );
    }
}
