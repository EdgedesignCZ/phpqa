<?php

namespace Edge\QA\Tool;

class Psalm extends Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'xml' => ['psalm.xml'],
        'errorsXPath' => '//item/severity[text()=\'error\']',
        'composer' => 'vimeo/psalm',
        'internalClass' => 'Psalm\Checker\ProjectChecker',
        'handler' => 'Edge\QA\Tool\Psalm',
    );

    public function __invoke()
    {
        if (!$this->config->value('psalm.config')) {
            $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__.'/../../app/'));
            $psalmXml = $twig->render(
                'psalm.xml.twig',
                array(
                    'includes' => $this->options->getAnalyzedDirs(),
                    'excludes' => $this->options->ignore->psalm()
                )
            );
        } else {
            $psalmXml = file_get_contents($this->config->path('psalm.config'));
        }

        $psalmFile = $this->saveDynamicConfig($psalmXml, 'xml');

        $args = array(
            'config' => $psalmFile,
            'show-info' => $this->config->value('psalm.showInfo') ? 'true' : 'false',
        );
        if ($this->options->isSavedToFiles) {
            $args['report'] = $this->options->toFile('psalm.xml');
        }
        if ($this->config->value('psalm.deadCode')) {
            $args['find-dead-code'] = '';
        }
        if ($this->options->isParallel && ((int) $this->config->value('psalm.threads')) > 1) {
            $args['threads'] = $this->config->value('psalm.threads');
        }

        return $args;
    }
}
