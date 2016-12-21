<?php

namespace Edge\QA\Task;

/**
 * Adding process breaks parent signature.
 * Returns Symfony process instead of fluent interface.
 */
class ParallelExec extends \Robo\Task\Base\ParallelExec
{
    public function process($command)
    {
        parent::process($command);
        return end($this->processes);
    }
}
