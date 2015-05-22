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
        $this->buildDir = '"' . $opts['buildDir'] . '"';
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
        $this->runCI(
            'phploc',
            "{$this->analyzedDir} --progress {$this->ignore->bergman()} --log-xml={$this->buildDir}/phploc.xml"
        );
    }

    private function ciPhpcpd()
    {
        $this->runCI(
            'phpcpd',
            "{$this->analyzedDir} --progress {$this->ignore->bergman()} --log-pmd={$this->buildDir}/phpcpd.xml"
        );
    }

    private function ciPhpcs()
    {
        $this->runCI(
            'phpcs',
            "{$this->analyzedDir} -p --standard=PSR2 --extensions=php {$this->ignore->phpcs()} "
            . "--report=checkstyle --report-file={$this->buildDir}/checkstyle.xml"
        );
    }

    private function ciPdepend()
    {
        $this->runCI(
            'pdepend',
            "--summary-xml={$this->buildDir}/pdepend-summary.xml"
            . " --jdepend-chart={$this->buildDir}/pdepend-jdepend.svg"
            . " --overview-pyramid={$this->buildDir}/pdepend-pyramid.svg"
            . " {$this->ignore->pdepend()} {$this->analyzedDir}"
        );
    }

    private function ciPhpmd()
    {
        $this->runCI(
            'phpmd',
            "{$this->analyzedDir} xml config/phpmd.xml {$this->ignore->phpmd()}"
            . " --reportfile {$this->buildDir}/phpmd.xml"
        );
    }

    private function ciPhpmetrics()
    {
        $this->runCI(
            'phpmetrics',
            "{$this->analyzedDir} --extensions=php {$this->ignore->phpmetrics()}"
            . " --report-html={$this->buildDir}/phpmetrics.html"
        );
    }

    private function runCI($tool, $arguments)
    {
        $this->_exec("bin/{$tool} {$arguments}");
    }
}