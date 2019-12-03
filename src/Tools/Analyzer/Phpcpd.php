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
        $phpcpdNames = array_map(
            function ($extension) {
                return "*.{$extension}";
            },
            explode(',', $this->config->csv('phpqa.extensions'))
        );

        $args = array(
            'progress' => '',
            $this->options->ignore->bergmann(),
            $this->options->getAnalyzedDirs(' '),
            'min-lines' => $this->config->value('phpcpd.minLines'),
            'min-tokens' => $this->config->value('phpcpd.minTokens'),
            'names' => $phpcpdNames,
        );
        if ($this->options->isSavedToFiles) {
            $args['log-pmd'] = $this->tool->getEscapedXmlFile();
        }
        return $args;
    }
}
