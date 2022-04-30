<?php

namespace Edge\QA\Tools\Analyzer;

use PHPUnit\Framework\TestCase;

class PhpstanTest extends TestCase
{

    private $autoloadDirectories = ['src/'];
    private $ignoredPaths = ['Test.php'];

    /** @dataProvider provideConfig */
    public function testBuildConfig($existingConfig, $expectedConfig)
    {
        $config = Phpstan::buildConfig($existingConfig, $this->autoloadDirectories, $this->ignoredPaths);
        $this->assertEquals($expectedConfig, $config);
        assertThat($config, is($expectedConfig));
    }

    public function provideConfig()
    {
        return [
            'No config' => [
                'existingConfig' => [],
                'expectedConfig' => [
                    'parameters' => [
                        'autoload_directories' => $this->autoloadDirectories,
                        'excludes_analyse' => $this->ignoredPaths,
                    ],
                ],
            ],
            'Deprecated config' => [
                'existingConfig' => [
                    'parameters' => [
                        'reportUnmatchedIgnoredErrors' => false,
                        'excludes_analyse' => [
                            'File.php',
                        ],
                    ],
                ],
                'expectedConfig' => [
                    'parameters' => [
                        'reportUnmatchedIgnoredErrors' => false,
                        'excludes_analyse' => [
                            'File.php',
                            'Test.php',
                        ],
                    ],
                ],
            ],
        ];
    }
}
