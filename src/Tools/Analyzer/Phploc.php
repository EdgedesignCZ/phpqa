<?php

namespace Edge\QA\Tools\Analyzer;

class Phploc extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => ' ',
        'xml' => ['phploc.xml'],
        'composer' => 'phploc/phploc',
    );

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
