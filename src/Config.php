<?php

namespace Edge\QA;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $configDir;
    private $defaultConfig;

    private $overridenDir;
    private $overridenConfig = array();

    public function __construct()
    {
        $this->configDir = __DIR__ . '/../';
        $config = file_get_contents("{$this->configDir}.phpqa.yml");
        $this->defaultConfig = Yaml::parse($config);
    }

    public function loadCustomConfig($configDir)
    {
        $this->overridenDir = $configDir . '/';
        $config = file_get_contents("{$this->overridenDir}.phpqa.yml");
        $this->overridenConfig = Yaml::parse($config);
    }

    public function value($path)
    {
        list($dir, $value) = $this->get($path);
        return $value;
    }

    public function path($path)
    {
        list($dir, $file) = $this->get($path);
        return realpath("{$dir}{$file}");
    }

    private function get($path)
    {
        $overriden = $this->getValue($this->overridenConfig, $path);
        return $overriden ?
            array($this->overridenDir, $overriden) :
            array($this->configDir, $this->getValue($this->defaultConfig, $path));
    }

    private function getValue(array $config, $path)
    {
        $result = $config;
        foreach (explode('.', $path) as $key) {
            if (!array_key_exists($key, $result)) {
                return null;
            }
            $result = $result[$key];
        }
        return $result;
    }
}
