<?php

namespace Edge\QA\Tools\Analyzer;

class Phpcpd extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => ' ',
        'xml' => ['phpcpd.xml'],
        'errorsXPath' => '//pmd-cpd/duplication',
        'composer' => 'sebastian/phpcpd',
    );

    public function __invoke()
    {
        $args = array(
            'verbose' => '',
            $this->options->ignore->bergmann(),
            $this->options->getAnalyzedDirs(' '),
            'min-lines' => $this->config->value('phpcpd.minLines'),
            'min-tokens' => $this->config->value('phpcpd.minTokens'),
        );
        $phpcpdNames = array_map(
            function ($extension) {
                return ".{$extension}";
            },
            array_filter(explode(',', $this->config->csv('phpqa.extensions')))
        );
        if ($phpcpdNames) {
            $args['suffix'] = \Edge\QA\escapePath(implode(',', $phpcpdNames));
        }
        if ($this->options->isSavedToFiles) {
            $args['log-pmd'] = $this->tool->getEscapedXmlFile();
        }
        return $args;
    }
}
