<?php

class IgnoredPaths
{
    private $ignoreDirs;
    private $ignoreFiles;
    private $ignoreBoth;

    public function __construct($ignoredDirs, $ignoredFiles)
    {
        $this->ignoreDirs = $this->csvToArray($ignoredDirs);
        $this->ignoreFiles = $this->csvToArray($ignoredFiles);
        $this->ignoreBoth = array_merge($this->ignoreDirs, $this->ignoreFiles);
    }

    private function csvToArray($csv)
    {
        return array_filter(explode(',', $csv));
    }

    public function phpcs()
    {
        return $this->ignore(' --ignore=*/', '/*,*/', '/*', ',');
    }

    public function pdepend()
    {
        return $this->ignore(' --ignore=/', '/,/', '/', ',/');
    }

    public function phpmd()
    {
        return $this->ignore(" --exclude /", '/,/', '/', ',/');
    }

    public function phpmetrics()
    {
        return $this->ignore(' --excluded-dirs="', '|', '"');
    }

    public function bergman()
    {
        return $this->ignore(' --exclude=', ' --exclude=', '');
    }

    private function ignore($before, $dirSeparator, $after, $fileSeparator = null)
    {
        $input = $fileSeparator ? $this->ignoreDirs : $this->ignoreBoth;
        if ($input) {
            return $this->implode($input, $before, $dirSeparator, "{$after}{$this->files($fileSeparator)}");
        }
        return '';
    }

    private function files($separator)
    {
        return $this->implode($this->ignoreFiles, $separator, $separator);
    }

    private function implode(array $input, $before, $separator, $after = '')
    {
        return $input && $separator ? ($before . implode($separator, $input) . $after) : '';
    }
}
