<?php

namespace Edge\QA\Tools\Analyzer;

use Edge\QA\OutputMode;

class Phpunit extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'outputMode' => OutputMode::RAW_CONSOLE_OUTPUT,
        'composer' => 'phpunit/phpunit',
    );

    public function __invoke()
    {
        $args = array();
        $configFile = $this->config->path('phpunit.config');
        if ($configFile) {
            $args['configuration'] = \Edge\QA\escapePath($configFile);
        }
        if ($this->options->isSavedToFiles) {
            foreach ($this->config->value('phpunit.reports.file') as $report => $formats) {
                foreach ($formats as $format) {
                    list($file, $html) = $this->getFile($report, $format);
                    $args["{$report}-{$format}"] = $this->options->toFile($file);
                    $this->tool->userReports["{$report}.{$format}"] = $this->options->rawFile($html ?: $file);
                }
            }
        }
        return $args;
    }

    private function getFile($report, $format)
    {
        static $extensions = [
            'junit' => 'xml', 'text' => 'txt', 'tap' => 'text',
            'clover' => 'xml', 'crap4j' => 'xml',
        ];
        if ("{$report}-{$format}" == 'coverage-html') {
            return ["{$report}-{$format}/", "{$report}-{$format}/index.html"];
        } elseif ("{$report}-{$format}" == 'coverage-xml') {
            return ["{$report}-{$format}/", null];
        } else {
            $extension = array_key_exists($format, $extensions) ? $extensions[$format] : $format;
            return ["{$report}-{$format}.{$extension}", null];
        }
    }
}
