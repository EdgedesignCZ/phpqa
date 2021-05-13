<?php

namespace Edge\QA\Tools\Analyzer;

class Phpcs extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'xml' => ['checkstyle.xml'],
        'errorsXPath' => [
            # ignoreWarnings => xpath
            false => '//checkstyle/file/error',
            true => '//checkstyle/file/error[@severity="error"]',
        ],
        'composer' => 'squizlabs/php_codesniffer',
    );

    public function __invoke()
    {
        $this->tool->errorsType = $this->config->value('phpcs.ignoreWarnings') === true;
        $standards = $this->config->pathsOrValues('phpcs.standard');
        $args = array(
            '-p',
            'standard' => \Edge\QA\escapePath(implode(',', $standards)),
            $this->options->ignore->phpcs(),
            $this->options->getAnalyzedDirs(' '),
            'extensions' => $this->config->csv('phpqa.extensions')
        );
        if ($this->options->isSavedToFiles) {
            $reports = ['checkstyle' => 'checkstyle.xml'] + $this->config->value('phpcs.reports.file');
            foreach ($reports as $report => $file) {
                $args["report-{$report}"] = $this->options->toFile($file);
                if ($report != 'checkstyle') {
                    $this->tool->userReports[$report] = $this->options->rawFile($file);
                }
            }
        } else {
            foreach ($this->config->value('phpcs.reports.cli') as $report) {
                $args["report-{$report}"] = '';
            }
            if ($this->config->value('phpcs.ignoreWarnings')) {
                $args['warning-severity'] = 0;
            }
        }

        return $args;
    }
}
