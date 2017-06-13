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
        'phpmetrics2' => array(
            'optionSeparator' => '=',
            'composer' => 'phpmetrics/phpmetrics',
            'binary' => 'phpmetrics',
        ),
        'phploc' => array(
            'optionSeparator' => ' ',
            'xml' => ['phploc.xml'],
            'composer' => 'phploc/phploc',
        ),
        'phpcs' => array(
            'optionSeparator' => '=',
            'xml' => ['checkstyle.xml'],
            'errorsXPath' => [
                # ignoreWarnings => xpath
                false => '//checkstyle/file/error',
                true => '//checkstyle/file/error[@severity="error"]',
            ],
            'composer' => 'squizlabs/php_codesniffer',
        ),
        'phpcs3' => array(
            'optionSeparator' => '=',
            'xml' => ['checkstyle.xml'],
            'errorsXPath' => [
                # ignoreWarnings => xpath
                false => '//checkstyle/file/error',
                true => '//checkstyle/file/error[@severity="error"]',
            ],
            'composer' => 'squizlabs/php_codesniffer',
            'binary' => 'phpcs',
        ),
        'php-cs-fixer' => array(
            'optionSeparator' => ' ',
            'internalClass' => 'PhpCsFixer\Config',
            'outputMode' => OutputMode::XML_CONSOLE_OUTPUT,
            'composer' => 'friendsofphp/php-cs-fixer',
            'xml' => ['php-cs-fixer.xml'],
            'errorsXPath' => '//testsuites/testsuite/testcase/failure',
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
            'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT,
            'composer' => 'jakub-onderka/php-parallel-lint',
        ),
        'phpstan' => array(
            'optionSeparator' => ' ',
            'internalClass' => 'PHPStan\Analyser\Analyser',
            'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT,
            'composer' => 'phpstan/phpstan',
        ),
    );
    /** @var array [tool => oldVersion] */
    private $toolsWithDifferentVersions = array(
        'phpmetrics2' => array(
            'tool' => 'phpmetrics',
            'internalClass' => 'Hal\Application\Command\RunMetricsCommand',
        ),
        'phpcs3' => array(
            'tool' => 'phpcs',
            'internalClass' => 'PHP_CodeSniffer',
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
        $tools(array_diff_key($this->tools, $this->toolsWithDifferentVersions));
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
        $opts['tools'] = $this->selectToolsThatAreInstalled($opts['tools']);

        $this->options = new Options($opts);
        $this->usedTools = $this->options->buildRunningTools($this->tools);
        $this->config = new Config();
        $this->config->loadCustomConfig($this->options->configDir, $opts['config']);
    }

    private function selectToolsThatAreInstalled($tools)
    {
        foreach ($this->toolsWithDifferentVersions as $newTool => $legacyTool) {
            if (!class_exists($legacyTool['internalClass'])) {
                $tools = str_replace($legacyTool['tool'], $newTool, $tools);
            }
        }
        return $tools;
    }

    private function ciClean()
    {
        if ($this->options->isSavedToFiles) {
            if (is_dir($this->options->buildDir)) {
                $this->_cleanDir($this->options->buildDir);
            } else {
                $this->_mkdir($this->options->buildDir);
            }
        }
    }

    private function runTools()
    {
        $group = $this->taskPhpqaRunner($this->options->isParallel);
        foreach ($this->usedTools as $tool) {
            $exec = $this->toolToExec($tool);
            $tool->process = $group->process($exec);
        }
        $group->printed($this->options->isOutputPrinted)->run();
    }

    /** @return \Robo\Task\Base\Exec */
    private function toolToExec(RunningTool $tool)
    {
        $binary = pathToBinary($tool->binary);
        $process = $this->taskExec($binary);
        $method = str_replace('-', '', $tool);
        foreach ($this->{$method}($tool) as $arg => $value) {
            if (is_int($arg)) {
                $this->addArgToExec($process, $value);
            } else {
                $this->addArgToExec($process, $tool->buildOption($arg, $value));
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
        return $this->buildPhpcs($tool, \PHP_CodeSniffer::getInstalledStandards());
    }

    private function phpcs3(RunningTool $tool)
    {
        require_once COMPOSER_VENDOR_DIR . '/squizlabs/php_codesniffer/autoload.php';
        return $this->buildPhpcs($tool, \PHP_CodeSniffer\Util\Standards::getInstalledStandards());
    }

    private function buildPhpcs(RunningTool $tool, array $installedStandards)
    {
        $tool->errorsType = $this->config->value('phpcs.ignoreWarnings') === true;
        $standard = $this->config->value('phpcs.standard');
        if (!in_array($standard, $installedStandards)) {
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
            $reports = ['checkstyle' => 'checkstyle.xml'] + $this->config->value('phpcs.reports.file');
            foreach ($reports as $report => $file) {
                $args["report-{$report}"] = $this->options->toFile($file);
                if ($report != 'checkstyle') {
                    $tool->userReports[$report] = $this->options->rawFile($file);
                }
            }
        } else {
            foreach ($this->config->value('phpcs.reports.cli') as $report) {
                $args["report-{$report}"] = '';
            }
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

    private function phpmetrics2(RunningTool $tool)
    {
        $args = array(
            $this->options->ignore->phpmetrics2(),
            'extensions' => 'php',
        );
        if ($this->options->isSavedToFiles) {
            $tool->htmlReport = $this->options->rawFile('phpmetrics/index.html');
            $args['report-html'] = $this->options->toFile('phpmetrics/');
            $args['report-violations'] = $this->options->toFile('phpmetrics.xml');
        }
        $args[] = $this->options->getAnalyzedDirs(',');
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

    private function phpcsfixer()
    {
        $configFile = $this->config->value('php-cs-fixer.config');
        if ($configFile) {
            $analyzedDir = $this->options->getAnalyzedDirs(' ');
        } else {
            $analyzedDirs = $this->options->getAnalyzedDirs();
            $analyzedDir = reset($analyzedDirs);
            if (count($analyzedDirs) > 1) {
                $this->say("<error>php-cs-fixer analyzes only first directory {$analyzedDir}</error>");
                $this->say(
                    "- <info>multiple dirs are supported if you specify " .
                    "<comment>php-cs-fixer.config</comment> in <comment>.phpqa.yml</comment></info>"
                );
            }
        }
        $args = [
            'fix',
            $analyzedDir,
            'verbose' => '',
            'format' => $this->options->isSavedToFiles ? 'junit' : 'txt',
        ];
        if ($configFile) {
            $args['config'] = $configFile;
        } else {
            $args += [
                'rules' => $this->config->value('php-cs-fixer.rules'),
                'allow-risky' => $this->config->value('php-cs-fixer.allowRiskyRules') ? 'yes' : 'no',
            ];
        }
        if ($this->config->value('php-cs-fixer.isDryRun')) {
            $args['dry-run'] = '';
        }
        return $args;
    }

    private function buildHtmlReport()
    {
        foreach ($this->usedTools as $tool) {
            if (!$tool->htmlReport) {
                $tool->htmlReport = $this->options->rawFile("{$tool->binary}.html");
            }
            if ($tool->hasOutput(OutputMode::XML_CONSOLE_OUTPUT)) {
                file_put_contents($this->options->rawFile("{$tool}.xml"), $tool->process->getOutput());
            }

            if ($tool->hasOutput(OutputMode::RAW_CONSOLE_OUTPUT)) {
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
                    $this->config->path("report.{$tool->binary}"),
                    $tool->htmlReport,
                    ['root-directory' => $this->options->getCommonRootPath()]
                );
            }
        }
        twigToHtml(
            'phpqa.html.twig',
            array(
                'tools' => $this->usedTools,
                'appVersion' => PHPQA_VERSION,
                'buildDir' => $this->options->rawFile('')
            ),
            $this->options->rawFile('phpqa.html')
        );
    }

    private function buildSummary()
    {
        $summary = new Task\TableSummary($this->options, $this->getOutput());
        return $summary($this->usedTools);
    }
}
