<?php

namespace Edge\QA;

use Edge\QA\Tools\Tools;

trait CodeAnalysisTasks
{
    /** @var Tools */
    private $tools;
    /** @var Options */
    private $options;

    /**
     * @description Current versions
     * @option $config path directory with .phpqa.yml, <comment>@default</comment> <info>current working directory</info>
     */
    public function tools(
        $opts = [
            'config' => null,
        ]
    ) {
        $this->loadConfig($opts);
        $versions = new Task\TableVersions($this->getOutput());
        $versions($this->tools->getVersions());
    }

    /**
     * @description Executes QA tools
     * @option $analyzedDir <error>DEPRECATED, use --analyzedDirs</error>
     * @option $analyzedDirs csv path(s) to analyzed directories <comment>@default</comment> <info>./</info> <comment>@example</comment> src,tests
     * @option $buildDir path to output directory <comment>@default</comment> <info>build/</info>
     * @option $ignoredDirs csv <comment>@default</comment> <info>vendor</info> <comment>@example</comment> CI,bin,vendor
     * @option $ignoredFiles csv <comment>@example</comment> RoboFile.php
     * @option $tools csv with optional definition of allowed errors count <comment>@default</comment> <info>phpmetrics,phploc,phpcs,php-cs-fixer,phpmd,pdepend,phpcpd,phpstan,phpunit,psalm,security-checker,parallel-lint</info> <comment>@example</comment> phploc,phpmd:1,phpcs:0
     * @option $output output format <comment>@default</comment> <info>file</info> <comment>@example</comment> cli
     * @option $execution output format <comment>@default</comment> <info>parallel</info> <comment>@example</comment> no-parallel
     * @option $config path directory with .phpqa.yml, <comment>@default</comment> <info>current working directory</info>
     * @option $report build HTML report (only when output format is file), 'offline' for bundling assets with report <comment>@example</comment> offline
     */
    public function ci(
        $opts = [
            'analyzedDir' => null,
            'analyzedDirs' => null,
            'buildDir' => null,
            'ignoredDirs' => null,
            'ignoredFiles' => null,
            'tools' => null,
            'output' => null,
            'config' => null,
            'report' => null,
            'execution' => null,
        ]
    ) {
        $cliOptions = $this->normalizeCliOptions($opts);
        $this->loadConfig($cliOptions);
        $this->ciClean();
        $this->runTools();
        if ($this->options->hasReport) {
            $this->buildHtmlReport();
        }
        return $this->buildSummary();
    }

    private function normalizeCliOptions(array $options)
    {
        if (!$options['analyzedDirs'] && $options['analyzedDir']) {
            $options['analyzedDirs'] = $options['analyzedDir'];
            $this->yell("Option --analyzedDir is deprecated, please use option --analyzedDirs");
        }
        $options['report'] = $this->getInput()->hasParameterOption('--report') ? ($options['report'] ?: true) : false;
        return $options;
    }

    private function loadConfig(array $cliOptions)
    {
        $config = new Config();
        $config->loadUserConfig($cliOptions['config']);
        $this->tools = new Tools($config, function ($text) {
            $this->say($text);
        });
        if (!array_key_exists('report', $cliOptions)) {
            return; // hotfix for `phpqa tools`
        }
        if ($config->csv('extensions')) {
            $this->yell("Configuring root extensions .phpqa.yml is deprecated, please move it to phpqa.extensions");
        }

        // rather keep it because .phpqa.yml can be changed?
        $availableOptions = [
            'tools' => [
                'phpmetrics',
                'phploc',
                'phpcs',
                'php-cs-fixer',
                'phpmd',
                'pdepend',
                'phpcpd',
                'phpstan',
                'phpunit',
                'psalm',
                'security-checker',
                'parallel-lint',
                'deptrac',
            ],
            'analyzedDirs' => './',
            'buildDir' => 'build/',
            'ignoredDirs' => 'vendor',
            'ignoredFiles' => '',
            'output' => 'file',
            'report' => false,
            'execution' => 'parallel',
            'verbose' => false,
        ];
        $options = [];
        foreach ($availableOptions as $option => $defaultValue) {
            if ($cliOptions[$option]) {
                $options[$option] = $cliOptions[$option];
            } else {
                $options[$option] = $config->value("phpqa.{$option}") ?: $defaultValue;
                if (is_array($options[$option])) {
                    $options[$option] = implode(',', $options[$option]);
                }
            }
        }
        $this->options = new Options($options);
    }

    private function ciClean()
    {
        if ($this->options->isSavedToFiles) {
            if (is_dir($this->options->buildDir)) {
                $this->_cleanDir($this->options->buildDir);
            }
            $this->_mkdir($this->options->buildDir);
        }
    }

    private function runTools()
    {
        $group = $this->taskPhpqaRunner($this->options->isParallel);
        foreach ($this->tools->getExecutableTools($this->options) as $tool) {
            $exec = $this->toolToExec($tool);
            $tool->process = $group->process($exec);
        }
        $group->printed($this->options->isOutputPrinted)->run();
    }

    /** @return \Robo\Task\Base\Exec */
    private function toolToExec(RunningTool $tool)
    {
        list($binary, $args) = $this->tools->buildCommand($tool, $this->options);
        $process = $this->taskExec($binary);
        foreach ($args as $arg => $value) {
            if (is_int($arg)) {
                $this->addArgToExec($process, $value);
            } else {
                $this->addArgToExec($process, $tool->buildOption($arg, $value));
            }
        }
        return $process;
    }

    private function buildHtmlReport()
    {
        $assetsLoader = new Task\AssetsLoader($this->getOutput());
        $assets = $assetsLoader($this->options, $this->tools->getAssets());
        foreach ($this->tools->getExecutableTools($this->options) as $tool) {
            if (!$tool->htmlReport) {
                $tool->htmlReport = $this->options->rawFile("{$tool}.html");
            }
            if ($tool->hasOutput(OutputMode::XML_CONSOLE_OUTPUT)) {
                file_put_contents($this->options->rawFile("{$tool}.xml"), trim($tool->process->getOutput()));
            }

            if ($tool->hasOutput(OutputMode::RAW_CONSOLE_OUTPUT)) {
                twigToHtml(
                    'cli.html.twig',
                    array(
                        'tool' => (string) $tool,
                        'process' => $tool->process,
                        'assets' => $assets,
                    ),
                    $this->options->rawFile("{$tool}.html")
                );
            } else {
                xmlToHtml(
                    $tool->getXmlFiles(),
                    $this->tools->getReport($tool),
                    $tool->htmlReport,
                    ['root-directory' => $this->options->getCommonRootPath()] + $assets
                );
            }
        }
        twigToHtml(
            'phpqa.html.twig',
            array(
                'summary' => $this->tools->getSummary($this->options),
                'versions' => $this->tools->getVersions(),
                'buildDir' => $this->options->rawFile(''),
                'createdFiles' => glob("{$this->options->rawFile('')}/*"),
                'commands' => array(
                    'phpqa' => 'cd "' . getcwd() . "\" && \\\n" . PHPQA_USED_COMMAND,
                    'files' => 'ls -lA "' . realpath(getcwd() . '/' . $this->options->rawFile('')) . '"',
                ),
                'assets' => $assets,
            ),
            $this->options->rawFile('phpqa.html')
        );
    }

    private function buildSummary()
    {
        $summary = new Task\TableSummary($this->getOutput());
        return $summary($this->tools->getSummary($this->options));
    }
}
