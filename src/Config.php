<?php

namespace Edge\QA;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $configs = array();

    public function __construct()
    {
        $this->loadConfig(__DIR__ . '/../');
    }

    public function loadUserConfig($directory)
    {
        $this->loadConfig($directory ?: getcwd(), $directory);
    }

    private function loadConfig($directory, $isUserDirectory = false)
    {
        $configDir = $directory . '/';
        $configFile = "{$configDir}.phpqa.yml";
        if (file_exists($configFile)) {
            $config = Yaml::parse(file_get_contents($configFile));
            $this->configs = array_merge(
                array($configDir => $config),
                $this->configs
            );
        } elseif ($isUserDirectory) {
            $this->throwInvalidPath('.phpqa.yml', $configFile);
        }
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

    public function csv($path)
    {
        return $this->get(
            $path,
            function ($value) {
                return (is_array($value) ? implode(',', $value) : $value);
            }
        );
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
