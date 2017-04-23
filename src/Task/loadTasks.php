<?php

namespace Edge\QA\Task;

trait loadTasks
{
    function taskPhpqaRunner($isParallel, RoboAdapter $robo)
    {
        $class = $isParallel ? ParallelExec::class : ($robo->isVersionOne() ? NonParallelExecV1::class : NonParallelExecV0::class);
        if ($robo->isVersionOne()) {
            return $this->task($class);
        } else {
            return new $class();
        }
    }
}
