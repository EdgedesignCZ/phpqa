<?php

namespace Edge\QA\Tool;

class Phploc extends Tool
{
    public function __invoke()
    {
        $args = array(
            $this->options->ignore->bergmann(),
            $this->options->getAnalyzedDirs(' '),
        );
        if ($this->options->isSavedToFiles) {
            $args['log-xml'] = $this->options->toFile('phploc.xml');
        }
        return $args;
    }
}
