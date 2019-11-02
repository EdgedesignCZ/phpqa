<?php

namespace Edge\QA;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $configs = array();
    private $cwd;

    public function __construct($cwd = null)
    {
        $this->loadConfig(__DIR__ . '/../');
        $this->cwd = $cwd ?: getcwd();
    }

    public function loadUserConfig($directories)
    {
        /** @var string[] $directoriesArray */
        $directoriesArray = array_filter(explode(',', $directories ?: $this->cwd));
        foreach ($directoriesArray as $directory) {
            $this->loadConfig($directory, $directory != $this->cwd);
        }
        $this->mergeConfigs('tool');
    }

    private function loadConfig($directory, $isUserDirectory = false)
    {
        $configDir = $directory . '/';
        $configFile = "{$configDir}.phpqa.yml";
        if (file_exists($configFile)) {
            $config = Yaml::parse((string) file_get_contents($configFile));
            $this->configs = array_merge(
                array($configDir => $config),
                $this->configs
            );
        } elseif ($isUserDirectory) {
            $this->throwInvalidPath('.phpqa.yml', $configFile);
        }
    }

    // better solution would be dynamic merge in findInConfig
    // right now it cannot be used for 'report' because it would path would return invalid file
    private function mergeConfigs($key)
    {
        $mergedConfig = [];
        $userConfig = key($this->configs);
        foreach ($this->configs as $config) {
            $mergedConfig = array_merge(isset($config[$key]) ? $config[$key] : [], $mergedConfig);
        }
        $this->configs[$userConfig][$key] = $mergedConfig;
    }

    public function getCustomBinary($tool)
    {
        $binary = $this->path("{$tool}.binary");
        if ($binary) {
            $filename = basename($binary);
            if (is_bool(strpos($filename, "{$tool}"))) {
                throw new \RuntimeException("Invalid '{$tool}' binary ('{$tool}' not found in '{$binary}')");
            }
            return escapePath($binary);
        }
        return null;
    }

    public function value($path)
    {
        return $this->get(
            $path,
            function ($value) {
                return $value;
            }
        );
    }

    public function path($path)
    {
        return $this->get(
            $path,
            function ($file, $dir) use ($path) {
                $realpath = realpath("{$dir}{$file}");
                if (!$realpath) {
                    $this->throwInvalidPath($path, "{$dir}{$file}");
                }
                return $realpath;
            }
        );
    }

    public function pathsOrValues($path)
    {
        return $this->get(
            $path,
            function ($values, $dir) {
                return array_map(
                    function ($pathOrValue) use ($dir) {
                        $realpath = realpath("{$dir}{$pathOrValue}");
                        return $realpath ? $realpath : $pathOrValue;
                    },
                    (array) $values
                );
            }
        );
    }

    public function csv($path, $separator = ',')
    {
        $csv = function ($path) use ($separator) {
            return $this->get(
                $path,
                function ($value) use ($separator) {
                    return is_array($value) ? implode($separator, $value) : $value;
                }
            );
        };
        if ($path == 'phpqa.extensions') {
            // hotfix for loading legacy extensions without BC
            return $csv(str_replace('phpqa.', '', $path)) ?: $csv($path);
        }
        return $csv($path);
    }

    private function get($path, $extractor)
    {
        foreach ($this->configs as $dir => $config) {
            $value = $this->findInConfig($config, $path);
            if ($value !== null) {
                return $extractor($value, $dir);
            }
        }
    }

    private function findInConfig(array $config, $path)
    {
        $result = $config;
        foreach (explode('.', $path) as $key) {
            if (!is_array($result) || !array_key_exists($key, $result)) {
                return null;
            }
            $result = $result[$key];
        }
        return $result;
    }

    private function throwInvalidPath($source, $path)
    {
        throw new \RuntimeException("Invalid {$source} - '{$path}' does not exist.");
    }
}
