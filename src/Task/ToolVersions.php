<?php

namespace Edge\QA\Task;

use Robo\Task\Base\Exec;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Edge\QA\Config;

class ToolVersions
{
    private $output;

    public function __construct(OutputInterface $p)
    {
        $this->output = $p;
    }

    public function __invoke(array $qaTools, Config $config)
    {
        $composerPackages = $this->findComposerPackages();
        if ($composerPackages) {
            $this->composerInfo($qaTools, $composerPackages, $config);
        } else {
            $this->consoleInfo(array_keys($qaTools));
        }
    }

    private function findComposerPackages()
    {
        $installedJson = COMPOSER_BINARY_DIR . '/../composer/installed.json';
        if (!is_file($installedJson)) {
            return [];
        }

        $installedTools = json_decode(file_get_contents($installedJson));
        if (!is_array($installedTools)) {
            return [];
        }

        $tools = array();
        foreach ($installedTools as $tool) {
            $tools[$tool->name] = $tool;
        }

        return $tools + [
            'edgedesign/phpqa' => (object) [
                'version_normalized' => PHPQA_VERSION,
                'authors' => [(object) ['name' => "Zdeněk Drahoš"]],
            ]
        ];
    }

    private function composerInfo(array $qaTools, array $composerPackages, Config $phpqaConfig)
    {
        $table = new Table($this->output);
        $table->setHeaders(['Tool', 'Version', 'Authors / Info']);
        $table->addRow($this->toolToTableRow('phpqa', 'edgedesign/phpqa', $composerPackages, $phpqaConfig));
        foreach ($qaTools as $tool => $config) {
            $table->addRow($this->toolToTableRow($tool, $config['composer'], $composerPackages, $phpqaConfig));
        }
        $table->render();
    }

    private function toolToTableRow($tool, $composerPackage, array $composerPackages, Config $phpqaConfig)
    {
        $customBinary = $phpqaConfig->getCustomBinary($tool);
        if ($customBinary) {
            $version = $this->loadVersionFromConsoleCommand("{$customBinary} --version");
            $composerInfo = [
                'version' => $version,
                'version_normalized' => $version,
                'authors' => [(object) ['name' => "<comment>{$customBinary}</comment>"]],
            ];
        } elseif (array_key_exists($composerPackage, $composerPackages)) {
            $composerInfo = get_object_vars($composerPackages[$composerPackage]);
        } else {
            $composerInfo = [
                'version' => '',
                'version_normalized' => '<error>not installed</error>',
                'authors' => [(object) ['name' => "<info>composer require {$composerPackage}</info>"]],
            ];
        }
            
        $composerInfo += [
            'version_normalized' => '',
            'authors' => [(object) ['name' => '']],
        ];

        return array(
            "<comment>{$tool}</comment>",
            $this->normalizeVersion($composerInfo),
            $this->groupAuthors($composerInfo['authors'])
        );
    }

    private function normalizeVersion(array $composerInfo)
    {
        if ($composerInfo['version_normalized'] == '9999999-dev') {
            return $composerInfo['version'];
        }
        return preg_replace('/\.0$/s', '', $composerInfo['version_normalized']);
    }

    private function groupAuthors(array $composerAuthors)
    {
        return implode(
            ',',
            array_map(
                function ($author) {
                    return $author->name;
                },
                $composerAuthors
            )
        );
    }

    private function consoleInfo(array $tools)
    {
        $this->output->writeln([
            '<comment>phpqa ' . PHPQA_VERSION . '</comment>',
            '',
        ]);

        foreach ($tools as $tool) {
            $binary = \Edge\QA\pathToBinary($tool);
            $versionCommand = $tool == 'parallel-lint' ? $binary : "{$binary} --version";
            $this->output->writeln($this->loadVersionFromConsoleCommand($versionCommand));
        }
    }

    private function loadVersionFromConsoleCommand($command)
    {
        $exec = new Exec($command);
        $result = $exec
            ->printed(false)
            ->run()
            ->getMessage();
        return $this->getFirstLine($result);
    }

    private function getFirstLine($string)
    {
        return strtok($string, "\n");
    }
}
