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
                file_put_contents($localAsset, $this->downloadUrl($url));
                $assets[$id] = "./{$id}";
                $progressBar->advance();
            }
            $duration = round(microtime(true) - $start, 3);
            $this->writeln("Download time: <comment>{$duration}s</comment>");
        }
        return $assets;
    }

    private function downloadUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid url '{$url}'");
        }
        if (ini_get('allow_url_fopen')) {
            return file_get_contents($url);
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
    }

    // copy-paste from \Robo\Common\TaskIO
    private function writeln($text, $color = 'magenta')
    {
        $this->output->writeln(
            "\n<fg=white;bg={$color};options=bold>[assets]</fg=white;bg={$color};options=bold> {$text}"
        );
    }
}
