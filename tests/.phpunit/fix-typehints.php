<?php

if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
    return;
}

$files = [
    'OptionsTest.php',
    'Report/ReportTest.php',
];
foreach ($files as $file) {
    $path = __DIR__ . "/../{$file}";
    $compatiblePhp = str_replace(
        [
            'function setUp(): void',
            'function tearDown(): void',
        ],
        [
            'function setUp()',
            'function tearDown()',
        ],
        file_get_contents($path)
    );
    file_put_contents($path, $compatiblePhp);
}
