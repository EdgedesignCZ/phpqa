<?php

namespace Edge\QA;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldLoadDefaultConfig()
    {
        $config = new Config();
        assertThat($config->value('phpcpd.minLines'), is(greaterThan(0)));
        assertThat($config->value('phpcpd.minTokens'), is(greaterThan(0)));
        assertThat($config->value('phpcs.standard'), is(nonEmptyString()));
        assertThat($config->path('phpmd.standard'), is(nonEmptyString()));
    }

    public function testShouldBuildAbsolutePath()
    {
        $config = new Config();
        assertThat($config->path('phpmd.standard'), not(startsWith('app/')));
    }

    public function testShouldOverrideDefaultConfig()
    {
        $config = new Config();
        $config->loadCustomConfig(__DIR__);
        assertThat($config->value('phpcpd.minLines'), is(1));
        assertThat($config->value('phpcpd.minTokens'), is(70));
        assertThat($config->value('phpcs.standard'), is('PSR2'));
        assertThat($config->path('phpmd.standard'), is(__DIR__ . "/my-standard.xml"));
    }

    public function testShouldIgnoreNonExistenConfig()
    {
        $directoryWithoutConfig = __DIR__ . '/../';
        $config = new Config();
        $config->loadCustomConfig($directoryWithoutConfig);
        assertThat($config->value('phpcs.standard'), is(nonEmptyString()));
    }
}
