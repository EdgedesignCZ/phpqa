<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class SecurityChecker extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'internalClass' => 'Enlightn\SecurityChecker\AdvisoryAnalyzer',
        'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT,
        'composer' => 'enlightn/security-checker',
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
            'security:check',
            $composerLock,
        ];
    }
}
