<?php

class IgnoredPathsTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider provideTools */
    public function testNoOptionWhenNothingIsIgnored($tool)
    {
        $paths = new IgnoredPaths('');
        assertThat($paths->$tool(), is(emptyString()));
    }

    /** @dataProvider provideTools */
    public function testShouldIgnoreDirectories($tool, $expectedOption)
    {
        $paths = new IgnoredPaths('bin,vendor');
        assertThat($paths->$tool(), is($expectedOption));
    }

    public function provideTools()
    {
        return array(
            array('phpcs', ' --ignore=*/bin/*,*/vendor/*'),
            array('pdepend', ' --ignore=/bin/,/vendor/'),
            array('phpmd', ' --exclude /bin/,/vendor/'),
            array('phpmetrics', ' --excluded-dirs="bin|vendor"'),
            array('bergman', ' --exclude=bin --exclude=vendor')
        );
    }
}
