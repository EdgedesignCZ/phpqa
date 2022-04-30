<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class SecurityChecker extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT,
        'composer' => 'enlightn/security-checker',
    );

    public function __invoke()
    {
        $composerLockFromConfig = $this->config->path('security-checker.composerLock');
        $composerLock = file_exists($composerLockFromConfig)
            ? $composerLockFromConfig
            : $this->detectComposerLock();

        return [
            'security:check',
            $composerLock,
        ];
    }

    private function detectComposerLock()
    {
        foreach ($this->options->getAnalyzedDirs() as $escapedDir) {
            $dir = rtrim(trim($escapedDir, '"'), '/');
            $path = "{$dir}/composer.lock";
            if (file_exists($path)) {
                return $path;
            }
        }
        return getcwd() . '/composer.lock';
    }
}
