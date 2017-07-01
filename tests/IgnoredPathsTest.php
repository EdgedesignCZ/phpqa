<?php

namespace Edge\QA;

class IgnoredPathsTest extends \PHPUnit_Framework_TestCase
{
    private $operatingSystem = 'Linux';

    private function ignore($tool, $dirs, $files)
    {
        $paths = new IgnoredPaths($dirs, $files);
        $paths->setOS($this->operatingSystem);
        return $paths->$tool();
    }

    /** @dataProvider provideTools */
    public function testNoOptionWhenNothingIsIgnored($tool)
    {
        assertThat($this->ignore($tool, '', ' '), is(emptyString()));
    }

    /** @dataProvider provideTools */
    public function testIgnoreDirectoriesAndFiles($tool, $expectedOptions, $os = null)
    {
        $this->operatingSystem = $os ?: $this->operatingSystem;
        $this->assertEquals(
            $expectedOptions,
            [
                'both' => $this->ignore($tool, 'bin,vendor', 'autoload.php,RoboFile.php'),
                'dirs' => $this->ignore($tool, 'bin,vendor', ''),
                'files' => $this->ignore($tool, '', 'autoload.php,RoboFile.php'),
            ]
        );
    }

    public function provideTools()
    {
        return array(
            array(
                'phpcs',
                array(
                    'both' => ' --ignore=*/bin/*,*/vendor/*,autoload.php,RoboFile.php',
                    'dirs' => ' --ignore=*/bin/*,*/vendor/*',
                    'files' => ' --ignore=autoload.php,RoboFile.php'
                )
            ),
            array(
                'pdepend',
                array(
                    'both' => ' --ignore=/bin/,/vendor/,/autoload.php,/RoboFile.php',
                    'dirs' => ' --ignore=/bin/,/vendor/',
                    'files' => ' --ignore=/autoload.php,/RoboFile.php'
                )
            ),
            array(
                'phpmd',
                array(
                    'both' => ' --exclude /bin/,/vendor/,/autoload.php,/RoboFile.php',
                    'dirs' => ' --exclude /bin/,/vendor/',
                    'files' => ' --exclude /autoload.php,/RoboFile.php'
                )
            ),
            'pdepend + windows' => array(
                'pdepend',
                array(
                    'both' => ' --ignore=bin\*,vendor\*,autoload.php,RoboFile.php',
                    'dirs' => ' --ignore=bin\*,vendor\*',
                    'files' => ' --ignore=autoload.php,RoboFile.php'
                ),
                'Windows'
            ),
            'phpmd + windows' => array(
                'phpmd',
                array(
                    'both' => ' --exclude=bin\*,vendor\*,autoload.php,RoboFile.php',
                    'dirs' => ' --exclude=bin\*,vendor\*',
                    'files' => ' --exclude=autoload.php,RoboFile.php'
                ),
                'WIN32'
            ),
            array(
                'phpmetrics',
                array(
                    'both' => ' --excluded-dirs="bin|vendor|autoload.php|RoboFile.php"',
                    'dirs' => ' --excluded-dirs="bin|vendor"',
                    'files' => ' --excluded-dirs="autoload.php|RoboFile.php"'
                )
            ),
            array(
                'phpmetrics2',
                array(
                    'both' => ' --exclude="bin,vendor,autoload.php,RoboFile.php"',
                    'dirs' => ' --exclude="bin,vendor"',
                    'files' => ' --exclude="autoload.php,RoboFile.php"'
                )
            ),
            array(
                'bergmann',
                array(
                    'both' => ' --exclude=bin --exclude=vendor --exclude=autoload.php --exclude=RoboFile.php',
                    'dirs' => ' --exclude=bin --exclude=vendor',
                    'files' => ' --exclude=autoload.php --exclude=RoboFile.php'
                )
            ),
            array(
                'parallelLint',
                array(
                    'both' => ' --exclude bin --exclude vendor --exclude autoload.php --exclude RoboFile.php',
                    'dirs' => ' --exclude bin --exclude vendor',
                    'files' => ' --exclude autoload.php --exclude RoboFile.php'
                )
            ),
        );
    }
}
