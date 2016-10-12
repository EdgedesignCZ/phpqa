<?php

namespace Edge\QA;

use DOMDocument;
use XSLTProcessor;
use Exception;
use Twig_Loader_Filesystem;
use Twig_Environment;

function twigToHtml($template, array $params, $outputFile)
{
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../app/report');
    $twig = new Twig_Environment($loader);
    $html = $twig->render($template, $params);
    file_put_contents($outputFile, $html);
}

function xmlToHtml($input, $style, $outputFile)
{
    convertPhpErrorsToExceptions();
    try {
        if (is_array($input)) {
            $rootXml = array_shift($input);
            $xml = new DOMDocument();
            $xml->load($rootXml);
            foreach ($input as $file) {
                $anotherXml = new DOMDocument();
                $anotherXml->load($file);
                $xml->documentElement->appendChild($xml->importNode($anotherXml->documentElement, true));
            }
        } else {
            $xml = new DOMDocument();
            $xml->load($input);
        }

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
