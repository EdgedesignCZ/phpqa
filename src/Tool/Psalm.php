<?php

namespace Edge\QA\Tool;

class Psalm extends Tool
{
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

        $psalmDir = rtrim($this->options->isSavedToFiles ? $this->options->rawFile('') : getcwd(), '/');
        $psalmFile = "{$psalmDir}/psalm-phpqa.xml";
        file_put_contents($psalmFile, $psalmXml);

        $args = array(
            'config' => \Edge\QA\escapePath($psalmFile),
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
