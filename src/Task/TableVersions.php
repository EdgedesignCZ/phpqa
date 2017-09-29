<?php

namespace Edge\QA\Task;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class TableVersions
{
    private $output;
    
    public function __construct(OutputInterface $o)
    {
        $this->output = $o;
    }

    public function __invoke(array $versions)
    {
        $table = new Table($this->output);
        $table->setHeaders(['Tool', 'Version', 'Authors / Info']);
        foreach ($versions as $tool => $version) {
            $table->addRow(array(
                "<comment>{$tool}</comment>",
                $version['version_normalized'],
                $version['authors'],
            ));
        }
        $table->render();
    }
}
