<?php

namespace Edge\QA\Task;

use Consolidation\Log\ConsoleLogLevel;
use Robo\Result;
use Symfony\Component\Process\Process;

/**
 * The task has similar output, same signatures as ParallelExec,
 * but executes processes sequentially (like in \Robo\Task\CommandStack)
 *
 * \Robo\Task\Base\ExecStack has different behavior than ParallelExec:
 * - all commands are executed inside one command
 * - group execution with stopOnFail, but it stops everything when e.g. phpcs failed
 * - prints phploc's output when one command failed
 *      phpqa --tools phpcs,phploc,phpmd --output file --execution s --quiet
 */
class NonParallelExecV1 extends ParallelExec
{
    public function run()
    {
        foreach ($this->processes as $process) {
            $process->setIdleTimeout($this->idleTimeout);
            $process->setTimeout($this->timeout);
            $this->printTaskInfo($process->getCommandLine());
        }

        $this->startProgressIndicator();
        $this->startTimer();

        foreach ($this->processes as $process) {
            $process->run();
            $this->advanceProgressIndicator();
            if ($this->isPrinted) {
                $this->printProcessResult($process);
            }
        }

        $this->stopProgressIndicator();
        $this->stopTimer();

        $errorMessage = '';
        $exitCode = 0;
        foreach ($this->processes as $p) {
            if ($p->getExitCode() === 0) {
                continue;
            }
            $errorMessage .= "'" . $p->getCommandLine() . "' exited with code ". $p->getExitCode()." \n";
            $exitCode = max($exitCode, $p->getExitCode());
        }
        if (!$errorMessage) {
            $this->printTaskSuccess(count($this->processes) . " processes finished running");
        }

        return new Result($this, $exitCode, $errorMessage, ['time' => $this->getExecutionTime()]);
    }

    protected function printProcessResult(Process $process)
    {
        $this->printTaskInfo(
            "Output for <fg=white;bg=magenta> " . $process->getCommandLine()." </fg=white;bg=magenta>"
        );
        $this->printTaskOutput(ConsoleLogLevel::SUCCESS, $process->getOutput(), $this->getTaskContext());
        if ($process->getErrorOutput()) {
            $this->printTaskOutput(ConsoleLogLevel::ERROR, $process->getErrorOutput(), $this->getTaskContext());
        }
    }
}
