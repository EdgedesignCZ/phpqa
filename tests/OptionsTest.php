<?php

namespace Edge\QA;

/** @SuppressWarnings(PHPMD.TooManyPublicMethods) */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
    // copy-pasted options from CodeAnalysisTasks
    private $defaultOptions = array(
        'analyzedDirs' => './',
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

    public function testEscapePaths()
    {
        assertThat($this->fileOutput->getAnalyzedDirs(','), is('"./"'));
        assertThat($this->fileOutput->getAnalyzedDirs(), is(['"./"']));
        assertThat($this->fileOutput->toFile('file'), is('"build//file"'));
        assertThat($this->fileOutput->rawFile('file'), is('build//file'));
    }

    public function testIgnorePdependInCliOutput()
    {
        $cliOutput = $this->overrideOptions(array('output' => 'cli'));
        assertThat($this->buildRunningTools($this->fileOutput, array('pdepend' => [])), is(nonEmptyArray()));
        assertThat($this->buildRunningTools($cliOutput, array('pdepend' => [])), is(emptyArray()));
    }

    public function testIgnoreNotInstalledTool()
    {
        $tools = $this->buildRunningTools(
            $this->fileOutput,
            array('pdepend' => ['internalClass' => 'UnknownTool\UnknownClass'])
        );
        assertThat($tools['pdepend']->isExecutable, is(false));
    }

    /** @dataProvider provideOutputs */
    public function testBuildOutput(array $opts, $isSavedToFiles, $isOutputPrinted, $hasReport)
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
    public function testExecute(array $opts, $isParallel)
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
    public function testBuildRootPath($analyzedDirs, $expectedRoot)
    {
        $options = $this->overrideOptions(array('analyzedDirs' => $analyzedDirs));
        assertThat($options->getCommonRootPath(), is($expectedRoot));
    }

    public function provideAnalyzedDir()
    {
        return array(
            'current dir + analyzed dir + slash' => array('src', getcwd() . '/src/'),
            'find common root from multiple dirs' => array('src,tests', getcwd() . '/'),
            'no path when dir is invalid' => array('./non-existent-directory', ''),
        );
    }

    public function testLoadAllowedErrorsCount()
    {
        $options = $this->overrideOptions(array('tools' => 'phpcs:1,pdepend'));
        $tools = $this->buildRunningTools($options, array('phpcs' => [], 'pdepend' => []));
        assertThat($tools['phpcs']->getAllowedErrorsCount(), is(1));
        assertThat($tools['pdepend']->getAllowedErrorsCount(), is(nullValue()));
    }

    private function buildRunningTools(Options $o, array $tools)
    {
        return $o->buildRunningTools($tools);
    }
}
