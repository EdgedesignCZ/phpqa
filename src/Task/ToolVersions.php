<?php

namespace Edge\QA\Task;

use Symfony\Component\Process\Process;
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
        $phpqaPackages = [
            'edgedesign/phpqa' => (object) [
                'version_normalized' => PHPQA_VERSION,
                'authors' => [(object) ['name' => "Zdeněk Drahoš"]],
            ]
        ];
        $this->toolsToTable($qaTools, $composerPackages, $phpqaPackages, $config);
    }

    public function getVersions(array $qaTools, Config $phpqaConfig)
    {
        $composerPackages = $this->findComposerPackages();
        $phpqaPackages = [
            'edgedesign/phpqa' => (object) [
                'version' => PHPQA_VERSION,
                'version_normalized' => PHPQA_VERSION,
                'authors' => [(object) ['name' => "Zdeněk Drahoš"]],
            ]
        ];
        $versions = [];
        $versions['phpqa'] = $this->toolToTableRow('phpqa', 'edgedesign/phpqa', $phpqaPackages, $phpqaConfig, true);
        foreach ($qaTools as $tool => $config) {
            $versions[$tool] = $this->toolToTableRow($tool, $config['composer'], $composerPackages, $phpqaConfig, true);
        }
        return $versions;
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

        return $tools;
    }

    private function toolsToTable(array $qaTools, array $composerPackages, array $phpqaPackages, Config $phpqaConfig)
    {
        $table = new Table($this->output);
        $table->setHeaders(['Tool', 'Version', 'Authors / Info']);
        $table->addRow($this->toolToTableRow('phpqa', 'edgedesign/phpqa', $phpqaPackages, $phpqaConfig));
        foreach ($qaTools as $tool => $config) {
            $table->addRow($this->toolToTableRow($tool, $config['composer'], $composerPackages, $phpqaConfig));
        }
        $table->render();
    }

    private function toolToTableRow($tool, $composerPackage, array $composerPackages, Config $phpqaConfig, $isOnlyAnalyzed = false)
    {
        $customBinary = $phpqaConfig->getCustomBinary($tool);
        if ($customBinary) {
            $versionCommand = "{$customBinary} --version";
            $version = $this->loadVersionFromConsoleCommand($versionCommand);
            $composerInfo = [
                'version' => $version,
                'version_normalized' => $version,
                'authors' => [(object) ['name' => "<comment>{$versionCommand}</comment>"]],
            ];
        } elseif (array_key_exists($composerPackage, $composerPackages)) {
            $composerInfo = get_object_vars($composerPackages[$composerPackage]) + [
                'version_normalized' => '',
                'authors' => [(object) ['name' => '']],
            ];
        } elseif ($composerPackages) {
            $composerInfo = [
                'version' => '',
                'version_normalized' => '<error>not installed</error>',
                'authors' => [(object) ['name' => "<info>composer require {$composerPackage}</info>"]],
            ];
        } else {
            $binary = \Edge\QA\pathToBinary($tool);
            $versionCommand = $tool == 'parallel-lint' ? $binary : "{$binary} --version";
            $version = $this->loadVersionFromConsoleCommand($versionCommand);
            $composerInfo = [
                'version' => $version,
                'version_normalized' => $version ?: '<error>not installed</error>',
                'authors' => [(object) ['name' => $versionCommand
                    ? "<comment>{$versionCommand}</comment>"
                    : "<info>composer require {$composerPackage}</info>"]],
            ];
        }

        if ($isOnlyAnalyzed) {
            return $composerInfo['version'];
        }

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

    private function loadVersionFromConsoleCommand($command)
    {
        $process = new Process($command);
        $process->run();
        $firstLine = $this->getFirstLine($process->getOutput());
        return $this->extractVersion($firstLine);
    }

    private function getFirstLine($string)
    {
        return strtok($string, "\n");
    }

    private function extractVersion($text)
    {
        return str_replace(
            array(
                ' by Sebastian Bergmann and contributors.',
                'PHPUnit '
            ),
            '',
            $text
        );
    }
}
