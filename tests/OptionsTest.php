<?php

namespace Edge\QA;

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
        'report' => false
    );

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
    }

    public function testShouldIgnorePdependInCliOutput()
    {
        $cliOutput = $this->overrideOptions(array('output' => 'cli'));
        assertThat($this->fileOutput->filterTools(array('pdepend' => '')), is(nonEmptyArray()));
        assertThat($cliOutput->filterTools(array('pdepend' => '')), is(emptyArray()));
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
}
