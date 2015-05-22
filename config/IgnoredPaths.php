<?php

class IgnoredPaths
{
    private $ignoredDirs;

    public function __construct($ignoredDirs)
    {
        $this->ignoredDirs = array_filter(explode(',', $ignoredDirs));
    }

    public function phpcs()
    {
        return $this->ignore(' --ignore=*/{IMPLODE}/*', '/*,*/');
    }

    public function pdepend()
    {
        return $this->ignore(' --ignore=/{IMPLODE}/', '/,/');
    }

    public function phpmd()
    {
        return $this->ignore(' --exclude /{IMPLODE}/', '/,/');
    }

    public function phpmetrics()
    {
        return $this->ignore(' --excluded-dirs="{IMPLODE}"', '|');
    }

    public function bergman()
    {
        return $this->ignore(' --exclude={IMPLODE}', ' --exclude=');
    }

    private function ignore($template, $imploder)
    {
        if ($this->ignoredDirs) {
            return str_replace(
                '{IMPLODE}',
                implode($imploder, $this->ignoredDirs),
                $template
            );
        }
        return '';
    }
}
