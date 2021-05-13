<?php

namespace Edge\QA\Tools;

use Symfony\Component\Process\Process;

class GetVersions
{
    public function hasToolVersion(array $toolSettings, $operator, $version)
    {
        $versions = $this->__invoke(['tool' => $toolSettings]);
        $toolVersion = $versions['tool']['version_normalized'];
        return self::compareVersions($toolVersion, $operator, $version);
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
        $versions['phpqa'] = $this->analyzeTool('phpqa', ['edgedesign/phpqa'], $composer, [
            'hasCustomBinary' => false,
        ]);
        foreach ($tools as $tool => $config) {
            $packages = array_merge(
                [$config['composer']],
                array_key_exists('internalDependencies', $config) ? array_keys($config['internalDependencies']) : []
            );
            $versions[$tool] = $this->analyzeTool($tool, $packages, $composer, $config);
        }
        return $versions;
    }

    private function findComposerPackages()
    {
        $installedJson = COMPOSER_VENDOR_DIR . '/composer/installed.json';
        if (!is_file($installedJson)) {
            return [];
        }

        $rawTools = json_decode((string) file_get_contents($installedJson));
        $installedTools = is_object($rawTools) ? $rawTools->packages : $rawTools;
        if (!is_array($installedTools)) {
            return [];
        }

        $tools = array();
        foreach ($installedTools as $tool) {
            $tools[$tool->name] = $tool;
        }

        return $tools;
    }

    private function analyzeTool($tool, array $requiredPackages, array $composerPackages, array $binaries)
    {
        $notInstalledPackages = implode(' ', array_filter(
            $requiredPackages,
            function ($package) use ($composerPackages) {
                return !array_key_exists($package, $composerPackages);
            }
        ));

        if ($binaries['hasCustomBinary']) {
            $versionCommand = "{$binaries['runBinary']} --version";
            $version = $this->loadVersionFromConsoleCommand($versionCommand);
            $composerInfo = [
                'version' => $version,
                'version_normalized' => $version,
                'authors' => [(object) ['name' => "<comment>{$versionCommand}</comment>"]],
            ];
        } elseif (!$composerPackages) {
            $versionCommand = $tool == 'parallel-lint' ? $binaries['runBinary'] : "{$binaries['runBinary']} --version";
            $version = $this->loadVersionFromConsoleCommand($versionCommand);
            $composerInfo = [
                'version' => $version,
                'version_normalized' => $version ?: '<error>not installed</error>',
                'authors' => [
                    (object) [
                        'name' => $versionCommand
                            ? "<comment>{$versionCommand}</comment>"
                            : "<info>composer require {$notInstalledPackages}</info>"
                    ]
                ],
            ];
        } elseif ($notInstalledPackages) {
            $composerInfo = [
                'version' => '',
                'version_normalized' => '<error>not installed</error>',
                'authors' => [(object) ['name' => "<info>composer require {$notInstalledPackages}</info>"]],
            ];
        } else {
            $toolPackage = reset($requiredPackages);
            $composerInfo = get_object_vars($composerPackages[$toolPackage]) + [
                'version_normalized' => '',
                'authors' => [(object) ['name' => '']],
            ];
        }

        return array(
            'version' => $composerInfo['version'],
            'version_normalized' => $this->normalizeVersion($composerInfo),
            'authors' => $this->groupAuthors($composerInfo['authors']),
            'composer' => implode(' ', $requiredPackages),
        );
    }

    private function normalizeVersion(array $composerInfo)
    {
        if ($composerInfo['version_normalized'] == '9999999-dev') {
            return $composerInfo['version'];
        }
        return self::normalizeSemver($composerInfo['version_normalized']);
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
        $process = $this->createSymfonyProcess($command);
        $process->run();
        if ($process->getOutput()) {
            return self::extractVersionFromConsole($process->getOutput());
        } elseif ($process->getErrorOutput()) {
            return trim($process->getErrorOutput());
        }
        return "{$process->getExitCode()} {$process->getExitCodeText()}";
    }

    private function createSymfonyProcess($command)
    {
        if (method_exists('Symfony\Component\Process\Process', 'fromShellCommandline')) {
            return Process::fromShellCommandline($command);
        } else {
            return new Process($command);
        }
    }

    public static function extractVersionFromConsole($text)
    {
        $regexes = [
            'semver' => '(\d+\.\d+(\.\d+)?)',
            'dev version' => '(\d+\.x)',
        ];
        foreach ($regexes as $regex) {
            $match = [];
            preg_match($regex, $text, $match);
            if ($match) {
                return self::normalizeSemver($match[0]);
            }
        }
        return $text;
    }

    public static function normalizeSemver($semver)
    {
        return preg_replace('/\.0$/s', '', $semver);
    }

    public static function compareVersions($toolVersion, $operator, $version)
    {
        return $toolVersion && version_compare(str_replace(".x", "", $toolVersion), $version, $operator);
    }
}
