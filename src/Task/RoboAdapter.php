<?php

namespace Edge\QA\Task;

class RoboAdapter
{
    private $isVersionOne;

    public function __construct()
    {
        $this->isVersionOne = method_exists('Robo\Common\CommandArguments', 'rawArg');
    }

    // Robo v1 escapes the values
    public function arg($exec, $arg)
    {
        if ($this->isVersionOne) {
            return $exec->rawArg($arg);
        } else {
            return $exec->arg($arg);
        }
    }

    public function isVersionOne()
    {
        return $this->isVersionOne;
    }
}
