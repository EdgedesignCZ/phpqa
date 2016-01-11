<?php

namespace Edge\QA;

use DOMDocument;
use XSLTProcessor;

function xmlToHtml($input, $style, $outputFile)
{
    $xml = new DOMDocument();
    $xml->load($input);
    $xsl = new DOMDocument();
    $xsl->load($style);

    $xslt = new XSLTProcessor();
    $xslt->importStylesheet($xsl);
    $xslt->transformToDoc($xml)->saveHTMLFile($outputFile);
}
