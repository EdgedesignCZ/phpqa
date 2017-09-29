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

    public function __construct(Config $c, Options $o, RunningTool $t)
    {
        $this->config = $c;
        $this->options = $o;
        $this->tool = $t;
    }

    abstract public function __invoke();

    public function saveDynamicConfig($config, $fileExtension)
    {
        $directory = rtrim($this->options->isSavedToFiles ? $this->options->rawFile('') : getcwd(), '/');
        $file = "{$directory}/{$this->tool}-phpqa.{$fileExtension}";
        file_put_contents($file, $config);
        return \Edge\QA\escapePath($file);
    }
}
