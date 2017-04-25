<?php

namespace Edge\QA\Task;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

// Adapter for Robo v0.X
class NonParallelExecV0 extends NonParallelExecV1
{
    private $progress;

    protected function startProgressIndicator()
    {
        $this->progress = new ProgressBar($this->getOutput());
        $this->progress->start(count($this->processes));
    }

    protected function advanceProgressIndicator($steps = 1)
    {
        $this->progress->advance($steps);
    }

    protected function stopProgressIndicator()
    {
        $this->getOutput()->writeln("");
    }

    protected function printProcessResult(Process $process)
    {
        $this->getOutput()->writeln("");
        $this->printTaskInfo(
            "Output for <fg=white;bg=magenta> " . $process->getCommandLine()." </fg=white;bg=magenta>"
        );
        $this->getOutput()->writeln($process->getOutput(), OutputInterface::OUTPUT_RAW);
        if ($process->getErrorOutput()) {
            $this->getOutput()->writeln("<error>" . $process->getErrorOutput() . "</error>");
        }
    }
}
