<?php

namespace Edge\QA\Tools;

use Edge\QA\Config;
use Edge\QA\Options;
use Edge\QA\RunningTool;

class Tools
{
    private $config;
    private $tools = array();

    /** @var \Edge\QA\Task\ToolVersions */
    private $versions;
    /** @var \Edge\QA\Task\ToolSummary */
    private $summary;
    /** @var RunningTool[] */
    private $runningTools;

    public function __construct(Config $c)
    {
        $this->config = $c;
        $this->loadTools();
        $this->versions = new \Edge\QA\Task\ToolVersions($this->tools, $this->config);
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

    public function getRunningTools(Options $o)
    {
        if (!$this->runningTools) {
            $this->buildRunningTools($o);
        }
        return $this->runningTools;
    }

    private function buildRunningTools(Options $o)
    {
        list($this->runningTools, $skippedTools) = $o->buildRunningTools($this->tools, $this->config);
        $this->summary = new \Edge\QA\Task\ToolSummary($o, $this->runningTools, $skippedTools);
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

    public function getSummary()
    {
        return $this->summary->__invoke();
    }

    public function getVersions()
    {
        return $this->versions->__invoke();
    }
}
