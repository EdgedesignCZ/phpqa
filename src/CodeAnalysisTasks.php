<?php

namespace Edge\QA;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

trait CodeAnalysisTasks
{
    /** @var array [tool => optionSeparator] */
    private $tools = array(
        'phpmetrics' => array(
            'optionSeparator' => ' ',
            'transformedXml' => '',
            'htmlReport' => '',
        ),
        'phploc' => array(
            'optionSeparator' => ' ',
            'transformedXml' => 'phploc.xml',
            'htmlReport' => '',
        ),
        'phpcs' => array(
            'optionSeparator' => '=',
            'transformedXml' => 'checkstyle.xml',
            'htmlReport' => '',
        ),
        'phpmd' => array(
            'optionSeparator' => ' ',
            'transformedXml' => 'phpmd.xml',
            'htmlReport' => '',
        ),
        'pdepend' => array(
            'optionSeparator' => '=',
            'transformedXml' => 'pdepend-jdepend.xml',
            'htmlReport' => '',
        ),
        'phpcpd' => array(
            'optionSeparator' => ' ',
            'transformedXml' => 'phpcpd.xml',
            'htmlReport' => '',
        ),
    );
    /** @var Options */
    private $options;
    /** @var Config */
    private $config;
    /** @var array */
    private $usedTools;

    /**
     * @description Current versions
     */
    public function tools()
    {
        foreach (array_keys($this->tools) as $tool) {
            $this->_exec(pathToBinary("{$tool} --version"));
        }
    }

    /**
     * @description Executes QA tools
     * @option $analyzedDir path to analyzed directory
     * @option $buildDir path to output directory
     * @option $ignoredDirs csv @example CI,bin,vendor
     * @option $ignoredFiles csv @example RoboFile.php
     * @option $tools csv @example phploc,phpcpd
     * @option $output output format @example cli
     * @option $config path directory with .phpqa.yml, @default current working directory
     * @option $report build HTML report (only output format is file)
     */
    public function ci(
        $opts = array(
            'analyzedDir' => './',
            'buildDir' => 'build/',
            'ignoredDirs' => 'vendor',
            'ignoredFiles' => '',
            'tools' => 'phploc,phpcpd,phpcs,pdepend,phpmd,phpmetrics',
            'output' => 'file',
            'config' => '',
            'report' => false,
            'execution' => 'parallel',
        )
    ) {
        $this->loadOptions($opts);
        $this->ciClean();
        $this->runTools();
        if ($this->options->hasReport) {
            $this->buildReport();
        }
        $this->printSummary();
    }

    private function loadOptions(array $opts)
    {
        $this->options = new Options($opts);
        $this->usedTools = $this->options->filterTools($this->tools);
        $this->config = new Config();
        $this->config->loadCustomConfig($this->options->configDir);
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
        $group = $this->options->isParallel ? $this->taskParallelExec() : new Task\NonParallelExec();
        foreach ($this->usedTools as $tool => $config) {
            $exec = $this->toolToExec($tool, $config['optionSeparator']);
            $group->process($exec);
        }
        $group->printed($this->options->isOutputPrinted)->run();
    }

    /** @return \Robo\Task\Base\Exec */
    private function toolToExec($tool, $optionSeparator)
    {
        $binary = pathToBinary($tool);
        $process = $this->taskExec($binary);
        foreach ($this->$tool() as $arg => $value) {
            if (is_int($arg)) {
                $process->arg($value);
            } elseif ($value) {
                $process->arg("--{$arg}{$optionSeparator}{$value}");
            } else {
                $process->arg("--{$arg}");
            }
        }
        return $process;
    }

    private function phploc()
    {
        $args = array(
            'progress' => '',
            $this->options->ignore->bergmann(),
            $this->options->analyzedDir
        );
        if ($this->options->isSavedToFiles) {
            $args['log-xml'] = $this->options->toFile('phploc.xml');
        }
        return $args;
    }

    private function phpcpd()
    {
        $args = array(
            'progress' => '',
            $this->options->ignore->bergmann(),
            $this->options->analyzedDir,
            'min-lines' => $this->config->value('phpcpd.minLines'),
            'min-tokens' => $this->config->value('phpcpd.minTokens'),
        );
        if ($this->options->isSavedToFiles) {
            $args['log-pmd'] = $this->options->toFile('phpcpd.xml');
        }
        return $args;
    }

    private function phpcs()
    {
        $standard = $this->config->value('phpcs.standard');
        if (!in_array($standard, \PHP_CodeSniffer::getInstalledStandards())) {
            $standard = escapePath($this->config->path('phpcs.standard'));
        }
        $args = array(
            '-p',
            'extensions' => 'php',
            'standard' => $standard,
            $this->options->ignore->phpcs(),
            $this->options->analyzedDir
        );
        if ($this->options->isSavedToFiles) {
            $args['report'] = 'checkstyle';
            $args['report-file'] = $this->options->toFile('checkstyle.xml');
        } else {
            $args['report'] = 'full';
        }
        return $args;
    }

    private function pdepend()
    {
        return array(
            'jdepend-xml' => $this->options->toFile('pdepend-jdepend.xml'),
            'summary-xml' => $this->options->toFile('pdepend-summary.xml'),
            'jdepend-chart' => $this->options->toFile('pdepend-jdepend.svg'),
            'overview-pyramid' => $this->options->toFile('pdepend-pyramid.svg'),
            $this->options->ignore->pdepend(),
            $this->options->analyzedDir
        );
    }

    private function phpmd()
    {
        $args = array(
            $this->options->analyzedDir,
            $this->options->isSavedToFiles ? 'xml' : 'text',
            escapePath($this->config->path('phpmd.standard')),
            'suffixes' => 'php',
            $this->options->ignore->phpmd()
        );
        if ($this->options->isSavedToFiles) {
            $args['reportfile'] = $this->options->toFile('phpmd.xml');
        }
        return $args;
    }

    private function phpmetrics()
    {
        $args = array(
            $this->options->analyzedDir,
            'extensions' => 'php',
            $this->options->ignore->phpmetrics()
        );
        if ($this->options->isSavedToFiles) {
            $htmlFile = $this->options->toFile('phpmetrics.html');
            $args['offline'] = '';
            $args['report-html'] = $htmlFile;
            $args['report-xml'] = $this->options->toFile('phpmetrics.xml');
            $this->usedTools['phpmetrics']['htmlReport'] = trim($htmlFile, '"');
        } else {
            $args['report-cli'] = '';
        }
        return $args;
    }

    private function buildReport()
    {
        foreach ($this->usedTools as $tool => $config) {
            if ($config['transformedXml']) {
                $htmlFile = $this->options->rawFile("{$tool}.html");
                xmlToHtml(
                    $this->options->rawFile($config['transformedXml']),
                    $this->config->path("report.{$tool}"),
                    $htmlFile
                );
                $this->usedTools[$tool]['htmlReport'] = $htmlFile;
            }
        }
        twigToHtml(
            'phpqa.html.twig',
            array('tools' => array_keys($this->usedTools)),
            $this->options->rawFile('phpqa.html')
        );
    }

    private function printSummary()
    {
        $this->getOutput()->writeln('');
        $table = new Table($this->getOutput());
        $table->setHeaders(array('Tool', 'HTML report'));
        foreach ($this->usedTools as $tool => $config) {
            $table->addRow(array("<comment>{$tool}</comment>", $config['htmlReport']));
        }
        $table->addRow(new TableSeparator());
        $table->addRow(array(
            '<comment>phpqa</comment>',
            $this->options->hasReport ? $this->options->rawFile("phpqa.html") : ''
        ));
        $table->render();
    }
}
