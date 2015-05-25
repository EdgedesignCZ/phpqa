<?php

namespace Edge\QA;

trait CodeAnalysisTasks
{
    private $buildDir;
    private $analyzedDir;
    private $ignore;

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
        $this->ciClean();
        $tools = array(
            'phploc' => $this->ciPhploc(),
            'phpcpd' => $this->ciPhpcpd(),
            'phpcs' => $this->ciPhpcs(),
            'pdepend' => $this->ciPdepend(),
            'phpmd' => $this->ciPhpmd(),
            'phpmetrics' => $this->ciPhpmetrics(),
        );
        $this->parallelRun($tools, $opts['tools']);
    }

    private function ciClean()
    {
        if (is_dir($this->buildDir)) {
            $this->_cleanDir($this->buildDir);
        }
        $this->_mkdir($this->buildDir);
    }

    private function ciPhploc()
    {
        return $this->task('phploc')
            ->option('progress')
            ->option('log-xml', $this->toFile('phploc.xml'))
            ->arg($this->ignore->bergmann())
            ->arg($this->analyzedDir);
    }

    private function ciPhpcpd()
    {
        return $this->task('phpcpd')
            ->option('progress')
            ->option('log-pmd', $this->toFile('phpcpd.xml'))
            ->arg($this->ignore->bergmann())
            ->arg($this->analyzedDir);
    }

    private function ciPhpcs()
    {
        return $this->task('phpcs')
            ->arg('-p')
            ->arg('--extensions=php')
            ->arg('--standard=PSR2')
            ->arg('--report=checkstyle')
            ->arg("--report-file={$this->toFile('checkstyle.xml')}")
            ->arg($this->ignore->phpcs())
            ->arg($this->analyzedDir);
    }

    private function ciPdepend()
    {
        return $this->task('pdepend')
            ->arg("--jdepend-xml={$this->toFile('pdepend-jdepend.xml')}")
            ->arg("--summary-xml={$this->toFile('pdepend-summary.xml')}")
            ->arg("--jdepend-chart={$this->toFile('pdepend-jdepend.svg')}")
            ->arg("--overview-pyramid={$this->toFile('pdepend-pyramid.svg')}")
            ->arg($this->ignore->pdepend())
            ->arg($this->analyzedDir);
    }

    private function ciPhpmd()
    {
        return $this->task('phpmd')
            ->arg($this->analyzedDir)
            ->arg('xml')
            ->arg($this->qaFile('app/phpmd.xml'))
            ->option('sufixxes', 'php')
            ->option('reportfile', $this->toFile('phpmd.xml'))
            ->arg($this->ignore->phpmd());
    }

    private function ciPhpmetrics()
    {
        return $this->task('phpmetrics')
            ->arg($this->analyzedDir)
            ->option('extensions', 'php')
            ->option('report-html', $this->toFile('phpmetrics.html'))
            ->arg($this->ignore->phpmetrics());
    }

    private function toFile($file)
    {
        return "\"{$this->buildDir}/{$file}\"";
    }

    private function task($tool)
    {
        return $this->taskExec($this->qaFile("vendor/bin/{$tool}"));
    }

    private function qaFile($file)
    {
        return __DIR__ . "/../{$file}";
    }

    private function parallelRun($tools, $allowedToolsInCsv)
    {
        $allowedTools = explode(',', $allowedToolsInCsv);
        $parallel = $this->taskParallelExec();
        foreach ($tools as $tool => $process) {
            if (in_array($tool, $allowedTools)) {
                $parallel->process($process);
            }
        }
        $parallel->printed()->run();
    }
}