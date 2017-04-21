<?php

namespace Edge\QA\Task;

use Robo\Robo;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Adding process breaks parent signature.
 * Returns Symfony process instead of fluent interface.
 */
class ParallelExec extends \Robo\Task\Base\ParallelExec
{
    public function process($command)
    {
        $this->setLogger(Robo::service('logger'));
        parent::process($command);

        return end($this->processes);
    }
}
