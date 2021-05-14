<?php

namespace Edge\QA\Tools\Analyzer;

class Phpmd extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => ' ',
        'xml' => ['phpmd.xml'],
        'errorsXPath' => [
            # ignoreParsingErrors => xpath
            true => '//pmd/file/violation',
            false => ['//pmd/file/violation', '//pmd/error'],
        ],
        'composer' => 'phpmd/phpmd',
    );

    public function __invoke()
    {
        $this->tool->errorsType = $this->config->value('phpmd.ignoreParsingErrors') === true;
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
