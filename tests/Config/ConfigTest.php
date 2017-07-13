<?php

namespace Edge\QA;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadDefaultConfig()
    {
        $config = new Config();
        assertThat($config->value('phpcpd.minLines'), is(greaterThan(0)));
        assertThat($config->value('phpcpd.minTokens'), is(greaterThan(0)));
        assertThat($config->value('phpcs.standard'), is(nonEmptyString()));
        assertThat($config->value('phpcs.ignoreWarnings'), identicalTo(false));
        assertThat($config->value('phpcs.reports.cli'), is(nonEmptyArray()));
        assertThat($config->value('phpcs.reports.file'), is(nonEmptyArray()));
        assertThat($config->value('php-cs-fixer.rules'), is(nonEmptyString()));
        assertThat($config->value('php-cs-fixer.isDryRun'), identicalTo(true));
        assertThat($config->value('php-cs-fixer.allowRiskyRules'), identicalTo(false));
        assertThat($config->path('php-cs-fixer.config'), is(nullValue()));
        assertThat($config->path('phpmetrics.config'), is(nullValue()));
        assertThat($config->path('phpmd.standard'), is(nonEmptyString()));
        assertThat($config->value('phpstan.level'), identicalTo(0));
        assertThat($config->value('phpunit.config'), is(nullValue()));
        assertThat($config->value('phpunit.reports.file'), is(emptyArray()));
    }

    public function testBuildAbsolutePath()
    {
        $config = new Config();
        assertThat($config->path('phpmd.standard'), not(startsWith('app/')));
    }

    public function testOverrideDefaultConfig()
    {
        $config = new Config();
        $config->loadCustomConfig(__DIR__);
        assertThat($config->value('phpcpd.minLines'), is(5));
        assertThat($config->value('phpcpd.minTokens'), is(70));
        assertThat($config->value('phpcs.standard'), is('Zend'));
        assertThat($config->path('phpmd.standard'), is(__DIR__ . "/my-standard.xml"));
    }

    public function testIgnoreNonExistentUserConfig()
    {
        $directoryWithoutConfig = __DIR__ . '/../';
        $config = new Config();
        $this->shouldStopPhpqa();
        $config->loadCustomConfig($directoryWithoutConfig, true);
    }

    public function testThrowExceptionWhenFileDoesNotExist()
    {
        $config = new Config();
        $config->loadCustomConfig(__DIR__);
        $this->shouldStopPhpqa();
        $config->path('phpcs.standard');
    }

    public function testConfigCsvString()
    {
        $config = new Config();
        $config->loadCustomConfig(__DIR__);
        $extensions = $config->csv('extensions');
        assertThat($extensions, equalTo('php,inc,module'));
    }

    private function shouldStopPhpqa()
    {
        $this->setExpectedException('Exception');
    }
}
