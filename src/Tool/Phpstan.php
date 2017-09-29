<?php

namespace Edge\QA\Tool;

use Edge\QA\OutputMode;

class Phpstan extends Tool
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
            $params = \Nette\Neon\Neon::decode(file_get_contents($defaultConfig))['parameters'] + [
                'excludes_analyse' => []
            ];
        } else {
            $params = [
                'autoload_directories' => $createAbsolutePaths($this->options->getAnalyzedDirs()),
                'excludes_analyse' => [],
            ];
        }

        $params['excludes_analyse'] = array_merge(
            $params['excludes_analyse'],
            $createAbsolutePaths($this->options->ignore->phpstan())
        );

        $neonDir = $this->options->isSavedToFiles ? $this->options->rawFile('') : getcwd();
        $neonFile = "{$neonDir}/phpstan-phpqa.neon";
        file_put_contents(
            $neonFile,
            "# Configuration generated in phpqa\n" .
            \Nette\Neon\Neon::encode(['parameters' => $params])
        );

        return array(
            'analyze',
            'ansi' => '',
            'level' => $this->config->value('phpstan.level'),
            'configuration' => $neonFile,
            $this->options->getAnalyzedDirs(' '),
        );
    }
}
