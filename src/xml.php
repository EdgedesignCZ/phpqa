<?php

namespace Edge\QA;

use DOMDocument;
use XSLTProcessor;
use Exception;

function xmlToHtml($input, $style, $outputFile)
{
    convertPhpErrorsToExceptions();
    try {
        $xml = new DOMDocument();
        $xml->load($input);
        $xsl = new DOMDocument();
        $xsl->load($style);

        $xslt = new XSLTProcessor();
        $xslt->importStylesheet($xsl);
        $xslt->transformToDoc($xml)->saveHTMLFile($outputFile);
    } catch (Exception $e) {
        file_put_contents($outputFile, $e->getMessage());
    }
}

function convertPhpErrorsToExceptions()
{
    static $isNotLoaded = true;
    if ($isNotLoaded) {
        set_error_handler('Edge\QA\phpErrorToException');
        $isNotLoaded = false;
    }
}

function phpErrorToException($severity, $message, $filename, $lineno)
{
    if (error_reporting() & $severity) {
        throw new \ErrorException($message, 0, $severity, $filename, $lineno);
    }
}
