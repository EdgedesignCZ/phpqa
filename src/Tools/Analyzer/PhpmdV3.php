<?php

namespace Edge\QA\Tools\Analyzer;

class PhpmdV3 extends \Edge\QA\Tools\Tool
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
        $extensionOptions = $this->config->csv('phpqa.extensions', ' --suffixes ');

        $args = array(
            'analyze',
            $this->options->ignore->phpmd3(),
            $extensionOptions ? " --suffixes $extensionOptions" : '',
            'ruleset' => \Edge\QA\escapePath(implode(',', $rulesets)),
            'format' => $this->options->isSavedToFiles ? 'xml' : 'text',
        );
        if ($this->options->isSavedToFiles) {
            $args['reportfile-xml'] = $this->tool->getEscapedXmlFile();
        }
        $args[] = '-- ' . $this->options->getAnalyzedDirs(' ');
        return $args;
    }
}
