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
     * @option $config path directory with .phpqa.yml, @default current working directory
     */
    public function tools(
        $opts = array(
            'config' => '',
        )
    ) {
        $this->loadConfig($opts);
        $versions = new Task\TableVersions($this->getOutput());
        $versions($this->tools->getVersions());
    }

    /**
     * @description Executes QA tools
     * @option $analyzedDir DEPRECATED, use --analyzedDirs
     * @option $analyzedDirs csv path(s) to analyzed directories @default ./ @example src,tests
     * @option $buildDir path to output directory
     * @option $ignoredDirs csv @example CI,bin,vendor
     * @option $ignoredFiles csv @example RoboFile.php
     * @option $tools csv with optional definition of allowed errors count @example phploc,phpmd:1,phpcs:0
     * @option $output output format @example cli
     * @option $config path directory with .phpqa.yml, @default current working directory
     * @option $report build HTML report (only when output format is file), 'offline' for bundling assets with report
     */
    public function ci(
        $opts = array(
            'analyzedDir' => '',
            'analyzedDirs' => '',
            'buildDir' => 'build/',
            'ignoredDirs' => 'vendor',
            'ignoredFiles' => '',
            'tools' => 'phploc,phpcpd,phpcs,pdepend,phpmd,phpmetrics,parallel-lint',
            'output' => 'file',
            'config' => '',
            'report' => '',
            'execution' => 'parallel'
        )
    ) {
        $this->loadConfig($opts);
        $this->loadOptions($opts);
        $this->ciClean();
        $this->runTools();
        if ($this->options->hasReport) {
            $this->buildHtmlReport();
        }
        return $this->buildSummary();
    }

    private function loadConfig(array $opts)
    {
        $config = new Config();
        $config->loadUserConfig($opts['config']);
        $this->tools = new Tools($config, function ($text) {
            $this->say($text);
        });
    }

    private function loadOptions(array $opts)
    {
        if (!$opts['analyzedDirs']) {
            $opts['analyzedDirs'] = $opts['analyzedDir'] ?: './';
            if ($opts['analyzedDir']) {
                $this->yell("Option --analyzedDir is deprecated, please use option --analyzedDirs");
            }
        }
        $opts['report'] = $this->getInput()->hasParameterOption('--report') ? ($opts['report'] ?: true) : false;
        $this->options = new Options($opts);
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
