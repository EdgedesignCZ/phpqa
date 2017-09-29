<?php

namespace Edge\QA\Tool;

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
}
