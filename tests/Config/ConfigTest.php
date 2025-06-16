<?php

namespace Edge\QA;

/** @SuppressWarnings(PHPMD.TooManyPublicMethods) */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private $defaultToolsCount = 13;

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
        assertThat($config->value('phpmd.ignoreParsingErrors'), is(true));
        assertThat($config->value('phpstan.level'), identicalTo(0));
        assertThat($config->value('phpstan.memoryLimit'), is(nullValue()));
        assertThat($config->value('phpstan.errorFormat'), is('checkstyle'));
        assertThat($config->value('phpunit.config'), is(nullValue()));
        assertThat($config->value('phpunit.reports.file'), is(emptyArray()));
        assertThat($config->value('psalm.config'), is(nonEmptyString()));
        assertThat($config->value('psalm.deadCode'), identicalTo(false));
        assertThat($config->value('psalm.threads'), identicalTo(1));
        assertThat($config->value('psalm.showInfo'), identicalTo(true));
        assertThat($config->value('psalm.memoryLimit'), is(nullValue()));
        assertThat($config->value('phpmetrics.config'), is(nullValue()));
        assertThat($config->value('phpmetrics.junit'), is(nullValue()));
        assertThat($config->value('phpmetrics.composer'), is(nullValue()));
        assertThat($config->value('phpmetrics.git'), identicalTo(false));
        assertThat($config->value('pdepend.coverageReport'), is(nullValue()));
        assertThat($config->value('deptrac.depfile'), is(nullValue()));
        assertThat($config->value('deptrac.reportUncovered'), is(true));
        assertThat($config->value('security-checker.composerLock'), is(nullValue()));
    }

    public function testBuildAbsolutePath()
    {
        $config = new Config();
        assertThat($config->path('phpmd.standard'), not(startsWith('app/')));
    }

    public function testOverrideDefaultConfig()
    {
        $config = new Config();
        $config->loadUserConfig(__DIR__);
        assertThat($config->value('phpcpd.minLines'), is(5));
        assertThat($config->value('phpcpd.minTokens'), is(70));
        assertThat($config->value('phpcs.standard'), is('Zend'));
        assertThat($config->path('phpmd.standard'), is(__DIR__ . DIRECTORY_SEPARATOR . 'my-standard.xml'));
    }

    public function testAllowPartialUpdateOfTools()
    {
        $config = new Config();
        assertThat($config->value('tool'), is(arrayWithSize($this->defaultToolsCount)));
        assertThat($config->value('tool.phpmetrics'), is(arrayWithSize(2)));
        $config->loadUserConfig(__DIR__);
        assertThat($config->value('tool'), is(arrayWithSize($this->defaultToolsCount + 1)));
        assertThat($config->value('tool.phpmetrics'), is(nonEmptyString()));
    }

    public function testIgnoreNonExistentUserConfig()
    {
        $directoryWithoutConfig = __DIR__ . '/../';
        $config = new Config();
        $this->shouldStopPhpqa();
        $config->loadUserConfig($directoryWithoutConfig);
    }

    public function testNoExceptionWhenCwdHasNoConfig()
    {
        $directoryWithoutConfig = __DIR__ . '/../';
        $config = new Config($directoryWithoutConfig);
        $config->loadUserConfig('');
    }

    public function testThrowExceptionWhenFileDoesNotExist()
    {
        $config = new Config();
        $config->loadUserConfig(__DIR__);
        $this->shouldStopPhpqa();
        $config->path('phpcs.standard');
    }

    public function testConfigCsvString()
    {
        $config = new Config();
        $config->loadUserConfig(__DIR__);
        $extensions = $config->csv('phpqa.extensions');
        assertThat($extensions, equalTo('php,inc,module'));
    }

    public function testUseCwdIfNoDirectoryIsSpecified()
    {
        $config = new Config();
        $config->loadUserConfig('');
    }

    public function testIgnoreInvalidBinaryDoesNotExist()
    {
        $config = new Config();
        $config->loadUserConfig(__DIR__);
        assertThat($config->getCustomBinary('phpunit'), is(nullValue()));
    }

    public function testToolAndBinaryNameMightNotMatch()
    {
        $config = new Config();
        $config->loadUserConfig(__DIR__);
        assertThat($config->getCustomBinary('phpmetrics'), is(notNullValue()));
    }

    public function testMultipleConfig()
    {
        $config = new Config();
        $config->loadUserConfig(__DIR__ . ',' . __DIR__ . '/sub-config');

        assertThat($config->value('phpcs.standard'), is('PSR2'));
        assertThat($config->value('phpmd.standard'), is('my-standard.xml'));
        assertThat($config->value('phpcpd.lines'), is(53));
        assertThat($config->csv('phpqa.extensions'), is('php,inc'));
    }

    public function testAutodetectConfigInCurrentDirectory()
    {
        $config = new Config(__DIR__);
        $config->loadUserConfig('');
        assertThat($config->value('phpcs.standard'), is('Zend'));
    }

    public function testIgnoreAutodetectedConfigIfUserConfigIsSpecified()
    {
        $currentDir = __DIR__;
        $config = new Config($currentDir);
        $config->loadUserConfig("{$currentDir},{$currentDir}/sub-config,");
        assertThat($config->value('phpcs.standard'), is('PSR2'));
    }

    private function shouldStopPhpqa()
    {
        if (method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('Exception');
        } else {
            $this->expectException('Exception');
        }
    }
}
