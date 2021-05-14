<?php

namespace Edge\QA\Tools;

use Edge\QA\Config;
use Edge\QA\Options;
use Edge\QA\RunningTool;

abstract class Tool
{
    /** @var Config */
    protected $config;
    /** @var Options */
    protected $options;
    /** @var RunningTool */
    protected $tool;
    /** @var \Closure */
    private $presenter;
    /** @var array */
    public static $SETTINGS = array();

    public function __construct(Config $c, Options $o, RunningTool $t, $presenter)
    {
        $this->config = $c;
        $this->options = $o;
        $this->tool = $t;
        $this->presenter = $presenter;
    }

    abstract public function __invoke();

    protected function saveDynamicConfig($config, $fileExtension)
    {
        $directory = rtrim($this->options->isSavedToFiles ? $this->options->rawFile('') : getcwd(), '/');
        $file = "{$directory}/{$this->tool}-phpqa.{$fileExtension}";
        file_put_contents($file, $config);
        return \Edge\QA\escapePath($file);
    }

    protected function toolVersionIs($operator, $version)
    {
        $versions = new GetVersions();
        $customBinary = $this->config->getCustomBinary((string) $this->tool);
        $settings = static::$SETTINGS + [
            'hasCustomBinary' => (bool) $customBinary,
            'runBinary' => \Edge\QA\buildToolBinary((string) $this->tool, $customBinary),
        ];
        return $versions->hasToolVersion($settings, $operator, $version);
    }

    protected function writeln($text)
    {
        $this->presenter->__invoke($text);
    }
}
