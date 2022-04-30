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
    }

    public function provideConfig()
    {
        return [
            'No config' => [
                'existingConfig' => [],
                'expectedConfig' => [
                    'parameters' => [
                        'autoload_directories' => $this->autoloadDirectories,
                        'excludePaths' => [
                            'analyseAndScan' => $this->ignoredPaths,
                        ],
                    ],
                ],
            ],
            'No parameters in config' => [
                'existingConfig' => [
                    'includes' => $this->autoloadDirectories,
                ],
                'expectedConfig' => [
                    'includes' => $this->autoloadDirectories,
                    'parameters' => [
                        'excludePaths' => [
                            'analyseAndScan' => $this->ignoredPaths,
                        ],
                    ],
                ],
            ],
            'excludePaths shortcut' => [
                'existingConfig' => [
                    'parameters' => [
                        'excludePaths' => [
                            'File.php',
                        ],
                    ],
                ],
                'expectedConfig' => [
                    'parameters' => [
                        'excludePaths' => [
                            'File.php',
                            'Test.php',
                        ],
                    ],
                ],
            ],
            'excludePaths + analyseAndScan' => [
                'existingConfig' => [
                    'parameters' => [
                        'excludePaths' => [
                            'analyseAndScan' => [
                                'File.php',
                            ],
                        ],
                    ],
                ],
                'expectedConfig' => [
                    'parameters' => [
                        'excludePaths' => [
                            'analyseAndScan' => [
                                'File.php',
                                'Test.php',
                            ],
                        ],
                    ],
                ],
            ],
            'Deprecated excludes_analyse' => [
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
