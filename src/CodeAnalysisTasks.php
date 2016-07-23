<?php

namespace Edge\QA;

trait CodeAnalysisTasks
{
    /** @var array [tool => optionSeparator] */
    private $tools = array(
        'phpmetrics' => array(
            'optionSeparator' => ' ',
        ),
        'phploc' => array(
            'optionSeparator' => ' ',
            'transformedXml' => 'phploc.xml',
        ),
        'phpcs' => array(
            'optionSeparator' => '=',
            'transformedXml' => 'checkstyle.xml',
            'errorsXPath' => '//checkstyle/file/error',
        ),
        'phpmd' => array(
            'optionSeparator' => ' ',
            'transformedXml' => 'phpmd.xml',
            'errorsXPath' => '//pmd/file/violation',
        ),
        'pdepend' => array(
            'optionSeparator' => '=',
            'transformedXml' => 'pdepend-jdepend.xml',
        ),
        'phpcpd' => array(
            'optionSeparator' => ' ',
            'transformedXml' => 'phpcpd.xml',
            'errorsXPath' => '//pmd-cpd/duplication',
        ),
    );
    /** @var Options */
    private $options;
    /** @var Config */
    private $config;
    /** @var RunningTool[] */
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
            $this->buildHtmlReport();
        }
        return $this->buildSummary();
    }

    private function loadOptions(array $opts)
    {
        $this->options = new Options($opts);
        $this->usedTools = $this->options->buildRunningTools($this->tools);
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
        foreach ($this->usedTools as $tool) {
            $exec = $this->toolToExec($tool);
            $group->process($exec);
        }
        $group->printed($this->options->isOutputPrinted)->run();
    }

    /** @return \Robo\Task\Base\Exec */
    private function toolToExec(RunningTool $tool)
    {
        $binary = pathToBinary($tool);
        $process = $this->taskExec($binary);
        foreach ($this->{(string) $tool}($tool) as $arg => $value) {
            if (is_int($arg)) {
                $process->arg($value);
            } else {
                $process->arg($tool->buildOption($arg, $value));
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

    private function phpcpd(RunningTool $tool)
    {
        $args = array(
            'progress' => '',
            $this->options->ignore->bergmann(),
            $this->options->analyzedDir,
            'min-lines' => $this->config->value('phpcpd.minLines'),
            'min-tokens' => $this->config->value('phpcpd.minTokens'),
        );
        if ($this->options->isSavedToFiles) {
            $args['log-pmd'] = escapePath($tool->transformedXml);
        }
        return $args;
    }

    private function phpcs(RunningTool $tool)
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
            $args['report-file'] = escapePath($tool->transformedXml);
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

    private function phpmd(RunningTool $tool)
    {
        $args = array(
            $this->options->analyzedDir,
            $this->options->isSavedToFiles ? 'xml' : 'text',
            escapePath($this->config->path('phpmd.standard')),
            'suffixes' => 'php',
            $this->options->ignore->phpmd()
        );
        if ($this->options->isSavedToFiles) {
            $args['reportfile'] = escapePath($tool->transformedXml);
        }
        return $args;
    }

    private function phpmetrics(RunningTool $tool)
    {
        $args = array(
            $this->options->analyzedDir,
            'extensions' => 'php',
            $this->options->ignore->phpmetrics()
        );
        if ($this->options->isSavedToFiles) {
            $tool->htmlReport = $this->options->rawFile('phpmetrics.html');
            $args['offline'] = '';
            $args['report-html'] = escapePath($tool->htmlReport);
            $args['report-xml'] = $this->options->toFile('phpmetrics.xml');
        } else {
            $args['report-cli'] = '';
        }
        return $args;
    }

    private function buildHtmlReport()
    {
        foreach ($this->usedTools as $tool) {
            if ($tool->transformedXml) {
                $tool->htmlReport = $this->options->rawFile("{$tool}.html");
                xmlToHtml(
                    $tool->transformedXml,
                    $this->config->path("report.{$tool}"),
                    $tool->htmlReport
                );
            }
        }
        twigToHtml(
            'phpqa.html.twig',
            array('tools' => array_keys($this->usedTools)),
            $this->options->rawFile('phpqa.html')
        );
    }

    private function buildSummary()
    {
        if ($this->options->isSavedToFiles) {
            $summary = new Task\TableSummary($this->options, $this->getOutput());
            return $summary($this->usedTools);
        }
        return 0;
    }
}
