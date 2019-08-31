<?php

namespace Edge\QA\Tools;

use Symfony\Component\Process\Process;

class GetVersions
{
    public function getToolVersion(array $toolSettings)
    {
        $versions = $this->__invoke(['tool' => $toolSettings + ['customBinary' => null]]);
        return  $versions['tool']['version_normalized'];
    }

    public function __invoke(array $tools)
    {
        $composer = [
            'edgedesign/phpqa' => (object) [
                'version' => PHPQA_VERSION,
                'version_normalized' => PHPQA_VERSION,
                'authors' => [(object) ['name' => "Zdeněk Drahoš"]],
            ]
        ] + $this->findComposerPackages();
        $versions = [];
        $versions['phpqa'] = $this->analyzeTool('phpqa', 'edgedesign/phpqa', $composer);
        foreach ($tools as $tool => $config) {
            $versions[$tool] = $this->analyzeTool($tool, $config['composer'], $composer, $config['customBinary']);
        }
        return $versions;
    }

    private function findComposerPackages()
    {
        $installedJson = COMPOSER_VENDOR_DIR . '/composer/installed.json';
        if (!is_file($installedJson)) {
            return [];
        }

        $installedTools = json_decode((string) file_get_contents($installedJson));
        if (!is_array($installedTools)) {
            return [];
        }

        $tools = array();
        foreach ($installedTools as $tool) {
            $tools[$tool->name] = $tool;
        }

        return $tools;
    }

    private function analyzeTool($tool, $composerPackage, array $composerPackages, $customBinary = null)
    {
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

        return array(
            'version' => $composerInfo['version'],
            'version_normalized' => $this->normalizeVersion($composerInfo),
            'authors' => $this->groupAuthors($composerInfo['authors']),
            'composer' => $composerPackage,
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
