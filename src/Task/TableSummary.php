<?php

namespace Edge\QA\Task;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class TableSummary
{
    private $output;
    
    public function __construct(OutputInterface $o)
    {
        $this->output = $o;
    }

    public function __invoke(array $results)
    {
        $this->writeln('', 'cyan');
        $table = new Table($this->output);

        if ($results['isErrorsCountAnalyzed']) {
            $table->setHeaders(array('Tool', 'Allowed Errors', 'Errors count', 'Is OK?', 'HTML report'));
        } else {
            $table->setHeaders(array('Tool', 'Allowed exit code', 'Exit code', 'Is OK?'));
        }

        foreach ($results['tools'] as $tool => $result) {
            if ($tool == 'phpqa') {
                $table->addRow(new TableSeparator());
            }
            $table->addRow(array(
                "<comment>{$tool}</comment>",
                $result['allowedErrorsCount'],
                $result['errorsCount'],
                $this->getStatus($result['hasSucceeded']),
                $result['htmlReport'],
            ));
        }

        $table->render();
        return $this->result($results);
    }

    private function result(array $results)
    {
        if ($results['notInstalledTools']) {
            $this->writeln(
                'Not installed tools: <comment>' . implode(', ', $results['notInstalledTools']) . '</comment>',
                'magenta'
            );
        }
        if ($results['failedTools']) {
            $this->writeln('Failed tools: <comment>' . implode(', ', $results['failedTools']) . '</comment>', 'red');
        } else {
            $this->writeln('No failed tools', 'green');
        }
        return $results['tools']['phpqa']['hasSucceeded'] ? 0 : 1;
    }

    private function getStatus($isOk)
    {
        return $isOk ? '<info>✓</info>' : '<error>x</error>';
    }

    // copy-paste from \Robo\Common\TaskIO
    private function writeln($text, $color)
    {
        $this->output->writeln(
            "\n<fg=white;bg={$color};options=bold>[phpqa]</fg=white;bg={$color};options=bold> {$text}"
        );
    }
}
