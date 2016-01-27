<?php

namespace Edge\QA;

class Options
{
    /** @var string */
    public $analyzedDir;
    /** @var string */
    public $buildDir;
    /** @var string */
    public $configDir;
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

    /** @var boolean */
    public $isParallel;

    public function __construct(array $options)
    {
        $this->ignore = new IgnoredPaths($options['ignoredDirs'], $options['ignoredFiles']);
        $this->loadOutput($options);
        $this->loadTools($options['tools']);
    }

    private function loadOutput(array $options)
    {
        $this->analyzedDir = '"' . $options['analyzedDir'] . '"';
        $this->buildDir = $options['buildDir'];
        $this->isParallel = $options['execution'] == 'parallel';
        $this->isSavedToFiles = $options['output'] == 'file';
        $this->isOutputPrinted = $this->isSavedToFiles ? $options['verbose'] : true;
        $this->hasReport = $this->isSavedToFiles ? $options['report'] : false;
        $this->configDir = $options['config'] ? $options['config'] : getcwd();
    }

    private function loadTools($inputTools)
    {
        $tools = $this->isSavedToFiles ? $inputTools : str_replace('pdepend', '', $inputTools);
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
        return escapePath($this->rawFile($file));
    }

    public function rawFile($file)
    {
        return "{$this->buildDir}/{$file}";
    }
}
