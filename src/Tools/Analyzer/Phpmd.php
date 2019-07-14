<?php

namespace Edge\QA\Tools\Analyzer;

class Phpmd extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => ' ',
        'xml' => ['phpmd.xml'],
        'errorsXPath' => '//pmd/file/violation',
        'composer' => 'phpmd/phpmd',
    );

    public function __invoke()
    {
        $rulesets = $this->config->pathsOrValues('phpmd.standard');

        $args = array(
            $this->options->getAnalyzedDirs(','),
            $this->options->isSavedToFiles ? 'xml' : 'text',
            \Edge\QA\escapePath(implode(',', $rulesets)),
            $this->options->ignore->phpmd(),
            'suffixes' => $this->config->csv('phpqa.extensions')
        );
        if ($this->options->isSavedToFiles) {
            $args['reportfile'] = $this->tool->getEscapedXmlFile();
        }
        return $args;
    }
}
