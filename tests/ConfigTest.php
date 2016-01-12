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
}
