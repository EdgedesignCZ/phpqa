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
    private $isSavedToFiles;

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
     */
    public function ci(
        $opts = array(
            'analyzedDir' => './',
            'buildDir' => 'build/',
            'ignoredDirs' => 'vendor',
            'ignoredFiles' => '',
            'tools' => 'phploc,phpcpd,phpcs,pdepend,phpmd,phpmetrics',
            'output' => 'file',
        )
    ) {
        $this->analyzedDir = '"' . $opts['analyzedDir'] . '"';
        $this->buildDir = $opts['buildDir'];
        $this->ignore = new IgnoredPaths($opts['ignoredDirs'], $opts['ignoredFiles']);
        $this->isSavedToFiles = $opts['output'] == 'file';
        $isOutputPrinted = $this->isSavedToFiles ? $opts['verbose'] : true;
        $allowedTools = explode(',', $opts['tools']);
        $this->ciClean();
        $this->parallelRun($allowedTools, $isOutputPrinted);
    }

    private function ciClean()
    {
        if ($this->isSavedToFiles) {
            if (is_dir($this->buildDir)) {
                $this->_cleanDir($this->buildDir);
            }
            $this->_mkdir($this->buildDir);
        }
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
        $args = array(
            'progress' => '',
            $this->ignore->bergmann(),
            $this->analyzedDir
        );
        if ($this->isSavedToFiles) {
            $args['log-xml'] = $this->toFile('phploc.xml');
        }
        return $args;
    }

    private function phpcpd()
    {
        $args = array(
            'progress' => '',
            $this->ignore->bergmann(),
            $this->analyzedDir
        );
        if ($this->isSavedToFiles) {
            $args['log-pmd'] = $this->toFile('phpcpd.xml');
        }
        return $args;
    }

    private function phpcs()
    {
        $args = array(
            '-p',
            'extensions' => 'php',
            'standard' => 'PSR2',
            $this->ignore->phpcs(),
            $this->analyzedDir
        );
        if ($this->isSavedToFiles) {
            $args['report'] = 'checkstyle';
            $args['report-file'] = $this->toFile('checkstyle.xml');
        } else {
            $args['report'] = 'full';
        }
        return $args;
    }

    private function pdepend()
    {
        if (!$this->isSavedToFiles) {
            throw new \Exception('Pdepend has no CLI output');
        }
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
        $args = array(
            $this->analyzedDir,
            $this->isSavedToFiles ? 'xml' : 'text',
            $this->appFile('phpmd.xml'),
            'sufixxes' => 'php',
            $this->ignore->phpmd()
        );
        if ($this->isSavedToFiles) {
            $args['reportfile'] = $this->toFile('phpmd.xml');
        }
        return $args;
    }

    private function phpmetrics()
    {
        $args = array(
            $this->analyzedDir,
            'extensions' => 'php',
            $this->ignore->phpmetrics()
        );
        if ($this->isSavedToFiles) {
            $args['report-html'] = $this->toFile('phpmetrics.html');
        } else {
            $args['report-cli'] = '';
        }
        return $args;
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
