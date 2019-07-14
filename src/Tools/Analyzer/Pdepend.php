<?php

namespace Edge\QA\Tools\Analyzer;

class Pdepend extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'xml' => ['pdepend-jdepend.xml', 'pdepend-summary.xml', 'pdepend-dependencies.xml'],
        'composer' => 'pdepend/pdepend',
    );

    public function __invoke()
    {
        $args = array(
            'jdepend-xml' => $this->options->toFile('pdepend-jdepend.xml'),
            'summary-xml' => $this->options->toFile('pdepend-summary.xml'),
            'dependency-xml' => $this->options->toFile('pdepend-dependencies.xml'),
            'jdepend-chart' => $this->options->toFile('pdepend-jdepend.svg'),
            'overview-pyramid' => $this->options->toFile('pdepend-pyramid.svg'),
            'suffix' => $this->config->csv('phpqa.extensions'),
            $this->options->ignore->pdepend()
        );
        $coverageReport = $this->config->value('pdepend.coverageReport');
        if ($coverageReport) {
            $args['coverage-report'] = $coverageReport;
        }
        $args[] = $this->options->getAnalyzedDirs(',');
        return $args;
    }
}
