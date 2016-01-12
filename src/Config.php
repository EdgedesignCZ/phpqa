<?php

namespace Edge\QA;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $configDir;
    private $defaultConfig;

    public function __construct()
    {
        $this->configDir = __DIR__ . '/../';
        $config = file_get_contents("{$this->configDir}.phpqa.yml");
        $this->defaultConfig = Yaml::parse($config);
    }

    public function value($path)
    {
        $result = $this->defaultConfig;
        foreach (explode('.', $path) as $key) {
            $result = $result[$key];
        }
        return $result;
    }

    public function path($path)
    {
        $file = $this->value($path);
        return realpath("{$this->configDir}{$file}");
    }
}
