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
        'verbose' => true
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
        assertThat($this->fileOutput->appFile('file'), is(nonEmptyString()));
    }

    public function testShouldIgnorePdependInCliOutput()
    {
        $cliOutput = $this->overrideOptions(array('output' => 'cli'));
        assertThat($this->fileOutput->isToolAllowed('pdepend'), is(true));
        assertThat($cliOutput->isToolAllowed('pdepend'), is(false));
    }

    /** @dataProvider provideOutputs */
    public function testShouldBuildOutput(array $opts, $isSavedToFiles, $isOutputPrinted)
    {
        $options = $this->overrideOptions($opts);
        assertThat($options->isSavedToFiles, is($isSavedToFiles));
        assertThat($options->isOutputPrinted, is($isOutputPrinted));
    }

    public function provideOutputs()
    {
        return array(
            'ignore verbose in CLI output' => array(
                array('output' => 'cli', 'verbose' => false),
                false,
                true
            ),
            'respect verbose mode in FILE output' => array(
                array('output' => 'file', 'verbose' => false),
                true,
                false
            )
        );
    }
}
