<?php

namespace Edge\QA\Tool;

class Phpunit extends Tool
{
    public function __invoke()
    {
        $args = array();
        $configFile = $this->config->path('phpunit.config');
        if ($configFile) {
            $args['configuration'] = $configFile;
        }
        if ($this->options->isSavedToFiles) {
            $extensions = [
                'junit' => 'xml', 'text' => 'txt', 'tap' => 'text',
                'clover' => 'xml', 'crap4j' => 'xml',
            ];
            foreach ($this->config->value('phpunit.reports.file') as $report => $formats) {
                foreach ($formats as $format) {
                    $extension = array_key_exists($format, $extensions) ? $extensions[$format] : $format;
                    $filename = "{$report}-{$format}.{$extension}";
                    $args["{$report}-{$format}"] = $this->options->toFile($filename);
                    $this->tool->userReports["{$report}.{$format}"] = $this->options->rawFile($filename);
                }
            }
        }
        return $args;
    }
}
