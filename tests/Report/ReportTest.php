<?php

namespace Edge\QA;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    private $output;
    private $phplocXsl;
    private $xmlParams = ['bootstrap.min.css' => '','bootstrap.min.js' => '', 'jquery.min.js' => ''];

    public function setUp()
    {
        $this->output = __DIR__ . "/result.html";
        $this->phplocXsl = __DIR__ . "/../../app/report/phploc.xsl";
    }

    public function testConvertTwigToHtml()
    {
        twigToHtml("phpqa.html.twig", array('tools' => array()), $this->output);
        assertThat(file_get_contents($this->output), containsString('phpqa'));
    }

    /** @dataProvider provideXml */
    public function testConvertXmlToHtml($xml, $assertOutput)
    {
        xmlToHtml([__DIR__ . "/{$xml}"], $this->phplocXsl, $this->output, $this->xmlParams);
        assertThat(file_get_contents($this->output), $assertOutput);
    }

    public function provideXml()
    {
        return array(
            'create html' => array('phploc.xml', containsString('</table>')),
            'create empty file if something went south' => array('invalid.xml', not(containsString('</table>')))
        );
    }

    public function testIgnoreMissingXmlDocuments()
    {
        xmlToHtml([], $this->phplocXsl, $this->output, $this->xmlParams);
        assertThat(file_exists($this->output), is(false));
    }

    public function tearDown()
    {
        if (file_exists($this->output)) {
            unlink($this->output);
        }
    }
}
