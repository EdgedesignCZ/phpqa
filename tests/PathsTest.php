<?php

namespace Edge\QA;

class PathsTest extends \PHPUnit_Framework_TestCase
{
    public function testPathToBinaryIsEscaped()
    {
        define('COMPOSER_BINARY_DIR', '/home/user with space/phpqa/vendor/bin');
        assertThat(pathToBinary('phpcs'), allOf(startsWith('"'), endsWith('"')));
    }
}
