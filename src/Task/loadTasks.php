<?php

namespace Edge\QA\Task;

trait loadTasks
{
    function taskPhpqaRunner($isParallel, RoboAdapter $robo)
    {
        $class = $isParallel ? ParallelExec::class : NonParallelExec::class;
        if ($robo->isVersionOne()) {
            return $this->task($class);
        } else {
            return new $class();
        }
    }
}
