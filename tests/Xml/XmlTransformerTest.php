<?php

namespace Edge\QA;

class XmlTransformerTest extends \PHPUnit_Framework_TestCase
{
    private $output;

    public function setUp()
    {
        $this->output = __DIR__ . "/result.html";
    }

    public function testShouldConvertXmlToHtml()
    {
        xmlToHtml(
            __DIR__ . "/phploc.xml",
            __DIR__ . "/../../app/report/phploc.xsl",
            $this->output
        );
        assertThat(file_get_contents($this->output), containsString('</table>'));
    }

    public function tearDown()
    {
        unlink($this->output);
    }
}
