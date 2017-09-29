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
        'internalClass' => 'PHP_CodeSniffer',
    );

    public function __invoke()
    {
        return $this->buildPhpcs(\PHP_CodeSniffer::getInstalledStandards());
    }

    protected function buildPhpcs(array $installedStandards)
    {
        $this->tool->errorsType = $this->config->value('phpcs.ignoreWarnings') === true;
        $standard = $this->config->value('phpcs.standard');
        if (!in_array($standard, $installedStandards)) {
            $standard = \Edge\QA\escapePath($this->config->path('phpcs.standard'));
        }
        $args = array(
            '-p',
            'standard' => $standard,
            $this->options->ignore->phpcs(),
            $this->options->getAnalyzedDirs(' '),
            'extensions' => $this->config->csv('extensions')
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
        }

        return $args;
    }
}
