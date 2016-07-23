<?php

namespace Edge\QA;

class RunningTool
{
    private $tool;
    private $optionsSeparator;

    private $errorsXPath;
    private $allowedErrorsCount;

    public $transformedXml;
    public $htmlReport;

    public function __construct($tool, array $toolConfig)
    {
        $config = $toolConfig + [
            'optionSeparator' => '=',
            'transformedXml' => '',
            'errorsXPath' => '',
            'allowedErrorsCount' => null
        ];
        $this->tool = $tool;
        $this->optionsSeparator = $config['optionSeparator'];
        $this->transformedXml = $config['transformedXml'];
        $this->errorsXPath = $config['errorsXPath'];
        $this->allowedErrorsCount = $config['allowedErrorsCount'];
    }

    public function buildOption($arg, $value)
    {
        if ($value) {
            return "--{$arg}{$this->optionsSeparator}{$value}";
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
        if (!is_numeric($this->allowedErrorsCount)) {
            return [true, ''];
        } elseif (!file_exists($this->transformedXml)) {
            return [false, 0];
        }

        $xml = simplexml_load_file($this->transformedXml);
        $errorsCount = count($xml->xpath($this->errorsXPath));
        $isOk = $errorsCount <= $this->allowedErrorsCount;
        return [$isOk, $errorsCount];
    }
    
    public function __toString()
    {
        return $this->tool;
    }
}
