<?php

namespace Edge\QA\Tool;

class ParallelLint extends Tool
{
    public function __invoke()
    {
        return array(
            $this->options->ignore->parallelLint(),
            $this->options->getAnalyzedDirs(' '),
        );
    }
}
