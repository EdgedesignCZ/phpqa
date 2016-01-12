<?php

namespace Edge\QA;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $defaultConfig;

    public function __construct()
    {
        $config = file_get_contents(__DIR__ . '/../.phpqa.yml');
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
        return $this->value($path);
    }
}
