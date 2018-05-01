<?php

namespace Edge\QA;

/** @SuppressWarnings(PHPMD.TooManyPublicMethods) */
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
        return array_filter(explode(',', $csv), 'trim');
    }

    public function listOfValue($optionKey, $optionSeparator, $valuesSeparator, $dirPattern = '%s', $filePattern = '%s')
    {
        $dirs = array_map(function ($dir) use ($dirPattern) {
            return sprintf($dirPattern, $dir);
        }, $this->ignoreDirs);
        $files = array_map(function ($file) use ($filePattern) {
            return sprintf($filePattern, $file);
        }, $this->ignoreFiles);

        if (count($dirs) === 0 && count($files) === 0) {
            return '';
        }

        return ' ' . $optionKey . $optionSeparator . implode($valuesSeparator, array_merge($dirs, $files));
    }

    public function listOfOption($optionKey, $optionSeparator, $dirPattern = '%s', $filePattern = '%s')
    {
        return $this->listOfValue(
            $optionKey,
            $optionSeparator,
            ' ' . $optionKey . $optionSeparator,
            $dirPattern,
            $filePattern
        );
    }

    public function phpcs()
    {
        return $this->listOfValue('--ignore', '=', ',', '*/%s/*');
    }

    public function pdepend()
    {
        if ($this->isWindows) {
            return $this->pdependWindowsFilter('ignore');
        }
        return $this->listOfValue('--ignore', '=', ',', '/%s/', '/%s');
    }

    public function phpmd()
    {
        if ($this->isWindows) {
            return $this->pdependWindowsFilter('exclude');
        }
        return $this->listOfValue('--exclude', ' ', ',', '/%s/', '/%s');
    }

    private function pdependWindowsFilter($option)
    {
        return $this->listOfValue('--' . $option, '=', ',', '%s\*');
    }

    public function phpmetrics()
    {
        $result = $this->listOfValue('--excluded-dirs', '="', '|');
        return empty($result)? '' : $result . '"';
    }

    public function phpmetrics2()
    {
        $result = $this->listOfValue('--exclude', '="', ',');
        return empty($result)? '' : $result . '"';
    }

    public function bergmann()
    {
        return $this->listOfOption('--exclude', '=');
    }

    public function parallelLint()
    {
        return $this->listOfOption('--exclude', ' ');
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
}
