<?php

namespace Edge\QA;

use Edge\QA\Tools\Analyzer\Phpstan;

/** @SuppressWarnings(PHPMD.TooManyPublicMethods) */
class RunningToolTest extends \PHPUnit_Framework_TestCase
{
    private $errorsCountInXmlFile = 2;

    public function testBuildOptionWithDefinedSeparator()
    {
        $tool = new RunningTool('tool', ['optionSeparator' => ' ']);
        assertThat($tool->buildOption('option', ''), is('--option'));
        assertThat($tool->buildOption('option', 'value'), is('--option value'));
        assertThat($tool->buildOption('option', 0), is('--option 0'));
    }

    public function testMarkSuccessWhenXPathIsNotDefined()
    {
        $tool = new RunningTool('tool', ['errorsXPath' => null]);
        assertThat($tool->analyzeResult(), is([true, '']));
    }

    /** @dataProvider provideAllowedErrorsForNonexistentFile */
    public function testMarkFailureWhenXmlFileDoesNotExist($allowedErrors, $expectedIsOk)
    {
        $tool = new RunningTool('tool', [
            'xml' => ['non-existent.xml'],
            'errorsXPath' => '//errors/error',
            'allowedErrorsCount' => $allowedErrors,
        ]);
        list($isOk, $error) = $tool->analyzeResult();
        assertThat($isOk, is($expectedIsOk));
        assertThat($error, containsString('not found'));
    }

    public function provideAllowedErrorsForNonexistentFile()
    {
        return [
            'success when allowed errors are not defined' => [null, true],
            'success when errors count are defined' => [$this->errorsCountInXmlFile, false],
        ];
    }

    /** @dataProvider provideAllowedErrors */
    public function testCompareAllowedCountWithErrorsCountFromXml($allowedErrors, $isOk)
    {
        $tool = new RunningTool('tool', [
            'xml' => ['tests/Error/errors.xml'],
            'errorsXPath' => '//errors/error',
            'allowedErrorsCount' => $allowedErrors
        ]);
        assertThat($tool->analyzeResult(), is([$isOk, $this->errorsCountInXmlFile]));
    }

    public function provideAllowedErrors()
    {
        return [
            'success when allowed errors are not defined' => [null, true],
            'success when errors count <= allowed count' => [$this->errorsCountInXmlFile, true],
            'failure when errors count > allowed count' => [$this->errorsCountInXmlFile - 1, false],
        ];
    }

    public function testRuntimeSelectionOfErrorXpath()
    {
        $tool = new RunningTool('tool', [
            'xml' => ['tests/Error/errors.xml'],
            'errorsXPath' => [
                false => '//errors/error',
                true => '//errors/error[@severity="error"]',
            ],
            'allowedErrorsCount' => 0,
        ]);
        $tool->errorsType = true;
        assertThat($tool->analyzeResult(), is([false, 1]));
    }

    /** @dataProvider provideMultipleXpaths */
    public function testMultipleXpaths(array $xpaths, array $expectedResult)
    {
        $tool = new RunningTool('tool', [
            'xml' => ['tests/Error/errors.xml'],
            'errorsXPath' => [
                null => $xpaths,
            ],
            'allowedErrorsCount' => 3,
        ]);
        assertThat($tool->analyzeResult(), is($expectedResult));
    }

    public function provideMultipleXpaths()
    {
        return [
            'multiple elements' => [['//errors/error', '//errors/warning'], [true, 2 + 1]],
            'invalid xpath' => [[null], [false, 'SimpleXMLElement::xpath(): Invalid expression']],
        ];
    }

    /** @dataProvider provideProcess */
    public function testAnalyzeExitCodeInCliMode($allowedErrors, $exitCode, array $expectedResult)
    {
        if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
            $this->markTestSkipped('Skipped on PHP 7.2');
        }
        $tool = new RunningTool('tool', [
            'allowedErrorsCount' => $allowedErrors
        ]);
        $tool->process = $this->prophesize('Symfony\Component\Process\Process')
            ->getExitCode()->willReturn($exitCode)
            ->getObjectProphecy()->reveal();
        assertThat($tool->analyzeResult(true), is($expectedResult));
    }

    public function provideProcess()
    {
        return [
            'success when exit code = 0' => [0, 0, [true, 0]],
            'success when exit code <= allowed code' => [1, 1, [true, 1]],
            'failure when errors count > allowed count but errors count is always one' => [0, 2, [false, 1]],
        ];
    }

    public function testCreateUniqueIdForUserReport()
    {
        $tool = new RunningTool('phpcs', []);
        $tool->userReports['dir/path.php'] = 'My report';
        $report = $tool->getHtmlRootReports()[0];
        assertThat($report['id'], is('phpcs-dir-path-php'));
    }

    public function testDynamicOutputModePhpstan()
    {
        $tool = new RunningTool('phpstan', [
            'outputMode' => null,
        ]);
        Phpstan::$SETTINGS['outputMode'] = OutputMode::RAW_CONSOLE_OUTPUT;
        assertThat($tool->hasOutput(OutputMode::RAW_CONSOLE_OUTPUT), is(true));
    }
}
