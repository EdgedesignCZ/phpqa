<?php

namespace Edge\QA;

trait CodeAnalysisTasks
{
    /** @var array [tool => optionSeparator] */
    private $tools = array(
        'phpmetrics' => array(
            'optionSeparator' => ' ',
            'composer' => 'phpmetrics/phpmetrics',
        ),
        'phploc' => array(
            'optionSeparator' => ' ',
            'xml' => ['phploc.xml'],
            'composer' => 'phploc/phploc',
        ),
        'phpcs' => array(
            'optionSeparator' => '=',
            'xml' => ['checkstyle.xml'],
            'errorsXPath' => '//checkstyle/file/error',
            'composer' => 'squizlabs/php_codesniffer',
        ),
        'phpmd' => array(
            'optionSeparator' => ' ',
            'xml' => ['phpmd.xml'],
            'errorsXPath' => '//pmd/file/violation',
            'composer' => 'phpmd/phpmd',
        ),
        'pdepend' => array(
            'optionSeparator' => '=',
            'xml' => ['pdepend-jdepend.xml', 'pdepend-summary.xml', 'pdepend-dependencies.xml'],
            'composer' => 'pdepend/pdepend',
        ),
        'phpcpd' => array(
            'optionSeparator' => ' ',
            'xml' => ['phpcpd.xml'],
            'errorsXPath' => '//pmd-cpd/duplication',
            'composer' => 'sebastian/phpcpd',
        ),
        'parallel-lint' => array(
            'optionSeparator' => ' ',
            'internalClass' => 'JakubOnderka\PhpParallelLint\ParallelLint',
            'hasOnlyConsoleOutput' => true,
            'composer' => 'jakub-onderka/php-parallel-lint',
        ),
        'phpstan' => array(
            'optionSeparator' => ' ',
            'internalClass' => 'PHPStan\Analyser\Analyser',
            'hasOnlyConsoleOutput' => true,
            'composer' => 'phpstan/phpstan',
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
        $tools = new Task\ToolVersions($this->getOutput());
        $tools($this->tools);
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
        if (!$opts['analyzedDirs']) {
            $opts['analyzedDirs'] = $opts['analyzedDir'] ?: './';
            if ($opts['analyzedDir']) {
                $this->yell("Option --analyzedDir is deprecated, please use option --analyzedDirs");
            }
        }

        $this->options = new Options($opts);
        $this->usedTools = $this->options->buildRunningTools($this->tools);
        $this->config = new Config();
        $this->config->loadCustomConfig($this->options->configDir, $opts['config']);
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
        $group = $this->options->isParallel ? new Task\ParallelExec() : new Task\NonParallelExec();
        foreach ($this->usedTools as $tool) {
            $exec = $this->toolToExec($tool);
            $tool->process = $group->process($exec);
        }
        $group->printed($this->options->isOutputPrinted)->run();
    }

    /** @return \Robo\Task\Base\Exec */
    private function toolToExec(RunningTool $tool)
    {
        $binary = pathToBinary($tool);
        $process = $this->taskExec($binary);
        $method = str_replace('-', '', $tool);
        foreach ($this->{$method}($tool) as $arg => $value) {
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
            $this->options->getAnalyzedDirs(' '),
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
            $this->options->getAnalyzedDirs(' '),
            'min-lines' => $this->config->value('phpcpd.minLines'),
            'min-tokens' => $this->config->value('phpcpd.minTokens'),
        );
        if ($this->options->isSavedToFiles) {
            $args['log-pmd'] = $tool->getEscapedXmlFile();
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
            $this->options->getAnalyzedDirs(' '),
        );
        if ($this->options->isSavedToFiles) {
            $args['report'] = 'checkstyle';
            $args['report-file'] = $tool->getEscapedXmlFile();
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
            'dependency-xml' => $this->options->toFile('pdepend-dependencies.xml'),
            'jdepend-chart' => $this->options->toFile('pdepend-jdepend.svg'),
            'overview-pyramid' => $this->options->toFile('pdepend-pyramid.svg'),
            $this->options->ignore->pdepend(),
            $this->options->getAnalyzedDirs(','),
        );
    }

    private function phpmd(RunningTool $tool)
    {
        $args = array(
            $this->options->getAnalyzedDirs(','),
            $this->options->isSavedToFiles ? 'xml' : 'text',
            escapePath($this->config->path('phpmd.standard')),
            'suffixes' => 'php',
            $this->options->ignore->phpmd()
        );
        if ($this->options->isSavedToFiles) {
            $args['reportfile'] = $tool->getEscapedXmlFile();
        }
        return $args;
    }

    private function phpmetrics(RunningTool $tool)
    {
        $analyzedDirs = $this->options->getAnalyzedDirs();
        $analyzedDir = reset($analyzedDirs);
        if (count($analyzedDirs) > 1) {
            $this->say("<error>phpmetrics analyzes only first directory {$analyzedDir}</error>");
        }
        $args = array(
            $analyzedDir,
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

    private function parallellint()
    {
        return array(
            $this->options->ignore->parallelLint(),
            $this->options->getAnalyzedDirs(' '),
        );
    }

    private function phpstan()
    {
        $createAbsolutePaths = function (array $relativeDirs) {
            return array_values(array_filter(array_map(
                function ($relativeDir) {
                    return '%currentWorkingDirectory%/' . trim($relativeDir, '"');
                },
                $relativeDirs
            )));
        };

        $defaultConfig = $this->config->path('phpstan.standard') ?: (getcwd() . '/phpstan.neon');
        if (file_exists($defaultConfig)) {
            $params = \Nette\Neon\Neon::decode(file_get_contents($defaultConfig))['parameters'] + [
                'excludes_analyse' => []
            ];
        } else {
            $params = [
                'autoload_directories' => $createAbsolutePaths($this->options->getAnalyzedDirs()),
                'excludes_analyse' => [],
            ];
        }

        $params['excludes_analyse'] = array_merge(
            $params['excludes_analyse'],
            $createAbsolutePaths($this->options->ignore->phpstan())
        );

        $neonDir = $this->options->isSavedToFiles ? $this->options->rawFile('') : getcwd();
        $neonFile = "{$neonDir}/phpstan-phpqa.neon";
        file_put_contents(
            $neonFile,
            "# Configuration generated in phpqa\n" .
            \Nette\Neon\Neon::encode(['parameters' => $params])
        );

        return array(
            'analyze',
            'ansi' => '',
            'level' => $this->config->value('phpstan.level'),
            'configuration' => $neonFile,
            $this->options->getAnalyzedDirs(' '),
        );
    }

    private function buildHtmlReport()
    {
        foreach ($this->usedTools as $tool) {
            $tool->htmlReport = $this->options->rawFile("{$tool}.html");
            if ($tool->hasOnlyConsoleOutput) {
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
            array('tools' => array_keys($this->usedTools), 'appVersion' => PHPQA_VERSION),
            $this->options->rawFile('phpqa.html')
        );
    }

    private function buildSummary()
    {
        $summary = new Task\TableSummary($this->options, $this->getOutput());
        return $summary($this->usedTools);
    }
}
