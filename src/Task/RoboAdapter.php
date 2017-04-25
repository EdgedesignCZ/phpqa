<?php

namespace Edge\QA\Task;

trait RoboAdapter
{
    protected function taskPhpqaRunner($isParallel)
    {
        $getClass = function ($class) {
            return "Edge\QA\Task\\{$class}";
        };
        $class = $isParallel
            ? $getClass('ParallelExec')
            : ($this->isRoboVersionOne() ? $getClass('NonParallelExecV1') : $getClass('NonParallelExecV0'));
        if ($this->isRoboVersionOne()) {
            return $this->task($class);
        } else {
            return new $class();
        }
    }

    protected function addArgToExec($exec, $arg)
    {
        if ($this->isRoboVersionOne()) {
            return $exec->rawArg($arg);
        } else {
            return $exec->arg($arg);
        }
    }

    private function isRoboVersionOne()
    {
        static $isVersionOne = null;
        if ($isVersionOne === null) {
            $isVersionOne = method_exists('Robo\Common\CommandArguments', 'rawArg');
        }
        return $isVersionOne;
    }
}
