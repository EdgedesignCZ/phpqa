<?php

namespace Edge\QA\Task;

trait RoboAdapter
{
    protected function taskPhpqaRunner($isParallel)
    {
        $class = $isParallel
            ? ParallelExec::class
            : ($this->isRoboVersionOne() ? NonParallelExecV1::class : NonParallelExecV0::class);
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
