<?php

namespace Edge\QA;

class RunningTool
{
    private $tool;
    private $internalClass;
    private $optionSeparator;

    private $xmlFiles;
    private $errorsXPath;
    private $allowedErrorsCount;

    public $htmlReport;
    public $hasOnlyConsoleOutput;
    /** @var \Symfony\Component\Process\Process */
    public $process;

    public function __construct($tool, array $toolConfig)
    {
        $config = $toolConfig + [
            'optionSeparator' => '=',
            'xml' => [],
            'errorsXPath' => '',
            'allowedErrorsCount' => null,
            'hasOnlyConsoleOutput' => false,
            'internalClass' => null,
        ];
        $this->tool = $tool;
        $this->internalClass = $config['internalClass'];
        $this->optionSeparator = $config['optionSeparator'];
        $this->xmlFiles = $config['xml'];
        $this->errorsXPath = $config['errorsXPath'];
        $this->allowedErrorsCount = $config['allowedErrorsCount'];
        $this->hasOnlyConsoleOutput = $config['hasOnlyConsoleOutput'];
    }

    public function isInstalled()
    {
        return !$this->internalClass || class_exists($this->internalClass);
    }

    public function buildOption($arg, $value)
    {
        if ($value || $value === 0) {
            return "--{$arg}{$this->optionSeparator}{$value}";
        } else {
            return "--{$arg}";
        }
    }

    public function getAllowedErrorsCount()
    {
        return $this->allowedErrorsCount;
    }

    public function analyzeResult($hasNoOutput = false)
    {
        if ($hasNoOutput || $this->hasOnlyConsoleOutput) {
            return $this->evaluteErrorsCount($this->process->getExitCode() ? 1 : 0);
        } elseif (!$this->errorsXPath) {
            return [true, ''];
        } elseif (!file_exists($this->getMainXml())) {
            return [false, 0];
        }

        $xml = simplexml_load_file($this->getMainXml());
        $errorsCount = count($xml->xpath($this->errorsXPath));
        return $this->evaluteErrorsCount($errorsCount);
    }

    private function evaluteErrorsCount($errorsCount)
    {
        $isOk = $errorsCount <= $this->allowedErrorsCount || $this->areErrorsIgnored();
        return [$isOk, $errorsCount];
    }

    private function areErrorsIgnored()
    {
        return !is_numeric($this->allowedErrorsCount);
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
