<?php

namespace Edge\QA\Tools;

use Edge\QA\Config;
use Edge\QA\Options;
use Edge\QA\RunningTool;

class Tools
{
    private $config;
    private $tools = array();
    private $selectedTools;

    public function __construct(Config $c)
    {
        $this->config = $c;
        $this->loadTools();
    }

    /** @SuppressWarnings(PHPMD.ExitExpression) */
    private function loadTools()
    {
        foreach ($this->config->value('tool') as $id => $handler) {
            $handlers = array_map(
                function ($handler) use ($id) {
                    if (!is_subclass_of($handler, 'Edge\QA\Tool\Tool')) {
                        die("Invalid handler for {$id}. {$handler} is not subclass of 'Edge\QA\Tool\Tool'\n");
                    }
                    return $handler::$SETTINGS + ['handler' => $handler];
                },
                (array) $handler
            );
            if (count($handlers) > 1) {
                $handlers = array_filter(
                    $handlers,
                    function (array $config) {
                        return isset($config['internalClass']) ? class_exists($config['internalClass']) : true;
                    }
                );
            }
            $this->tools[$id] = array_shift($handlers);
        }
    }

    public function getExecutableTools(Options $o)
    {
        if (!$this->selectedTools) {
            $this->selectedTools = $o->buildRunningTools($this->tools, $this->config);
        }
        return array_filter(
            $this->selectedTools,
            function (RunningTool $t) {
                return $t->isExecutable;
            }
        );
    }

    public function buildCommand(RunningTool $tool, Options $o)
    {
        $customBinary = $this->config->getCustomBinary($tool);
        $binary = $customBinary ?: \Edge\QA\pathToBinary((string) $tool);

        $handlerClass = $this->tools[(string) $tool]['handler'];
        $handler = new $handlerClass($this->config, $o, $tool);
        $args = $handler($tool);

        return array($binary, $args);
    }

    public function getReport(RunningTool $tool)
    {
        return  $this->config->path("report.{$tool}");
    }

    public function getSummary(Options $o)
    {
        $analyzeResults = new AnalyzeResults($o);
        return $analyzeResults->__invoke($this->selectedTools);
    }

    public function getVersions()
    {
        $versions = new GetVersions();
        return $versions($this->tools, $this->config);
    }
}
