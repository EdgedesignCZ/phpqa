<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class LocalSecurityChecker extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT
    );

    public function __invoke()
    {
        $composerLock = getcwd() . "/composer.lock";
        foreach ($this->options->getAnalyzedDirs() as $escapedDir) {
            $dir = rtrim(trim($escapedDir, '"'), '/');
            $path = "{$dir}/composer.lock";
            if (file_exists($path)) {
                $composerLock = $path;
                break;
            }
        }

        return [
            'path' => $composerLock,
        ];
    }
}
