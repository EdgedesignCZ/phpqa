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
    private $presenter;

    public function __construct(Config $c, $presenter)
    {
        $this->config = $c;
        $this->presenter = $presenter;
        $this->loadTools();
    }

    /** @SuppressWarnings(PHPMD.ExitExpression) */
    private function loadTools()
    {
        foreach ($this->config->value('tool') as $tool => $handler) {
            $handlers = array_map(
                function ($handler) use ($tool) {
                    $abstractTool = 'Edge\QA\Tools\Tool';
                    if (!is_subclass_of($handler, $abstractTool)) {
                        die("Invalid handler for {$tool}. {$handler} is not subclass of '{$abstractTool}'\n");
                    }
                    $customBinary = $this->config->getCustomBinary($tool);
                    return [
                        'handler' => $handler,
                        'hasCustomBinary' => (bool) $customBinary,
                        'runBinary' => \Edge\QA\buildToolBinary($tool, $customBinary),
                    ] + $handler::$SETTINGS;
                },
                (array) $handler
            );
            if (count($handlers) > 1) {
                $handlers = array_filter(
                    $handlers,
                    function (array $config) use ($tool) {
                        if (!isset($config['internalClass'])) {
                            return true;
                        }
                        if (!is_string($config['internalClass'])) {
                            throw new \RuntimeException(
                                "'{$tool}' has multiple handlers - 'internalClass' can't be array (string expected)"
                            );
                        }
                        return class_exists($config['internalClass']);
                    }
                );
            }
            $this->tools[$tool] = array_shift($handlers);
        }
    }

    public function getExecutableTools(Options $o)
    {
        if (!$this->selectedTools) {
            $this->selectedTools = $o->buildRunningTools($this->tools);
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
        $binary = $this->tools[(string) $tool]['runBinary'];
        $handlerClass = $this->tools[(string) $tool]['handler'];
        $handler = new $handlerClass($this->config, $o, $tool, $this->presenter);
        $args = $handler($tool);

        return array($binary, $args);
    }

    public function getReport(RunningTool $tool)
    {
        return  $this->config->path("report.{$tool}");
    }

    public function getAssets()
    {
        return  $this->config->value('report.assets');
    }

    public function getSummary(Options $o)
    {
        $analyzeResults = new AnalyzeResults($o);
        return $analyzeResults->__invoke($this->selectedTools);
    }

    public function getVersions()
    {
        $versions = new GetVersions();
        return $versions($this->tools);
    }
}
