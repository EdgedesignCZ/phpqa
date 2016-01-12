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
    /** @var boolean */
    public $hasReport;

    public function __construct(array $options)
    {
        $this->analyzedDir = '"' . $options['analyzedDir'] . '"';
        $this->buildDir = $options['buildDir'];
        $this->ignore = new IgnoredPaths($options['ignoredDirs'], $options['ignoredFiles']);
        $this->isSavedToFiles = $options['output'] == 'file';
        $this->isOutputPrinted = $this->isSavedToFiles ? $options['verbose'] : true;
        $this->hasReport = $this->isSavedToFiles ? $options['report'] : false;
        $tools = $this->isSavedToFiles ? $options['tools'] : str_replace('pdepend', '', $options['tools']);
        $this->allowedTools = explode(',', $tools);
    }

    public function filterTools(array $tools)
    {
        $allowed = array();
        foreach ($tools as $tool => $value) {
            if (in_array($tool, $this->allowedTools)) {
                $allowed[$tool] = $value;
            }
        }
        return $allowed;
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
