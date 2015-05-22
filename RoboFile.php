<?php

class RoboFile extends \Robo\Tasks
{
    private $buildDir;
    private $analyzedDir;
    private $ignore;

    /**
     * @description Executes QA tools
     * @option string $analyzedDir path to analyzed directory
     * @option string $buildDir path to output directory
     * @option string $ignoredDirs csv
     * @option string $ignoredFiles csv
     */
    public function ci(
        $opts = array(
            'analyzedDir' => './',
            'buildDir' => 'build/',
            'ignoredDirs' => 'CI,bin,vendor',
            'ignoredFiles' => 'RoboFile.php'
        )
    ) {
        $this->analyzedDir = '"' . $opts['analyzedDir'] . '"';
        $this->buildDir = $opts['buildDir'];
        $this->ignore = new IgnoredPaths($opts['ignoredDirs'], $opts['ignoredFiles']);
        $this->ciClean();
        $this->ciPhploc();
        $this->ciPhpcpd();
        $this->ciPhpcs();
        $this->ciPdepend();
        $this->ciPhpmd();
        $this->ciPhpmetrics();
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
        $this->taskExec('bin/phploc')
            ->option('progress')
            ->option('log-xml', $this->toFile('phploc.xml'))
            ->arg($this->ignore->bergman())
            ->arg($this->analyzedDir)
            ->run();
    }

    private function ciPhpcpd()
    {
        $this->taskExec('bin/phpcpd')
            ->option('progress')
            ->option('log-pmd', $this->toFile('phpcpd.xml'))
            ->arg($this->ignore->bergman())
            ->arg($this->analyzedDir)
            ->run();
    }

    private function ciPhpcs()
    {
        $this->taskExec('bin/phpcs')
            ->arg('-p')
            ->arg('--extensions=php')
            ->arg('--standard=PSR2')
            ->arg('--report=checkstyle')
            ->arg("--report-file={$this->toFile('checkstyle.xml')}")
            ->arg($this->ignore->phpcs())
            ->arg($this->analyzedDir)
            ->run();
    }

    private function ciPdepend()
    {
        $this->taskExec('bin/pdepend')
            ->arg("--jdepend-xml={$this->toFile('pdepend-jdepend.xml')}")
            ->arg("--summary-xml={$this->toFile('pdepend-summary.xml')}")
            ->arg("--jdepend-chart={$this->toFile('pdepend-jdepend.svg')}")
            ->arg("--overview-pyramid={$this->toFile('pdepend-pyramid.svg')}")
            ->arg($this->ignore->pdepend())
            ->arg($this->analyzedDir)
            ->run();
    }

    private function ciPhpmd()
    {
        $this->taskExec('bin/phpmd')
            ->arg($this->analyzedDir)
            ->arg("xml config/phpmd.xml")
            ->option('reportfile', $this->toFile('phpmd.xml'))
            ->arg($this->ignore->phpmd())
            ->run();
    }

    private function ciPhpmetrics()
    {
        $this->taskExec('bin/phpmetrics')
            ->arg($this->analyzedDir)
            ->option('extensions', 'php')
            ->option('report-html', $this->toFile('phpmetrics.html'))
            ->arg($this->ignore->phpmetrics())
            ->run();
    }

    private function toFile($file)
    {
        return "'{$this->buildDir}/{$file}'";
    }
}