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

    /** @dataProvider provideCliVersion */
    public function testCliVersion($cliOutput, $expectedVersion)
    {
        assertThat(GetVersions::extractVersionFromConsole($cliOutput), is($expectedVersion));
    }

    public function provideCliVersion()
    {
        return [
            'semver at the end' => ['irrelevant 0.12.86', '0.12.86'],
            'semver at the start' => ['0.12.86 irrelevant', '0.12.86'],
            'semver at 2nd line' => ["first\nirrele 4.8.36 vant\nsecond", '4.8.36'],
            'custom format' => ['irrelevant v1.10', '1.10'],
            'no space' => ['v1.13.7irrelevant', '1.13.7'],
            'dev version' => ['irrelevant 4.x-dev@', '4.x'],
            'prefer semver (semver + dev)' => ['v1.13.7 4.x-dev@', '1.13.7'],
            'prefer semver (dev + semver)' => ['4.x-dev@ v1.13.7', '1.13.7'],
            'no version' => ['irrelevant text', 'irrelevant text'],
        ];
    }

    /** @dataProvider provideComparedVersions */
    public function testCompareVersions($toolVersion, $operator, $version, $expectedResult)
    {
        assertThat(GetVersions::compareVersions($toolVersion, $operator, $version), is($expectedResult));
    }

    public function provideComparedVersions()
    {
        return [
            'no version' => ['', '>', '1', false],
            'is lower?' => ['7', '<', '6', false],
            'is greater than dev version' => ['4', '>=', '4.x', true],
            'semver is greater than' => ['4.1', '>=', '4', true],
            'dev version is greater than' => ['4.x', '>=', '4', false], // TODO: true
        ];
    }
}
