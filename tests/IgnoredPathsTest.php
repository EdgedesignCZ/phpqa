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
        if ($tool == 'psalm') {
            assertThat($this->ignore($tool, '', ' '), is(['file' => [], 'directory' => []]));
        } else {
            assertThat($this->ignore($tool, '', ' '), is(emptyString()));
        }
    }

    /** @dataProvider provideTools */
    public function testIgnoreDirectoriesAndFiles($tool, $expectedOptions, $os = null)
    {
        $this->operatingSystem = $os ?: $this->operatingSystem;
        assertThat(
            $expectedOptions,
            is([
                'both' => $this->ignore($tool, 'app/config,vendor', 'autoload.php,RoboFile.php'),
                'dirs' => $this->ignore($tool, 'app/config,vendor', ''),
                'files' => $this->ignore($tool, '', 'autoload.php,RoboFile.php'),
            ])
        );
    }

    public function provideTools()
    {
        return array(
            array(
                'phpcs',
                array(
                    'both' => ' --ignore=*/app/config/*,*/vendor/*,autoload.php,RoboFile.php',
                    'dirs' => ' --ignore=*/app/config/*,*/vendor/*',
                    'files' => ' --ignore=autoload.php,RoboFile.php'
                )
            ),
            array(
                'pdepend',
                array(
                    'both' => ' --ignore=/app/config/,/vendor/,/autoload.php,/RoboFile.php',
                    'dirs' => ' --ignore=/app/config/,/vendor/',
                    'files' => ' --ignore=/autoload.php,/RoboFile.php'
                )
            ),
            array(
                'phpmd',
                array(
                    'both' => ' --exclude /app/config/,/vendor/,/autoload.php,/RoboFile.php',
                    'dirs' => ' --exclude /app/config/,/vendor/',
                    'files' => ' --exclude /autoload.php,/RoboFile.php'
                )
            ),
            'pdepend + windows' => array(
                'pdepend',
                array(
                    'both' => ' --ignore=app\config\*,vendor\*,autoload.php,RoboFile.php',
                    'dirs' => ' --ignore=app\config\*,vendor\*',
                    'files' => ' --ignore=autoload.php,RoboFile.php'
                ),
                'Windows'
            ),
            'phpmd + windows' => array(
                'phpmd',
                array(
                    'both' => ' --exclude=app\config\*,vendor\*,autoload.php,RoboFile.php',
                    'dirs' => ' --exclude=app\config\*,vendor\*',
                    'files' => ' --exclude=autoload.php,RoboFile.php'
                ),
                'WIN32'
            ),
            array(
                'phpmetrics',
                array(
                    'both' => ' --excluded-dirs="app/config|vendor|autoload.php|RoboFile.php"',
                    'dirs' => ' --excluded-dirs="app/config|vendor"',
                    'files' => ' --excluded-dirs="autoload.php|RoboFile.php"'
                )
            ),
            array(
                'phpmetrics2',
                array(
                    'both' => ' --exclude="app/config,vendor,autoload.php,RoboFile.php"',
                    'dirs' => ' --exclude="app/config,vendor"',
                    'files' => ' --exclude="autoload.php,RoboFile.php"'
                )
            ),
            array(
                'bergmann',
                array(
                    'both' => ' --exclude=app/config --exclude=vendor --exclude=autoload.php --exclude=RoboFile.php',
                    'dirs' => ' --exclude=app/config --exclude=vendor',
                    'files' => ' --exclude=autoload.php --exclude=RoboFile.php'
                )
            ),
            array(
                'parallelLint',
                array(
                    'both' => ' --exclude app/config --exclude vendor --exclude autoload.php --exclude RoboFile.php',
                    'dirs' => ' --exclude app/config --exclude vendor',
                    'files' => ' --exclude autoload.php --exclude RoboFile.php'
                )
            ),
            array(
                'psalm',
                array(
                    'both' => [
                        'file' => ['autoload.php', 'RoboFile.php'],
                        'directory' => ['app/config', 'vendor'],
                    ],
                    'dirs' => [
                        'file' => [],
                        'directory' => ['app/config', 'vendor'],
                    ],
                    'files' => [
                        'file' => ['autoload.php', 'RoboFile.php'],
                        'directory' => [],
                    ],
                )
            ),
        );
    }
}
