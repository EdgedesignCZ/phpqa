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

        if ($this->toolVersionIs('>=', '3')) {
            return $this->buildArgsV3($rulesets);
        }

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

    private function buildArgsV3(array $rulesets)
    {
        $extensions = array_filter(array_map('trim', explode(',', $this->config->csv('phpqa.extensions'))));
        $suffixArgs = ' ' . implode(' ', array_map(function ($ext) {
            return '--suffixes ' . $ext;
        }, $extensions));

        $args = [];
        $args[] = 'analyze';
        $args[] = $this->options->ignore->phpmd3();
        $args[] = $suffixArgs;
        $args['ruleset'] = \Edge\QA\escapePath(implode(',', $rulesets));
        $args['format'] = $this->options->isSavedToFiles ? 'xml' : 'text';
        if ($this->options->isSavedToFiles) {
            $args['reportfile-xml'] = $this->tool->getEscapedXmlFile();
        }
        $args[] = '-- ' . $this->options->getAnalyzedDirs(' ');
        return $args;
    }
}
