<?php

namespace Edge\QA\Task;

use Edge\QA\Options;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class AssetsLoader
{
    private $output;
    
    public function __construct(OutputInterface $o)
    {
        $this->output = $o;
    }

    public function __invoke(Options $o, array $assets)
    {
        if ($o->isOfflineReport) {
            $this->writeln('Dowloading assets...');
            $progressBar = new ProgressBar($this->output);
            $progressBar->start(count($assets));
            $start = microtime(true);
            foreach ($assets as $id => $url) {
                $localAsset = $o->rawFile($id);
                file_put_contents($localAsset, file_get_contents($url));
                $assets[$id] = "./{$id}";
                $progressBar->advance();
            }
            $duration = round(microtime(true) - $start, 3);
            $this->writeln("Download time: <comment>{$duration}s</comment>");
        }
        return $assets;
    }

    // copy-paste from \Robo\Common\TaskIO
    private function writeln($text, $color = 'magenta')
    {
        $this->output->writeln(
            "\n<fg=white;bg={$color};options=bold>[assets]</fg=white;bg={$color};options=bold> {$text}"
        );
    }
}
