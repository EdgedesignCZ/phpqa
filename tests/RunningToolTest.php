<?php

namespace Edge\QA;

class RunningToolTest extends \PHPUnit_Framework_TestCase
{
    private $errorsCountInXmlFile = 2;

    public function testBuildOptionWithDefinedSeparator()
    {
        $tool = new RunningTool('tool', ['optionSeparator' => ' ']);
        assertThat($tool->buildOption('option', ''), is('--option'));
        assertThat($tool->buildOption('option', 'value'), is('--option value'));
    }

    public function testMarkSuccessWhenXPathIsNotDefined()
    {
        $tool = new RunningTool('tool', ['errorsXPath' => null]);
        assertThat($tool->analyzeResult(), is([true, '']));
    }

    public function testMarkFailureWhenXmlFileDoesNotExist()
    {
        $tool = new RunningTool('tool', [
            'transformedXml' => 'non-existent.xml',
            'errorsXPath' => '//errors/error',
        ]);
        assertThat($tool->analyzeResult(), is([false, 0]));
    }

    /** @dataProvider provideAllowedErrors */
    public function testCompareAllowedCountWithErrorsCountFromXml($allowedErrors, $isOk)
    {
        $tool = new RunningTool('tool', [
            'transformedXml' => 'tests/Error/errors.xml',
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
}
