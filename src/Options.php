<?php

namespace Edge\QA;

class Options
{
    /** @var string[] */
    private $analyzedDirs;
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
        $this->analyzedDirs = array_map(
            function ($dir) {
                return '"' . $dir . '"';
            },
            array_filter(explode(',', $options['analyzedDirs']))
        );
        $this->buildDir = $options['buildDir'];
        $this->isParallel = $options['execution'] == 'parallel';
        $this->isSavedToFiles = $options['output'] == 'file';
        $this->isOutputPrinted = $this->isSavedToFiles ? $options['verbose'] : true;
        $this->hasReport = $this->isSavedToFiles ? $options['report'] : false;
        $this->configDir = $options['config'] ? $options['config'] : getcwd();
    }

    public function getCommonRootPath()
    {
        $paths = array_filter(array_map(
            function ($relativeDir) {
                return realpath(getcwd() . '/' . trim($relativeDir, '"'));
            },
            $this->analyzedDirs
        ));
        $commonPath = commonPath($paths);
        return $commonPath ? "{$commonPath}/" : '';
    }

    public function getAnalyzedDirs($separator = null)
    {
        return $separator ? implode($separator, $this->analyzedDirs) : $this->analyzedDirs;
    }

    private function loadTools($inputTools)
    {
        $tools = $this->isSavedToFiles ? $inputTools : str_replace('pdepend', '', $inputTools);
        $this->allowedTools = array();
        foreach (array_filter(explode(',', $tools)) as $tool) {
            if (is_int(strpos($tool, ':'))) {
                list($name, $allowedErrors) = explode(':', $tool);
            } else {
                $name = $tool;
                $allowedErrors = null;
            }
            $this->allowedTools[$name] = $allowedErrors;
        }
    }

    public function buildRunningTools(array $tools, $hasCustomBinary = null)
    {
        $hasCustomBinary = $hasCustomBinary ?: function () {
            return false;
        };
        $allowed = array();
        foreach ($tools as $tool => $config) {
            if (array_key_exists($tool, $this->allowedTools)) {
                $preload = [
                    'allowedErrorsCount' => $this->allowedTools[$tool],
                    'xml' => array_key_exists('xml', $config) ? array_map([$this, 'rawFile'], $config['xml']) : []
                ];
                $runningTool = new RunningTool($tool, $preload + $config);
                if ($runningTool->isInstalled() || $hasCustomBinary($tool)) {
                    $allowed[$tool] = $runningTool;
                }
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
