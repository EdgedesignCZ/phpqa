<?php

namespace Edge\QA\Tools;

class GetVersionsTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider provideComposerVersion */
    public function testNormalizeVersion($version, $expectedVersion)
    {
        assertThat(GetVersions::normalizeSemver($version), is($expectedVersion));
    }

    public function provideComposerVersion()
    {
        return [
            'full semver' => ['2.7.4', '2.7.4'],
            'minor release' => ['2.7.0', '2.7'],
            'major release' => ['2.0.0', '2.0'],
        ];
    }
}
