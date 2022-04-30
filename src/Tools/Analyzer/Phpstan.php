<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class Phpstan extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => ' ',
        'outputMode' => OutputMode::XML_CONSOLE_OUTPUT,
        'xml' => ['phpstan.xml'],
        'errorsXPath' => '//checkstyle/file/error',
        'composer' => 'phpstan/phpstan',
        'internalDependencies' => [
            'nette/neon' => 'Nette\Neon\Neon',
        ],
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

        $defaultConfigFile = $this->config->path('phpstan.standard') ?: (getcwd() . '/phpstan.neon');
        $existingConfig = file_exists($defaultConfigFile)
            ? \Nette\Neon\Neon::decode(file_get_contents($defaultConfigFile))
            : [];

        $config = self::buildConfig(
            $existingConfig,
            $createAbsolutePaths($this->options->getAnalyzedDirs()),
            $createAbsolutePaths($this->options->ignore->phpstan())
        );

        $phpstanConfig = "# Configuration generated in phpqa\n" . \Nette\Neon\Neon::encode($config);
        $neonFile = $this->saveDynamicConfig($phpstanConfig, 'neon');

        $args = array(
            'analyze',
            'ansi' => '',
            $this->getErrorFormatOption() => 'checkstyle',
            'level' => $this->config->value('phpstan.level'),
            'configuration' => $neonFile,
            $this->options->getAnalyzedDirs(' '),
        );
        if ($this->config->value('phpstan.memoryLimit')) {
            $args['memory-limit'] = $this->config->value('phpstan.memoryLimit');
        }
        return $args;
    }

    private function getErrorFormatOption()
    {
        return $this->toolVersionIs('<', '0.10.3') ?  'errorFormat' : 'error-format';
    }

    public static function buildConfig($existingConfig, $autoloadDirectories, $ignoredPaths)
    {
        if ($existingConfig !== []) {
            $config = $existingConfig + [
                'parameters' => [],
            ];
        } else {
            $config = [
                'parameters' => [
                    'autoload_directories' => $autoloadDirectories,
                    'excludePaths' => [
                        'analyseAndScan' => [],
                    ],
                ],
            ];
        }

        if (isset($config['parameters']['excludePaths']['analyseAndScan'])) {
            $config['parameters']['excludePaths']['analyseAndScan'] = array_merge(
                $config['parameters']['excludePaths']['analyseAndScan'],
                $ignoredPaths
            );
        } elseif (isset($config['parameters']['excludePaths'])) {
            $config['parameters']['excludePaths'] = array_merge(
                $config['parameters']['excludePaths'],
                $ignoredPaths
            );
        } elseif (isset($config['parameters']['excludes_analyse'])) {
            $config['parameters']['excludes_analyse'] = array_merge(
                $config['parameters']['excludes_analyse'],
                $ignoredPaths
            );
        } else {
            $config['parameters']['excludePaths'] = [
                'analyseAndScan' => $ignoredPaths,
            ];
        }

        return $config;
    }
}
