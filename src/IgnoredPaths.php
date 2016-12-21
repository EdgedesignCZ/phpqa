<?php

namespace Edge\QA;

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
        return array_filter(explode(',', $csv), 'trim');
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

    public function bergmann()
    {
        return $this->ignore(' --exclude=', ' --exclude=', '');
    }

    public function parallelLint()
    {
        return $this->ignore(' --exclude ', ' --exclude ', '');
    }

    public function phpstan()
    {
        return $this->ignoreBoth;
    }

    private function ignore($before, $dirSeparator, $after, $fileSeparator = null)
    {
        if ($fileSeparator) {
            if ($this->ignoreDirs) {
                $files = $this->implode($this->ignoreFiles, $fileSeparator, $fileSeparator);
                return $this->implode($this->ignoreDirs, $before, $dirSeparator, "{$after}{$files}");
            } else {
                $ignoredFiles = $this->implode($this->ignoreFiles, $before, $fileSeparator);
                return str_replace('*/', '', $ignoredFiles); // phpcs hack
            }
        } else {
            return $this->implode($this->ignoreBoth, $before, $dirSeparator, $after);
        }
    }

    private function implode(array $input, $before, $separator, $after = '')
    {
        return $input && $separator ? ($before . implode($separator, $input) . $after) : '';
    }
}
