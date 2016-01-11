<?php

namespace Edge\QA;

trait CodeAnalysisTasks
{
    /** @var array [tool => optionSeparator] */
    private $tools = array(
        'phploc' => ' ',
        'phpcpd' => ' ',
        'phpcs' => '=',
        'pdepend' => '=',
        'phpmd' => ' ',
        'phpmetrics' => ' '
    );
    /** @var array [tool => xml] */
    private $xmlFiles = array(
        'phploc' => 'phploc.xml',
        'phpcpd' => 'phpcpd.xml',
        'phpcs' => 'checkstyle.xml',
        'pdepend' => 'pdepend-jdepend.xml',
        'phpmd' => 'phpmd.xml'
    );
    /** @var Options */
    private $options;
    /** @var array */
    private $usedTools;

    /**
     * @description Current versions
     */
    public function tools()
    {
        foreach (array_keys($this->tools) as $tool) {
            $this->_exec($this->binary("{$tool} --version"));
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
            'report' => false,
        )
    ) {
        $this->loadOptions($opts);
        $this->ciClean();
        $this->parallelRun();
        if ($this->options->hasReport) {
            $this->buildReport();
        }
    }

    private function loadOptions(array $opts)
    {
        $this->options = new Options($opts);
        $this->usedTools = $this->options->filterTools($this->tools);
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

    private function parallelRun()
    {
        $parallel = $this->taskParallelExec();
        foreach ($this->usedTools as $tool => $optionSeparator) {
            $process = $this->toolToProcess($tool, $optionSeparator);
            $parallel->process($process);
        }
        $parallel->printed($this->options->isOutputPrinted)->run();
    }

    private function toolToProcess($tool, $optionSeparator)
    {
        $binary = $this->binary($tool);
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
            $this->options->analyzedDir
        );
        if ($this->options->isSavedToFiles) {
            $args['log-pmd'] = $this->options->toFile('phpcpd.xml');
        }
        return $args;
    }

    private function phpcs()
    {
        $args = array(
            '-p',
            'extensions' => 'php',
            'standard' => 'PSR2',
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
            $this->options->appFile('phpmd.xml'),
            'sufixxes' => 'php',
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
            $args['report-html'] = $this->options->toFile('phpmetrics.html');
        } else {
            $args['report-cli'] = '';
        }
        return $args;
    }

    private function buildReport()
    {
        $this->say('<info>Build HTML report</info>');
        $xsl = __DIR__ . "/../app/report/";
        $tools = array();
        if (array_key_exists('phpmetrics', $this->usedTools)) {
            $tools[] = 'phpmetrics';
        }
        foreach (array_keys($this->usedTools) as $tool) {
            if (array_key_exists($tool, $this->xmlFiles)) {
                $tools[] = $tool;
                xmlToHtml(
                    "{$this->options->buildDir}/{$this->xmlFiles[$tool]}",
                    "{$xsl}/{$tool}.xsl",
                    "{$this->options->buildDir}/{$tool}.html"
                );
                if ($this->options->isOutputPrinted) {
                    $this->say("<comment>{$this->options->buildDir}/{$tool}.html</comment>");
                }
            }
        }
        twigToHtml(
            'phpqa.html.twig',
            array('tools' => $tools),
            "{$this->options->buildDir}/phpqa.html"
        );
        if ($this->options->isOutputPrinted) {
            $this->say("<info>{$this->options->buildDir}/phpqa.html</info>");
        }
    }

    private function binary($tool)
    {
        return COMPOSER_BINARY_DIR . $tool;
    }
}
