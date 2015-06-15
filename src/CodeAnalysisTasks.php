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
    private $buildDir;
    private $analyzedDir;
    private $ignore;

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
     */
    public function ci(
        $opts = array(
            'analyzedDir' => './',
            'buildDir' => 'build/',
            'ignoredDirs' => 'vendor',
            'ignoredFiles' => '',
            'tools' => 'phploc,phpcpd,phpcs,pdepend,phpmd,phpmetrics'
        )
    ) {
        $this->analyzedDir = '"' . $opts['analyzedDir'] . '"';
        $this->buildDir = $opts['buildDir'];
        $this->ignore = new IgnoredPaths($opts['ignoredDirs'], $opts['ignoredFiles']);
        $allowedTools = explode(',', $opts['tools']);
        $this->ciClean();
        $this->parallelRun($allowedTools, $opts['verbose']);
    }

    private function ciClean()
    {
        if (is_dir($this->buildDir)) {
            $this->_cleanDir($this->buildDir);
        }
        $this->_mkdir($this->buildDir);
    }

    private function parallelRun($allowedTools, $isOutputPrinted)
    {
        $parallel = $this->taskParallelExec();
        foreach ($this->tools as $tool => $optionSeparator) {
            if (in_array($tool, $allowedTools)) {
                $process = $this->toolToProcess($tool, $optionSeparator);
                $parallel->process($process);
            }
        }
        $parallel->printed($isOutputPrinted)->run();
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
        return array(
            'progress' => '',
            'log-xml' => $this->toFile('phploc.xml'),
            $this->ignore->bergmann(),
            $this->analyzedDir
        );
    }

    private function phpcpd()
    {
        return array(
            'progress' => '',
            'log-pmd' => $this->toFile('phpcpd.xml'),
            $this->ignore->bergmann(),
            $this->analyzedDir
        );
    }

    private function phpcs()
    {
        return array(
            '-p',
            'extensions' => 'php',
            'standard' => 'PSR2',
            'report' => 'checkstyle',
            'report-file' => $this->toFile('checkstyle.xml'),
            $this->ignore->phpcs(),
            $this->analyzedDir
        );
    }

    private function pdepend()
    {
        return array(
            'jdepend-xml' => $this->toFile('pdepend-jdepend.xml'),
            'summary-xml' => $this->toFile('pdepend-summary.xml'),
            'jdepend-chart' => $this->toFile('pdepend-jdepend.svg'),
            'overview-pyramid' => $this->toFile('pdepend-pyramid.svg'),
            $this->ignore->pdepend(),
            $this->analyzedDir
        );
    }

    private function phpmd()
    {
        return array(
            $this->analyzedDir,
            'xml',
            $this->appFile('phpmd.xml'),
            'sufixxes' => 'php',
            'reportfile' => $this->toFile('phpmd.xml'),
            $this->ignore->phpmd()
        );
    }

    private function phpmetrics()
    {
        return array(
            $this->analyzedDir,
            'extensions' => 'php',
            'report-html' => $this->toFile('phpmetrics.html'),
            $this->ignore->phpmetrics()
        );
    }

    private function toFile($file)
    {
        return "\"{$this->buildDir}/{$file}\"";
    }

    private function appFile($file)
    {
        return __DIR__ . "/../app/{$file}";
    }

    private function binary($tool)
    {
        return COMPOSER_BINARY_DIR . $tool;
    }
}
