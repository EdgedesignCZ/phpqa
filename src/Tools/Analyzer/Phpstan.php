<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class Phpstan extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => ' ',
        'internalClass' => 'PHPStan\Analyser\Analyser',
        'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT,
        'composer' => 'phpstan/phpstan',
    );

    public function __invoke()
    {
        $createAbsolutePaths = function (array $relativeDirs) {
            return array_values(array_filter(array_map(
                function ($relativeDir) {
                    return '%currentWorkingDirectory%/' . trim($relativeDir, '"');
                },
                $relativeDirs
            )));
        };

        $defaultConfig = $this->config->path('phpstan.standard') ?: (getcwd() . '/phpstan.neon');
        if (file_exists($defaultConfig)) {
            $config = \Nette\Neon\Neon::decode(file_get_contents($defaultConfig));
            $config['parameters'] += [
                'excludes_analyse' => [],
            ];
        } else {
            $config = [
                'parameters' => [
                    'autoload_directories' => $createAbsolutePaths($this->options->getAnalyzedDirs()),
                    'excludes_analyse'     => [],
                ],
            ];
        }

        $config['parameters']['excludes_analyse'] = array_merge(
            $config['parameters']['excludes_analyse'],
            $createAbsolutePaths($this->options->ignore->phpstan())
        );

        $phpstanConfig = "# Configuration generated in phpqa\n" . \Nette\Neon\Neon::encode($config);
        $neonFile = $this->saveDynamicConfig($phpstanConfig, 'neon');

        return array(
            'analyze',
            'ansi' => '',
            'level' => $this->config->value('phpstan.level'),
            'configuration' => $neonFile,
            $this->options->getAnalyzedDirs(' '),
        );
    }
}
