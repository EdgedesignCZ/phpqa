<?php

namespace Edge\QA;

/** @SuppressWarnings(PHPMD.TooManyPublicMethods) */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
    // copy-pasted options from CodeAnalysisTasks
    private $defaultOptions = array(
        'analyzedDir' => './',
        'buildDir' => 'build/',
        'ignoredDirs' => 'vendor',
        'ignoredFiles' => '',
        'tools' => 'phploc,phpcpd,phpcs,pdepend,phpmd,phpmetrics',
        'output' => 'file',
        'config' => '',
        'verbose' => true,
        'report' => false,
        'execution' => 'parallel',
    );

    /** @var Options */
    private $fileOutput;

    public function setUp()
    {
        $this->fileOutput = $this->overrideOptions();
    }

    private function overrideOptions(array $options = array())
    {
        return new Options(array_merge($this->defaultOptions, $options));
    }

    public function testShouldEscapePaths()
    {
        assertThat($this->fileOutput->analyzedDir, is('"./"'));
        assertThat($this->fileOutput->toFile('file'), is('"build//file"'));
        assertThat($this->fileOutput->rawFile('file'), is('build//file'));
    }

    public function testShouldIgnorePdependInCliOutput()
    {
        $cliOutput = $this->overrideOptions(array('output' => 'cli'));
        assertThat($this->fileOutput->buildRunningTools(array('pdepend' => [])), is(nonEmptyArray()));
        assertThat($cliOutput->buildRunningTools(array('pdepend' => [])), is(emptyArray()));
    }

    /** @dataProvider provideConfig */
    public function testShouldLoadDirectoryWithCustomConfig($config, $expectedConfig)
    {
        $options = $this->overrideOptions(array('config' => $config));
        assertThat($options->configDir, is($expectedConfig));
    }

    public function provideConfig()
    {
        return array(
            'cwd when config is not defined' => array('', getcwd()),
            'use passed config (relative to cwd)' => array('path-to-config-directory', 'path-to-config-directory')
        );
    }

    /** @dataProvider provideOutputs */
    public function testShouldBuildOutput(array $opts, $isSavedToFiles, $isOutputPrinted, $hasReport)
    {
        $options = $this->overrideOptions($opts);
        assertThat($options->isSavedToFiles, is($isSavedToFiles));
        assertThat($options->isOutputPrinted, is($isOutputPrinted));
        assertThat($options->hasReport, is($hasReport));
    }

    public function provideOutputs()
    {
        return array(
            'ignore verbose and report in CLI output' => array(
                array('output' => 'cli', 'verbose' => false, 'report' => true),
                false,
                true,
                false
            ),
            'respect verbose mode and report in FILE output' => array(
                array('output' => 'file', 'verbose' => false, 'report' => true),
                true,
                false,
                true
            )
        );
    }

    /** @dataProvider provideExecutionMode */
    public function testShouldExecute(array $opts, $isParallel)
    {
        $options = $this->overrideOptions($opts);
        assertThat($options->isParallel, is($isParallel));
    }

    public function provideExecutionMode()
    {
        return array(
            'parallel executaion is default mode' => array(array(), true),
            'parallel executaion is default mode' => array(array('execution' => 'parallel'), true),
            'dont use parallelism if execution is other word' => array(array('execution' => 'single'), false),
        );
    }

    /** @dataProvider provideAnalyzedDir */
    public function testBuildRootPath($analyzedDir, $expectedRoot)
    {
        $options = $this->overrideOptions(array('analyzedDir' => $analyzedDir));
        assertThat($options->getCommonRootPath(), is($expectedRoot));
    }

    public function provideAnalyzedDir()
    {
        return array(
            'current dir + analyzed dir + slash' => array('./', getcwd() . '/'),
            'no path when dir is invalid' => array('./non-existent-directory', '')
        );
    }

    public function testLoadAllowedErrorsCount()
    {
        $options = $this->overrideOptions(array('tools' => 'phpcs:1,pdepend'));
        $tools = $options->buildRunningTools(array('phpcs' => [], 'pdepend' => []));
        assertThat($tools['phpcs']->getAllowedErrorsCount(), is(1));
        assertThat($tools['pdepend']->getAllowedErrorsCount(), is(nullValue()));
    }
}
