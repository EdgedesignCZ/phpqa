<?php

namespace Edge\QA;

class PathsTest extends \PHPUnit_Framework_TestCase
{
    public function testPathToBinaryIsEscaped()
    {
        define('COMPOSER_BINARY_DIR', '/home/user with space/phpqa/vendor/bin');
        $tool = 'irrelevant';
        assertThat(buildToolBinary($tool, __FILE__), allOf(startsWith('"'), endsWith('"')));
        assertThat(buildToolBinary($tool, 'not-installed-tool'), is(''));
    }
}
