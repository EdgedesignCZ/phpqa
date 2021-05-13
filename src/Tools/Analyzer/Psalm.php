<?php

namespace Edge\QA\Tools\Analyzer;

use SimpleXMLElement;
use DOMDocument;

class Psalm extends \Edge\QA\Tools\Tool
{
    public static $SETTINGS = array(
        'optionSeparator' => '=',
        'xml' => ['psalm.xml'],
        'errorsXPath' => '//item/severity[text()=\'error\']',
        'composer' => 'vimeo/psalm',
    );

    public function __invoke()
    {
        if (!$this->config->value('psalm.config')) {
            $this->writeln("<error>Invalid 'psalm.config'</error>");
        }
        
        $rawXml = file_get_contents($this->config->path('psalm.config'));
        $psalmXml = $this->updateProjectFiles($rawXml);
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
        if (!$this->config->value('psalm.memoryLimit')) {
            $args['use-ini-defaults'] = '';
        } elseif ($this->toolVersionIs('>=', '3.14')) {
            $args['memory-limit'] = $this->config->value('psalm.memoryLimit');
        }

        return $args;
    }

    private function updateProjectFiles($rawXml)
    {
        $xml = new SimpleXMLElement($rawXml);

        $attributes = $xml->attributes();
        if (!isset($attributes['resolveFromConfigFile']) && $this->toolVersionIs('>=', '4')) {
            $xml->addAttribute('resolveFromConfigFile', 'false');
        }

        if (!isset($xml->projectFiles)) {
            $xml->addChild('projectFiles');
        }
        if (!isset($xml->projectFiles->ignoreFiles)) {
            $xml->projectFiles->addChild('ignoreFiles');
        }

        foreach ($this->options->getAnalyzedDirs() as $dir) {
            $xml->projectFiles
                ->addChild('directory')
                ->addAttribute('name', trim($dir, '"'));
        }

        foreach ($this->options->ignore->psalm() as $type => $paths) {
            foreach ($paths as $path) {
                $xml->projectFiles->ignoreFiles
                    ->addChild($type)
                    ->addAttribute('name', trim($path, '"'));
            }
        }

        return $this->simpleXMLToPrettyString($xml);
    }

    private function simpleXMLToPrettyString(SimpleXMLElement $xml)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML((string) $xml->asXML());
        return $dom->saveXML();
    }
}
