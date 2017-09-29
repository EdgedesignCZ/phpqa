<?php

namespace Edge\QA\Tool;

class PhpMetricsV2 extends Tool
{
    public function __invoke()
    {
        $args = array(
            $this->options->ignore->phpmetrics2(),
            'extensions' => $this->config->csv('extensions')
        );
        if ($this->options->isSavedToFiles) {
            $this->tool->htmlReport = $this->options->rawFile('phpmetrics/index.html');
            $args['report-html'] = $this->options->toFile('phpmetrics/');
            $args['report-violations'] = $this->options->toFile('phpmetrics.xml');
        }
        $args[] = $this->options->getAnalyzedDirs(',');
        return $args;
    }
}
