<?php

namespace Edge\QA;

use Symfony\Component\Console\Helper\Table;

trait CodeAnalysisTasks
{
    /** @var array [tool => optionSeparator] */
    private $tools = array(
        'phpmetrics' => ['Edge\QA\Tool\PhpMetrics', 'Edge\QA\Tool\PhpMetricsV2'],
        'phploc' => 'Edge\QA\Tool\Phploc',
        'phpcs' => ['Edge\QA\Tool\Phpcs', 'Edge\QA\Tool\PhpcsV3'],
        'php-cs-fixer' => 'Edge\QA\Tool\PhpCsFixer',
        'phpmd' => 'Edge\QA\Tool\Phpmd',
        'pdepend' => 'Edge\QA\Tool\Pdepend',
        'phpcpd' => 'Edge\QA\Tool\Phpcpd',
        'parallel-lint' => 'Edge\QA\Tool\ParallelLint',
        'phpstan' => 'Edge\QA\Tool\Phpstan',
        'phpunit' => 'Edge\QA\Tool\Phpunit',
        'psalm' => 'Edge\QA\Tool\Psalm',
    );
    /** @var Options */
    private $options;
    /** @var Config */
    private $config;
    /** @var Task\ToolVersions */
    private $toolVersions;
    /** @var Task\ToolSummary */
    private $toolSummary;
    /** @var RunningTool[] */
    private $usedTools;
    /** @var string[] */
    private $skippedTools;

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
        $table = new Table($this->getOutput());
        $table->setHeaders(['Tool', 'Version', 'Authors / Info']);
        foreach ($this->toolVersions->__invoke() as $tool => $version) {
            $table->addRow(array(
                "<comment>{$tool}</comment>",
                $version['version_normalized'],
                $version['authors'],
            ));
        }
        $table->render();
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
     * @option $report build HTML report (only when output format is file)
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
            'report' => false,
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
        $this->config = new Config();
        $this->config->loadUserConfig($opts['config']);
        foreach ($this->tools as $id => $handler) {
            $handlers = array_map(
                function ($handler) {
                    return $handler::$SETTINGS + ['handler' => $handler];
                },
                (array) $handler
            );
            if (count($handlers) > 1) {
                $handlers = array_filter(
                    $handlers,
                    function (array $config) {
                        return isset($config['internalClass']) ? class_exists($config['internalClass']) : true;
                    }
                );
            }
            $this->tools[$id] = array_shift($handlers);
        }
        $this->toolVersions = new Task\ToolVersions($this->tools, $this->config);
    }

    private function loadOptions(array $opts)
    {
        if (!$opts['analyzedDirs']) {
            $opts['analyzedDirs'] = $opts['analyzedDir'] ?: './';
            if ($opts['analyzedDir']) {
                $this->yell("Option --analyzedDir is deprecated, please use option --analyzedDirs");
            }
        }

        $this->options = new Options($opts);
        list($this->usedTools, $this->skippedTools) = $this->options->buildRunningTools($this->tools, $this->config);
        $this->toolSummary = new Task\ToolSummary($this->options, $this->usedTools, $this->skippedTools);
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
        foreach ($this->usedTools as $tool) {
            $exec = $this->toolToExec($tool);
            $tool->process = $group->process($exec);
        }
        $group->printed($this->options->isOutputPrinted)->run();
    }

    /** @return \Robo\Task\Base\Exec */
    private function toolToExec(RunningTool $tool)
    {
        $customBinary = $this->config->getCustomBinary($tool);
        $binary = $customBinary ?: pathToBinary((string) $tool);
        $process = $this->taskExec($binary);

        $handlerClass = $this->tools[(string) $tool]['handler'];
        $handler = new $handlerClass($this->config, $this->options, $tool);
        $args = $handler($tool);

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
        foreach ($this->usedTools as $tool) {
            if (!$tool->htmlReport) {
                $tool->htmlReport = $this->options->rawFile("{$tool}.html");
            }
            if ($tool->hasOutput(OutputMode::XML_CONSOLE_OUTPUT)) {
                file_put_contents($this->options->rawFile("{$tool}.xml"), $tool->process->getOutput());
            }

            if ($tool->hasOutput(OutputMode::RAW_CONSOLE_OUTPUT)) {
                twigToHtml(
                    'cli.html.twig',
                    array(
                        'tool' => (string) $tool,
                        'process' => $tool->process
                    ),
                    $this->options->rawFile("{$tool}.html")
                );
            } else {
                xmlToHtml(
                    $tool->getXmlFiles(),
                    $this->config->path("report.{$tool}"),
                    $tool->htmlReport,
                    ['root-directory' => $this->options->getCommonRootPath()]
                );
            }
        }
        twigToHtml(
            'phpqa.html.twig',
            array(
                'summary' => $this->toolSummary->__invoke(),
                'versions' => $this->toolVersions->__invoke(),
                'buildDir' => $this->options->rawFile(''),
                'createdFiles' => glob("{$this->options->rawFile('')}/*"),
                'commands' => array(
                    'phpqa' => 'cd "' . getcwd() . "\" && \\\n" . PHPQA_USED_COMMAND,
                    'files' => 'ls -lA "' . realpath(getcwd() . '/' . $this->options->rawFile('')) . '"',
                ),
            ),
            $this->options->rawFile('phpqa.html')
        );
    }

    private function buildSummary()
    {
        $summary = new Task\TableSummary($this->getOutput());
        return $summary($this->toolSummary);
    }
}
