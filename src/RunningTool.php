<?php

namespace Edge\QA;

class RunningTool
{
    private $tool;
    private $optionSeparator;

    private $xmlFiles;
    private $errorsXPath;
    private $allowedErrorsCount;

    public $htmlReport;

    public function __construct($tool, array $toolConfig)
    {
        $config = $toolConfig + [
            'optionSeparator' => '=',
            'xml' => [],
            'errorsXPath' => '',
            'allowedErrorsCount' => null
        ];
        $this->tool = $tool;
        $this->optionSeparator = $config['optionSeparator'];
        $this->xmlFiles = $config['xml'];
        $this->errorsXPath = $config['errorsXPath'];
        $this->allowedErrorsCount = $config['allowedErrorsCount'];
    }

    public function buildOption($arg, $value)
    {
        if ($value) {
            return "--{$arg}{$this->optionSeparator}{$value}";
        } else {
            return "--{$arg}";
        }
    }

    public function getAllowedErrorsCount()
    {
        return $this->allowedErrorsCount;
    }

    public function analyzeResult()
    {
        if (!$this->errorsXPath) {
            return [true, ''];
        } elseif (!file_exists($this->getMainXml())) {
            return [false, 0];
        }

        $xml = simplexml_load_file($this->getMainXml());
        $errorsCount = count($xml->xpath($this->errorsXPath));
        $isOk = $errorsCount <= $this->allowedErrorsCount || !is_numeric($this->allowedErrorsCount);
        return [$isOk, $errorsCount];
    }

    public function getXmlFiles()
    {
        return $this->xmlFiles;
    }

    public function getEscapedXmlFile()
    {
        return escapePath($this->getMainXml());
    }

    private function getMainXml()
    {
        return reset($this->xmlFiles);
    }

    public function __toString()
    {
        return $this->tool;
    }
}
