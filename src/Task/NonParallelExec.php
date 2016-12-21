<?php

namespace Edge\QA\Task;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Robo\Result;

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
class NonParallelExec extends ParallelExec
{
    public function run()
    {
        foreach ($this->processes as $process) {
            $this->printTaskInfo($process->getCommandLine());
        }

        $progress = new ProgressBar($this->getOutput());
        $progress->start(count($this->processes));
        $this->startTimer();

        foreach ($this->processes as $process) {
            $process->run();
            $progress->advance();
            if ($this->isPrinted) {
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

        $this->getOutput()->writeln("");
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
}
