<?php

namespace Edge\QA\Task;

use Robo\Task\Base\Exec;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ToolVersions
{
    private $output;

    public function __construct(OutputInterface $p)
    {
        $this->output = $p;
    }

    public function __invoke(array $qaTools)
    {
        $composerPackages = $this->findComposerPackages();
        if ($composerPackages) {
            $this->composerInfo($qaTools, $composerPackages);
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

    private function composerInfo(array $qaTools, array $composerPackages)
    {
        $table = new Table($this->output);
        $table->setHeaders(['Tool', 'Version', 'Authors']);
        $table->addRow($this->toolToTableRow('phpqa', 'edgedesign/phpqa', $composerPackages));
        foreach ($qaTools as $tool => $config) {
            $table->addRow($this->toolToTableRow($tool, $config['composer'], $composerPackages));
        }
        $table->render();
    }

    private function toolToTableRow($tool, $composerPackage, array $composerPackages)
    {
        $composerInfo = array_key_exists($composerPackage, $composerPackages) ?
            get_object_vars($composerPackages[$composerPackage]) :
            [
                'version' => '',
                'version_normalized' => '<error>not installed</error>',
                'authors' => [(object) ['name' => "<info>composer require {$composerPackage}</info>"]],
            ];
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
            $versionCommand = $tool == 'parallel-lint' ? $tool : "{$tool} --version";
            $this->loadVersionFromConsoleCommand($versionCommand);
        }
    }

    private function loadVersionFromConsoleCommand($command)
    {
        $exec = new Exec(\Edge\QA\pathToBinary($command));
        $result = $exec
            ->printed(false)
            ->run()
            ->getMessage();
        $this->output->writeln($this->getFirstLine($result));
    }

    private function getFirstLine($string)
    {
        return strtok($string, "\n");
    }
}
