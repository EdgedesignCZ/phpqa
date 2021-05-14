<?php

namespace Edge\QA;

class IgnoredPaths
{
    private $ignoreDirs;
    private $ignoreFiles;
    private $ignoreBoth;
    private $isWindows;

    public function __construct($ignoredDirs, $ignoredFiles)
    {
        $this->ignoreDirs = $this->csvToArray($ignoredDirs);
        $this->ignoreFiles = $this->csvToArray($ignoredFiles);
        $this->ignoreBoth = array_merge($this->ignoreDirs, $this->ignoreFiles);
        $this->setOS(PHP_OS);
    }

    public function setOS($os)
    {
        $this->isWindows = strtoupper(substr($os, 0, 3)) == 'WIN';
    }

    private function csvToArray($csv)
    {
        return array_filter(explode(',', $csv), function ($value) {
            return (bool) trim($value);
        });
    }

    public function phpcs()
    {
        return $this->ignore(' --ignore=*/', '/*,*/', '/*', ',');
    }

    public function pdepend()
    {
        if ($this->isWindows) {
            return $this->pdependWindowsFilter('ignore=');
        }
        return $this->ignore(' --ignore=/', '/,/', '/', ',/');
    }

    public function phpmd()
    {
        if ($this->isWindows) {
            return $this->pdependWindowsFilter('exclude ');
        }
        return $this->ignore(" --exclude /", '/,/', '/', ',/');
    }

    private function pdependWindowsFilter($option)
    {
        return str_replace('/', '\\', $this->ignore(" --{$option}", '\*,', '\*', ','));
    }

    public function phpmetrics()
    {
        return $this->ignore(' --excluded-dirs="', '|', '"');
    }

    public function phpmetrics2()
    {
        return $this->ignore(' --exclude="', ',', '"');
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

    public function psalm()
    {
        return array(
            'file' => $this->ignoreFiles,
            'directory' => $this->ignoreDirs,
        );
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
