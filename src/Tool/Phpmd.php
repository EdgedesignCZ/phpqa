<?php

namespace Edge\QA\Tool;

class Phpmd extends Tool
{
    public function __invoke()
    {
        $args = array(
            $this->options->getAnalyzedDirs(','),
            $this->options->isSavedToFiles ? 'xml' : 'text',
            \Edge\QA\escapePath($this->config->path('phpmd.standard')),
            $this->options->ignore->phpmd(),
            'suffixes' => $this->config->csv('extensions')
        );
        if ($this->options->isSavedToFiles) {
            $args['reportfile'] = $this->tool->getEscapedXmlFile();
        }
        return $args;
    }
}
