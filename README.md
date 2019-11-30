
# PHPQA

Analyze PHP code with one command.

[![License](https://poser.pugx.org/edgedesign/phpqa/license)](https://packagist.org/packages/edgedesign/phpqa)
[![Latest Stable Version](https://poser.pugx.org/edgedesign/phpqa/v/stable)](/CHANGELOG.md)
[![Total Downloads](https://poser.pugx.org/edgedesign/phpqa/downloads)](https://packagist.org/packages/edgedesign/phpqa)
[![Build Status](https://travis-ci.org/EdgedesignCZ/phpqa.svg)](https://travis-ci.org/EdgedesignCZ/phpqa)
[![Windows status](https://ci.appveyor.com/api/projects/status/t9f05uk4cjcg294o?svg=true&passingText=Windows)](https://ci.appveyor.com/project/zdenekdrahos/phpqa)

## Requirements

- PHP >= 5.4.0
- `xsl` extension for [HTML reports](#html-reports)

## Why?

Every analyzer has different arguments and options in different formats *(no surprise in PHP world :)*.
If you ever tried to get ignoring directories to work then you know what I mean. On the other hand
CLI tools are cool because you can analyze any directory or file.
Unfortunately [Jenkins](http://jenkins-php.org/automation.html),
[Travis](https://github.com/libis/plugin-Mailer/blob/095cc1154fd6d7beb3be4425329868ecfa2043d9/.travis.yml),
[Scrutiziner](https://github.com/antonbabenko/imagepush2/blob/db88b1c65a34250ba98e01d584d72421aedfaeac/.scrutinizer.yml)
needs special configuration file. What if you want to analyze every bundle in your Symfony app?
Will you create e.g. Jenkins project/task for each bundle?

* I want to analyze selected directory without complex configuration and creating extra files/tasks
* I don't care about format of [ignored directories](https://github.com/EdgedesignCZ/phpqa/blob/master/tests/IgnoredPathsTest.php) in phploc, phpmd, ...
* I don't want to update all projects when QA tool is updated or if I've found cool tool like [PHPMetrics](https://github.com/Halleck45/PhpMetrics)
* I don't want to analyze XML files → tool should be able to build [html reports](#html-reports)
* I want fast execution time → tools should run in parallel ([thanks Robo](http://robo.li/tasks/Base/#parallelexec))

## Available [tools](https://github.com/ziadoz/awesome-php#code-analysis)

Tool| Description
----------------------------------------------------------------------- | ----------------------------- |
[phploc](https://github.com/sebastianbergmann/phploc) | Measure the size of a PHP project |
[phpcpd](https://github.com/sebastianbergmann/phpcpd) | Copy/Paste Detector (CPD) for PHP code |
[phpcs](https://github.com/squizlabs/PHP_CodeSniffer) | Detect violations of a coding standard |
[pdepend](https://github.com/pdepend/pdepend) | PHP adaptation of JDepend |
[phpmd](https://github.com/phpmd/phpmd) | Scan PHP project for messy code |
[phpmetrics](https://github.com/Halleck45/PhpMetrics) | Static analysis tool for PHP |

##### Suggested tools

Newly added tools aren't preinstalled. You have to install relevant composer packages if
you want to use them.

Tool | PHP | Supported since | Description |
---- | --- | --------------- | ----------- |
[security-checker](https://github.com/sensiolabs/security-checker) | `>= 5.3` | `1.16` | Check composer.lock for known security issues |
[php-cs-fixer](http://cs.sensiolabs.org/) | [`>= 5.3`](https://github.com/EdgedesignCZ/phpqa/pull/66#discussion_r115206573) | `1.12` | Automatically detect and fix PHP coding standards issues |
[phpunit](https://github.com/phpunit/phpunit) | `>= 5.3` | `1.13` | The PHP Unit Testing framework |
[phpstan](https://github.com/phpstan/phpstan) | `>= 7.0` | `1.9` | Discover bugs in your code without running it |
[psalm](https://github.com/vimeo/psalm) | `>= 5.6` | `1.14` | A static analysis tool for finding errors in PHP applications |
[parallel-lint](https://github.com/JakubOnderka/PHP-Parallel-Lint) | `>= 5.4` | `1.9` | Check syntax of PHP files |
[MacFJA/phpqa-extensions](https://github.com/MacFJA/phpqa-extensions) | - | - | PHP Assumptions, Magic Number Detector, ... |

_Tip_: use [`bin/suggested-tools.sh install`](/bin/suggested-tools.sh) for installing the tools.

## Install

### Clone + composer

```bash
# install phpqa
git clone https://github.com/EdgedesignCZ/phpqa.git && cd phpqa && composer install --no-dev

# make phpqa globally accessible
## you can symlink binary
sudo ln -s /path-to-phpqa-repository/phpqa /usr/bin/phpqa
## or add this directory to your PATH in your ~/.bash_profile (or ~/.bashrc)
export PATH=~/path-to-phpqa-repository-from-pwd:$PATH
```

### Composer

```bash
# global installation
composer global require edgedesign/phpqa --update-no-dev
# Make sure you have ~/.composer/vendor/bin/ in your PATH.

# local installation
composer require edgedesign/phpqa --dev
```

Of course you can add dependency to `require-dev` section in your `composer.json`.
But I wouldn't recommend it. In my experience *one* QA tool which analyzes
*N* projects is better than *N* projects with *N* analyzers. It's up to you
how many repositories you want to update when new version is released.

##### Symfony3 components

Symfony3 is supported since [version 1.7](/CHANGELOG.md#v170).
Install at least version `~3.0` of `sebastian/phpcpd`, otherwise you'll get error [`The helper "progress" is not defined.`](https://github.com/EdgedesignCZ/phpqa/issues/19)

```json
{
    "require-dev": {
        "edgedesign/phpqa": ">=1.7",
        "sebastian/phpcpd": "~3.0"
    }
}
```

##### Fake global installation in local project

Do you have problems with dependencies and you can't install phpqa globally?
Install phpqa in [subdirectory](#circleci---artifacts--global-installation).

```bash
#!/bin/sh

if [ ! -f qa/phpqa ];
then
    echo "installing phpqa"
    (git clone https://github.com/EdgedesignCZ/phpqa.git ./qa  && cd qa && composer install --no-dev)
fi

qa/phpqa
```

### Docker

Official docker image is https://hub.docker.com/r/zdenekdrahos/phpqa/.
The image can be used at [Gitlab CI](#gitlabci---docker-installation--composer-cache--artifacts).
Beware that the image is as lean as possible. That can be a problem for running PHPUnit tests.
In that case, you might miss PHP extensions for database etc (_you can [install phpqa](https://gitlab.com/costlocker/integrations/blob/213aab7/.ci/get-phpqa-binary#L40) in another [php image](https://gitlab.com/costlocker/integrations/blob/213aab7/.ci/.gitlab-ci.yml#L28)_). 

```bash
docker run --rm -it zdenekdrahos/phpqa:v1.23.3 phpqa tools
# using a tool without phpqa
docker run --rm -it zdenekdrahos/phpqa:v1.23.3 phploc -v
```

There are also available images [eko3alpha/docker-phpqa](https://hub.docker.com/r/eko3alpha/docker-phpqa/) and [sparkfabrik/docker-phpqa](https://hub.docker.com/r/sparkfabrik/docker-phpqa/).
`phpqa` is used as [an entrypoint](https://docs.docker.com/engine/reference/builder/#entrypoint) (_I haven't been able to use these images at Gitlab CI_).

```bash
docker run --rm -u $UID -v $PWD:/app eko3alpha/docker-phpqa --report --ignoredDirs vendor,build,migrations,test
```



## Analyze

| Command | Description |
| ------- | ----------- |
| `phpqa --help` | Show help - available options, tools, default values, ... |
| `phpqa --analyzedDirs ./ --buildDir ./build` | Analyze current directory and save output to build directory |
| `phpqa --analyzedDirs src,tests` | Analyze source and test directory ([phpmetrics analyzes only `src`](#project-with-multiple-directories-src-tests-)) |
| ~~`phpqa --analyzedDir ./`~~ | Deprecated in **v1.8** in favor of `--analyzedDirs` |
| `phpqa --ignoredDirs build,vendor` | Ignore directories |
| `phpqa --ignoredFiles RoboFile.php` | Ignore files |
| `phpqa --tools phploc,phpcs` | Run only selected tools |
| `phpqa --tools phpmd:1,phpcs:0,phpcpd:0` | Check number of errors and [exit code](#exit-code). **New in v1.6** |
| `phpqa --verbose` | Show output from executed tools |
| `phpqa --quiet` | Show no output at all |
| `phpqa --output cli` | [CLI output](#output-modes) instead of creating files in `--buildDir` |
| `phpqa --execution no-parallel` | Don't use parallelism if `--execution != parallel` |
| `phpqa --config ./my-config` | Use [custom configuration](#advanced-configuration---phpqayml) |
| `phpqa --report` | Build [html reports](#html-reports) |
| `phpqa --report offline` | Build html reports with [bundled assets](https://github.com/EdgedesignCZ/phpqa/issues/95). **New in v1.16** |
| `phpqa tools` | Show versions of available tools |

_Tip:_ CLI options can be defined in [.phpqa.yml](#advanced-configuration---phpqayml)

## Output modes

Tool | `--output file` (default) - generated files | `--output cli` |
---- | ------------------------- | -------------- |
phploc | [phploc.xml](https://edgedesigncz.github.io/phpqa/report/phploc.xml) | [✓](https://github.com/sebastianbergmann/phploc#analyse-a-directory-and-print-the-result) |
phpcpd | [phpcpd.xml](https://edgedesigncz.github.io/phpqa/report/phpcpd.xml) | [✓](https://github.com/sebastianbergmann/phpcpd#usage-example) |
phpcs | [checkstyle.xml](https://edgedesigncz.github.io/phpqa/report/checkstyle.xml) | [full report](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Reporting#printing-full-and-summary-reports) |
pdepend | [pdepend-jdepend.xml](https://edgedesigncz.github.io/phpqa/report/pdepend-jdepend.xml), [pdepend-summary.xml](https://edgedesigncz.github.io/phpqa/report/pdepend-summary.xml), [pdepend-dependencies.xml](https://edgedesigncz.github.io/phpqa/report/pdepend-dependencies.xml), [pdepend-jdepend.svg](https://edgedesigncz.github.io/phpqa/report/pdepend-jdepend.svg), [pdepend-pyramid.svg](https://edgedesigncz.github.io/phpqa/report/pdepend-pyramid.svg) | ✗ |
phpmd | [phpmd.xml](https://edgedesigncz.github.io/phpqa/report/phpmd.xml) | [✓](https://github.com/phpmd/phpmd/blob/master/src/main/php/PHPMD/Renderer/TextRenderer.php#L47) |
phpmetrics | [phpmetrics.html (v1)](https://edgedesigncz.github.io/phpqa/report/phpmetrics.html), [phpmetrics/index.html (v2)](https://edgedesigncz.github.io/phpqa/report/phpmetrics/), [phpmetrics.xml](https://edgedesigncz.github.io/phpqa/report/phpmetrics.xml) | [✓](https://github.com/phpmetrics/PhpMetrics#usage) |
php-cs-fixer | [php-cs-fixer.html](https://edgedesigncz.github.io/phpqa/report/php-cs-fixer.html) | [✓](http://cs.sensiolabs.org/#usage "txt output format") |
parallel-lint | [parallel-lint.html](https://edgedesigncz.github.io/phpqa/report/parallel-lint.html) | [✓](https://github.com/JakubOnderka/PHP-Parallel-Lint#example-output) |
phpstan | [phpstan.html](https://edgedesigncz.github.io/phpqa/report/phpstan.html), [phpstan-phpqa.neon](https://edgedesigncz.github.io/phpqa/report/phpstan-phpqa.neon) | [✓](https://edgedesigncz.github.io/phpqa/report/phpstan.html), [phpstan-phpqa.neon](https://edgedesigncz.github.io/phpqa/report/phpstan-phpqa.neon "Generated configuration is saved in current working directory") |
psalm | [psalm.html](https://edgedesigncz.github.io/phpqa/report/psalm.html), [psalm.xml](https://edgedesigncz.github.io/phpqa/report/psalm.xml), [psalm-phpqa.xml](https://edgedesigncz.github.io/phpqa/report/psalm-phpqa.xml) | [✓](https://edgedesigncz.github.io/phpqa/report/psalm.xml), [psalm-phpqa.xml](https://edgedesigncz.github.io/phpqa/report/psalm-phpqa.xml "Generated configuration is saved in current working directory") |

## Exit code

`phpqa` can return non-zero exit code **since version 1.6**. It's optional feature that is by default turned off.
You have to define number of allowed errors for *phpcpd, phpcs, phpmd* in `--tools`.

[mode](#output-modes) | Supported version | What is analyzed? |
--------------------- | ----------------- | ----------------- |
`--output file` | >= 1.6 | Number of errors in XML files, or exit code for tools without XML |
`--output cli` | >= 1.9 | Exit code |

Let's say your [Travis CI](https://docs.travis-ci.com/user/customizing-the-build/#Customizing-the-Build-Step)
or [Circle CI](https://circleci.com/docs/manually/#overview) build should fail when new error is introduced.
Define number of allowed errors for each tools and watch the build:

```bash
phpqa --report --tools phpcs:0,phpmd:0,phpcpd:0,parallel-lint:0,phpstan:0,phpmetrics,phploc,pdepend
```

Number of allowed errors can be also defined in [.phpqa.yml](#advanced-configuration---phpqayml).

```yaml
phpqa:
    # can be overriden by CLI: phpqa --tools phpcs:1
    tools:
        - phpcs:0
```

**File mode**

![screenshot from 2016-07-23 13 53 34](https://cloud.githubusercontent.com/assets/7994022/17077767/e18bcb2a-50dc-11e6-86bc-0dfc8e22d98c.png)

_Tip_: override [`phpcs.ignoreWarnings`](#advanced-configuration---phpqayml) if you want to count just errors without phpcs warnings.

**CLI mode**

![screenshot from 2016-12-21 14 31 27](https://cloud.githubusercontent.com/assets/7994022/21391059/33730d76-c78a-11e6-913a-84b3c7836c28.png)

_Tip_: use [`echo $?`](https://gist.github.com/zdenekdrahos/5368eea304ed3fa6070bc77772779738) for displaying exit code.

## Advanced configuration - `.phpqa.yml`

Provide [CLI options](#analyze) from [`.phpqa.yml`](/.phpqa.yml):

| CLI option | .phpqa.yml |
| ---------- | ---------- |
| `phpqa --analyzedDirs ./` | `phpqa.analyzedDirs: ./` |
| `phpqa --buildDir ./build	` | `phpqa.buildDir: ./build` |
| `phpqa --ignoredDirs build,vendor` | `phpqa.ignoredDirs: build,vendor` |
| `phpqa --ignoredFiles RoboFile.php` | `phpqa.ignoredFiles: RoboFile.php` |
| `phpqa --tools phploc,phpcs:0` | `phpqa.tools: phploc,phpcs:0` |
| `phpqa --report` | `phpqa.report: true` |
| `phpqa --execution no-parallel` | `phpqa.execution: no-parallel` |
| `phpqa --output cli	` | `phpqa.output: cli` |
| `phpqa --verbose` | `phpqa.verbose: true` |

Override tools' settings with [`.phpqa.yml`](/.phpqa.yml):

Tool | Settings | Default Value | Your value
---- | -------- | ------------- | ----------- |
[phpqa.extensions](https://github.com/EdgedesignCZ/phpqa/blob/master/.phpqa.yml#L49) | PHP File extensions | php | Name of php file to parse, you can specify it like a string `php,inc,modules` or like a yaml array.
[phpcs.standard](https://pear.php.net/manual/en/package.php.php-codesniffer.usage.php#package.php.php-codesniffer.usage.coding-standard) | Coding standard | PSR2 | Name of existing standard (`PEAR`, `PHPCS`, `PSR1`, `PSR2`, `Squiz`,  `Zend`), or path to your coding standard. To specify [multiple standards](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Usage#specifying-a-coding-standard), you can use an array
[phpcs.ignoreWarnings](https://github.com/EdgedesignCZ/phpqa/issues/53) | If number of allowed errors is compared with warnings+errors, or just errors from `checkstyle.xml` | `false` | Boolean value
[phpcs.reports](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Reporting) | Report types | [`full`](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Reporting#printing-full-and-summary-reports) report in [cli mode](#output-modes), [`checkstyle`](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Reporting#printing-a-checkstyle-report) in [file mode](#output-modes) | Predefined [report types](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Reporting) or [custom reports](https://github.com/wikidi/codesniffer#examples)
[php-cs-fixer.rules](http://cs.sensiolabs.org/#usage) | Coding standard rules | `@PSR2` | String value
[php-cs-fixer.allowRiskyRules](http://cs.sensiolabs.org/#usage) | Whether risky rules may run  | `false` | Boolean value
[php-cs-fixer.config](http://cs.sensiolabs.org/#usage) | Load configuration from [file](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/.php_cs.dist) | `null` | Path to `.phpcs` file
[php-cs-fixer.isDryRun](http://cs.sensiolabs.org/#usage) | If code is just analyzed or fixers are applied  | `true` | Boolean value
[phpmetrics.config](https://github.com/EdgedesignCZ/phpqa/issues/74) | Configuration for phpmetrics v1  | `null` | Path to `.phpmetrics.yml` file
[phpmetrics.git](https://github.com/EdgedesignCZ/phpqa/pull/122) | phpmetrics v2 analyses based on Git History   | `null` | Boolean value or path to git binary
[phpmetrics.junit](https://github.com/EdgedesignCZ/phpqa/pull/125) | phpmetrics v2 evaluates metrics according to JUnit logs | `null` | Path to JUnit xml
[phpmetrics.composer](https://github.com/EdgedesignCZ/phpqa/pull/123) | phpmetrics v2 analyzes composer dependencies | `null` | Path to composer.json when the file is not included in `analyzedDirs`
[pdepend.coverageReport](https://github.com/EdgedesignCZ/phpqa/pull/124) | Load Clover style CodeCoverage report | `null` | Path to report produced by PHPUnit's `--coverage-clover` option
[phpmd.standard](http://phpmd.org/documentation/creating-a-ruleset.html) | Ruleset | [Edgedesign's standard](/app/phpmd.xml) | Path to ruleset. To specify [multiple rule sets](https://phpmd.org/documentation/index.html#using-multiple-rule-sets), you can use an array
[phpcpd](https://github.com/sebastianbergmann/phpcpd/blob/de9056615da6c1230f3294384055fa7d722c38fa/src/CLI/Command.php#L136) | Minimum number of lines/tokens for copy-paste detection | 5 lines, 70 tokens |
[phpstan](https://github.com/phpstan/phpstan#configuration) | Level, config file | Level 0, `%currentWorkingDirectory%/phpstan.neon` | Take a look at [phpqa config in tests/.travis](/tests/.travis/) |
[phpunit.binary](https://github.com/EdgedesignCZ/phpqa/blob/4947416/.phpqa.yml#L40) | Phpunit binary  | phpqa's phpunit | Path to phpunit executable in your project, typically [`vendor/bin/phpunit`](https://gitlab.com/costlocker/integrations/blob/master/basecamp/backend/.phpqa.yml#L2) |
[phpunit.config](https://phpunit.de/manual/current/en/organizing-tests.html#organizing-tests.xml-configuration) | PHPUnit configuration, `analyzedDirs` and `ignoredDirs` are not used, you have to specify test suites in XML file | `null` | Path to `phpunit.xml` file
[phpunit.reports](https://phpunit.de/manual/current/en/textui.html) | Report types  | no report | List of reports and formats, corresponds with CLI option, e.g. `--log-junit` is `log: [junit]` in `.phpqa.yml` |
[psalm.config](https://github.com/vimeo/psalm/wiki/Configuration) | Psalm configuration, `analyzedDirs` and `ignoredDirs` are appended to `projectFiles` | [Predefined config](/app/psalm.xml) | Path to `psalm.xml` file
[psalm.deadCode](https://github.com/vimeo/psalm/wiki/Running-Psalm#command-line-options) | Enable or not `--find-dead-code` option  of psalm | `false` | Boolean value
[psalm.threads](https://github.com/vimeo/psalm/wiki/Running-Psalm#command-line-options) | Set the number of process to use in parallel (option `--threads` of psalm) (Only if `--execution == parallel` for phpqa) | `1` | Number (>= 1)
[psalm.showInfo](https://github.com/vimeo/psalm/wiki/Running-Psalm#command-line-options) | Display or not information (non-error) messages (option `--show-info=` of psalm) | `true` | Boolean value

`.phpqa.yml` is automatically detected in current working directory, but you can specify
directory via option:

```bash
# use .phpqa.yml from defined directory
phpqa --config path-to-directory-with-config
```

You don't have to specify full configuration. Missing or empty values are replaced
with default values from our [`.phpqa.yml`](/.phpqa.yml). Example of minimal config
that defines only standard for CodeSniffer:

```yaml
phpcs:
    standard: Zend
```

_Tip_: use [PHP Coding Standard Generator](http://edorian.github.io/php-coding-standard-generator/)
for generating phpcs/phpmd standards.

You can specify multiple configurations directory (separated by `,`).
They are loaded in the order they are defined.
This can be useful if you have a common configuration file that you want to use across multiple project but you still want to have per project configuration.
Also, path inside configuration file are relative to where the configuration file is,
so if you have a package that bundle a custom tool, the `.phpqa.yml` in the package can refers files within it.
```bash
phpqa --config ~/phpqa/,my-config/,$(pwd)
```

## HTML reports

If you don't have Jenkins or other CI server, then you can use HTML reports.
HTML files are built when you add option `--report`. Take a look at
[report from phpqa](https://edgedesigncz.github.io/phpqa/report/phpqa.html).

```bash
# build html reports
phpqa --report
```

### Custom templates

Define custom templates if you don't like [default templates](/app/report).
You have to define path to `xsl` files in your [`.phpqa.yml`](#advanced-configuration---phpqayml):

```yaml
# use different template for PHPMD, use default for other tools
report:
    phpmd: my-templates/phpmd.xsl
```

Be aware that all **paths are relative to `.phpqa.yml`**. Don't copy-paste section `report`
if you don't have custom templates!

### Requirements

[`xsl` extension](http://php.net/manual/en/class.xsltprocessor.php)
must be installed and enabled for exporting HTML reports.
Otherwise you'll get error `PHP Fatal error:  Class 'XSLTProcessor' not found`.

```bash
# install xsl extension in Ubuntu
sudo apt-get update
sudo apt-get install php5-xsl
sudo service apache2 restart
```

## Continuous integration

We use [Jenkins-CI](http://jenkins-php.org/) in Edgedesign. Below you can find examples of
[Phing](https://www.phing.info/), [Robo](http://robo.li/) and `bash` tasks.

### Project with one directory

Typically in Symfony project you have project with `src` directory with all the code and tests. So you don't need ignore vendors, web directory etc.

**Phing - `build.xml`**

```xml
<target name="ci-phpqa">
    <exec executable="phpqa" passthru="true">
        <arg value="--analyzedDirs=./src" />
        <arg value="--buildDir=./build/logs" />
        <arg value="--report" />
    </exec>
</target>
```

**Robo - `RoboFile.php`**

```php
public function ciPhpqa()
{
    $this->taskExec('phpqa')
        ->option('analyzedDirs', './src')
        ->option('buildDir', './build/logs')
        ->option('report')
        ->run();
}
```

### Project with multiple directories (src, tests, ...)

When you analyze root directory of your project don't forget to ignore vendors and
other non-code directories. Otherwise the analysis could take a very long time.

**Since version [1.8](CHANGELOG.md#v180) phpqa supports analyzing multiple directories.**
Except phpmetrics that analyzes only first directory. Analyze root directory and ignore other directories if you rely on phpmetrics report.

**Phing - `build.xml`**

```xml
<target name="ci-phpqa">
    <exec executable="phpqa" passthru="true">
        <arg value="--analyzedDirs=./" />
        <arg value="--buildDir=./build/logs" />
        <arg value="--ignoredDirs=app,bin,build,vendor,web" />
        <arg value="--ignoredFiles= " />
        <arg value="--verbose" />
        <arg value="--report" />
    </exec>
</target>
```

**Robo - `RoboFile.php`**

```php
public function ciPhpqa()
{
    $this->taskExec('phpqa')
        ->option('verbose')
        ->option('report')
        ->option('analyzedDirs', './')
        ->option('buildDir', './build')
        ->option('ignoredDirs', 'build,bin,vendor')
        ->option('ignoredFiles', 'RoboFile.php,error-handling.php')
        ->run();
}
```

**Bash**

```bash
phpqa --verbose --report --analyzedDirs ./ --buildDir ./var/CI --ignoredDirs=bin,log,temp,var,vendor,www
```

### Circle.ci - artifacts + global installation

```yaml
machine:
    php:
        version: 7.0.4

dependencies:
    cache_directories:
        - ~/.composer/cache
    post:
        - 'git clone https://github.com/EdgedesignCZ/phpqa.git ./qa && cd qa && composer install --no-dev'

test:
    override:
        - vendor/bin/phpunit --testdox-html ./var/tests/testdox.html --testdox-text ./var/tests/testdox.txt --log-junit $CIRCLE_TEST_REPORTS/phpunit/junit.xml
        - qa/phpqa --report --verbose --buildDir var/QA --ignoredDirs vendor --tools=phpcs:0,phpmd:0,phpcpd:0,phploc,pdepend,phpmetrics
    post:
        - cp -r ./var/QA $CIRCLE_ARTIFACTS
        - cp -r ./var/tests $CIRCLE_ARTIFACTS
```

### Gitlab.ci - docker installation + composer cache + artifacts

```yaml
stages:
  - test

test:
  stage: test
  image: zdenekdrahos/phpqa:v1.23.3
  variables:
    BACKEND_QA: "*/backend/var/QA"
    BACKEND_CACHE: $CI_PROJECT_DIR/.composercache
  cache:
    paths:
    - $BACKEND_CACHE
  script:
    - 'export COMPOSER_CACHE_DIR=$BACKEND_CACHE'
    - 'composer install --ignore-platform-reqs --no-progress --no-suggest'
    - 'phpqa --report --tools phpcs:0,phpunit:0 --buildDir var/QA --analyzedDirs ./ --ignoredDirs var,vendor'
  artifacts:
    when: always
    paths:
    - $BACKEND_QA
```

## Contributing

Contributions from others would be very much appreciated! Send
[pull request](https://github.com/EdgedesignCZ/phpqa/pulls)/[issue](https://github.com/EdgedesignCZ/phpqa/issues). Thanks!

## License

Copyright (c) 2015, 2016, 2017, 2018 Edgedesign.cz. MIT Licensed,
see [LICENSE](/LICENSE) for details.

