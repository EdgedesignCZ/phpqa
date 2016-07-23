<?php

namespace Edge\QA;

class RunningToolTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildOptionWithDefinedSeparator()
    {
        $tool = new RunningTool('tool', ['optionSeparator' => ' ']);
        assertThat($tool->buildOption('option', ''), is('--option'));
        assertThat($tool->buildOption('option', 'value'), is('--option value'));
    }

    public function testMarkSuccessWhenAllowedErrorsAreNotDefined()
    {
        $tool = new RunningTool('tool', ['allowedErrorsCount' => null]);
        assertThat($tool->getAllowedErrorsCount(), is(nullValue()));
        assertThat($tool->analyzeResult(), is([true, '']));
    }

    public function testMarkFailureWhenXmlFileDoesNotExist()
    {
        $tool = new RunningTool('tool', [
            'transformedXml' => 'non-existent.xml',
            'allowedErrorsCount' => 0
        ]);
        assertThat($tool->analyzeResult(), is([false, 0]));
    }

    /** @dataProvider provideAllowedErrors */
    public function testCompareErrorsInXmlWithAllowedCount($allowedErrors, $isOk)
    {
        $tool = new RunningTool('tool', [
            'transformedXml' => 'tests/Error/errors.xml',
            'errorsXPath' => '//errors/error',
            'allowedErrorsCount' => $allowedErrors
        ]);
        assertThat($tool->analyzeResult(), is([$isOk, 2]));
    }

    public function provideAllowedErrors()
    {
        return [
            [1, false],
            [2, true],
        ];
    }
}
