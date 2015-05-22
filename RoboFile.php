<?php

class RoboFile extends \Robo\Tasks
{
    private $bergmanExclude = '--exclude=CI --exclude=bin --exclude=vendor';
    private $phpcsIgnore = '--ignore=*/CI/*,*/bin/*,*/vendor/*,RoboFile.php,error-handling.php';
    private $pdependIgnore = '--ignore=/CI/,/bin/,/vendor/';
    private $phpmdExclude = '--exclude /CI/,/bin/,/vendor/';
    private $phpmetricsExclude = '--excluded-dirs="CI|bin|vendor"';

    private $buildDir;
    private $analyzedDir;

    /**
     * @description Executes QA tools
     * @param string $analyzedDir path to analyzed directory
     * @param string $buildDir path to output directory
     */
    public function ci($analyzedDir = './', $buildDir = 'build/')
    {
        $this->analyzedDir = $analyzedDir;
        $this->buildDir = $buildDir;
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
            "{$this->analyzedDir} --progress {$this->bergmanExclude} --log-xml={$this->buildDir}/phploc.xml"
        );
    }

    private function ciPhpcpd()
    {
        $this->runCI(
            'phpcpd',
            "{$this->analyzedDir} --progress {$this->bergmanExclude} --log-pmd={$this->buildDir}/phpcpd.xml"
        );
    }

    private function ciPhpcs()
    {
        $this->runCI(
            'phpcs',
            "{$this->analyzedDir} -p --standard=PSR2 --extensions=php {$this->phpcsIgnore} "
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
            . " {$this->pdependIgnore} {$this->analyzedDir}"
        );
    }

    private function ciPhpmd()
    {
        $this->runCI(
            'phpmd',
            "{$this->analyzedDir} xml config/phpmd.xml {$this->phpmdExclude} --reportfile {$this->buildDir}/phpmd.xml"
        );
    }

    private function ciPhpmetrics()
    {
        $this->runCI(
            'phpmetrics',
            "{$this->analyzedDir} --extensions=php --excluded-dirs={$this->phpmetricsExclude}"
            . " --report-html={$this->buildDir}/phpmetrics.html"
        );
    }

    private function runCI($tool, $arguments)
    {
        $this->_exec("bin/{$tool} {$arguments}");
    }
}