<?php

namespace Edge\QA;

class Options
{
    /** @var string */
    public $analyzedDir;
    /** @var string */
    public $buildDir;
    /** @var IgnoredPaths */
    public $ignore;
    /** @var array */
    private $allowedTools;

    /** @var boolean */
    public $isSavedToFiles;
    /** @var boolean */
    public $isOutputPrinted;

    public function __construct(array $options)
    {
        $this->analyzedDir = '"' . $options['analyzedDir'] . '"';
        $this->buildDir = $options['buildDir'];
        $this->ignore = new IgnoredPaths($options['ignoredDirs'], $options['ignoredFiles']);
        $this->isSavedToFiles = $options['output'] == 'file';
        $this->isOutputPrinted = $this->isSavedToFiles ? $options['verbose'] : true;
        $tools = $this->isSavedToFiles ? $options['tools'] : str_replace('pdepend', '', $options['tools']);
        $this->allowedTools = explode(',', $tools);
    }

    public function isToolAllowed($tool)
    {
        return in_array($tool, $this->allowedTools);
    }

    public function toFile($file)
    {
        return "\"{$this->buildDir}/{$file}\"";
    }

    public function appFile($file)
    {
        return __DIR__ . "/../app/{$file}";
    }
}
