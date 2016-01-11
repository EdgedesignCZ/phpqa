<?php

namespace Edge\QA;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    private $output;
    private $phplocXsl;

    public function setUp()
    {
        $this->output = __DIR__ . "/result.html";
        $this->phplocXsl = __DIR__ . "/../../app/report/phploc.xsl";
    }

    public function testShouldConvertTwigToHtml()
    {
        twigToHtml("phpqa.html.twig", array('tools' => array()), $this->output);
        assertThat(file_get_contents($this->output), containsString('phpqa'));
    }

    /** @dataProvider provideXml */
    public function testShouldConvertXmlToHtml($xml, $expectedOutput)
    {
        xmlToHtml(__DIR__ . "/{$xml}", $this->phplocXsl, $this->output);
        assertThat(file_get_contents($this->output), containsString($expectedOutput));
    }

    public function provideXml()
    {
        return array(
            'create html' => array('phploc.xml', '</table>'),
            'create empty file if something went south' => array('invalid.xml', 'Premature end of data in tag phploc')
        );
    }

    public function tearDown()
    {
        unlink($this->output);
    }
}
