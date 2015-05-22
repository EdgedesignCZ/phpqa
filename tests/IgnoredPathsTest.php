<?php

class IgnoredPathsTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider provideTools */
    public function testNoOptionWhenNothingIsIgnored($tool)
    {
        $paths = new IgnoredPaths('', '');
        assertThat($paths->$tool(), is(emptyString()));
    }

    /** @dataProvider provideTools */
    public function testShouldIgnoreDirectories($tool, $expectedOption)
    {
        $paths = new IgnoredPaths('bin,vendor', 'autoload.php,RoboFile.php');
        assertThat($paths->$tool(), is($expectedOption));
    }

    public function provideTools()
    {
        return array(
            array('phpcs', ' --ignore=*/bin/*,*/vendor/*,autoload.php,RoboFile.php'),
            array('pdepend', ' --ignore=/bin/,/vendor/,/autoload.php,/RoboFile.php'),
            array('phpmd', ' --exclude /bin/,/vendor/,/autoload.php,/RoboFile.php'),
            array('phpmetrics', ' --excluded-dirs="bin|vendor|autoload.php|RoboFile.php"'),
            array('bergman', ' --exclude=bin --exclude=vendor --exclude=autoload.php --exclude=RoboFile.php')
        );
    }
}
