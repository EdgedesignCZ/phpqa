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
            $this->options->ignore->bergmann(),
            $this->options->getAnalyzedDirs(' '),
            'min-lines' => $this->config->value('phpcpd.minLines'),
            'min-tokens' => $this->config->value('phpcpd.minTokens'),
        );
        $isOlderVersion = $this->toolVersionIs('<', '6');
        $phpcpdNames = array_map(
            function ($extension) use ($isOlderVersion) {
                return $isOlderVersion ? "*.{$extension}" : ".{$extension}";
            },
            array_filter(explode(',', $this->config->csv('phpqa.extensions')))
        );
        if ($isOlderVersion) {
            $args['progress'] = '';
        }
        if ($phpcpdNames) {
            $namesOptions = $isOlderVersion ? 'names' : 'suffix';
            $args[$namesOptions] = \Edge\QA\escapePath(implode(',', $phpcpdNames));
        }
        if ($this->options->isSavedToFiles) {
            $args['log-pmd'] = $this->tool->getEscapedXmlFile();
        }
        return $args;
    }
}
