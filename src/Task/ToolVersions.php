<?php

namespace Edge\QA\Task;

use Symfony\Component\Console\Output\OutputInterface;
use Robo\Task\Base\Exec;

class ToolVersions
{
    private $output;
    
    public function __construct(OutputInterface $p)
    {
        $this->output = $p;
    }

    public function __invoke(array $tools)
    {
        $this->output->writeln([
            '<comment>phpqa ' . PHPQA_VERSION . '</comment>',
            '',
        ]);

        foreach ($tools as $tool) {
            $versionCommand = $tool == 'parallel-lint' ? $tool : "{$tool} --version";
            $this->loadVersionFromConsoleCommand($versionCommand);
        }
    }

    private function loadVersionFromConsoleCommand($command)
    {
        $exec = new Exec(\Edge\QA\pathToBinary($command));
        $result = $exec
            ->printed(false)
            ->run()
            ->getMessage();
        $this->output->writeln($this->getFirstLine($result));
    }

    private function getFirstLine($string)
    {
        return strtok($string, "\n");
    }
}
