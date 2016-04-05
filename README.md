
# PHPQA CLI

Analyze PHP code with one command.

[![License](https://poser.pugx.org/edgedesign/phpqa/license)](https://packagist.org/packages/edgedesign/phpqa)
[![Latest Stable Version](https://poser.pugx.org/edgedesign/phpqa/v/stable)](https://packagist.org/packages/edgedesign/phpqa)
[![Total Downloads](https://poser.pugx.org/edgedesign/phpqa/downloads)](https://packagist.org/packages/edgedesign/phpqa)
[![Dependency Status](https://www.versioneye.com/user/projects/5566c1666365390010c20000/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5566c1666365390010c20000)
[![PHP runtimes](http://php-eye.com/badge/edgedesign/phpqa/tested.svg)](http://php-eye.com/package/edgedesign/phpqa)
[![Build Status](https://travis-ci.org/EdgedesignCZ/phpqa.svg)](https://travis-ci.org/EdgedesignCZ/phpqa)

## Requirements

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

## Install

### Without composer

```bash
# install phpqa
git clone https://github.com/EdgedesignCZ/phpqa.git
composer install --no-dev

# make phpqa globally accessible
## you can symlink binary
sudo ln -s /path-to-phpqa-repository/phpqa /usr/bin/phpqa
## or add this directory to your PATH in your ~/.bash_profile (or ~/.bashrc)
export PATH=~/path-to-phpqa-repository-from-pwd:$PATH
```

### Composer

```bash
composer global require edgedesign/phpqa --update-no-dev
# Make sure you have ~/.composer/vendor/bin/ in your PATH.
```

Of course you can add dependency to `require-dev` section in your `composer.json`.
But I wouldn't recommend it. In my experience *one* QA tool which analyzes
*N* projects is better than *N* projects with *N* analyzers. It's up to you
how many repositories you want to update when new version is released.


## Analyze

```bash
phpqa --help

# analyze current directory and save output to build directory
phpqa
phpqa --analyzedDir ./ --buildDir ./build

# ignore selected directories and files
phpqa --ignoredDirs build,vendor --ignoredFiles RoboFile.php

# run selected tools
phpqa --tools phploc,phpcs

# show output from executed tools
phpqa -v
phpqa --verbose

# show no output at all
phpqa -q
phpqa --quiet

# CLI output instead of creating files (default output are files in --buildDir)
phpqa --output cli

# don't use parallelism (tools are runned in parallel if you don't specify option --execution != parallel)
phpqa --execution no-parallel

# build html reports
phpqa --report

## show versions of available tools
phpqa tools
```

## Advanced configuration - `.phpqa.yml`

Override tools' settings with [`.phpqa.yml`](/.phpqa.yml):

Tool | Settings | Default Value | Your value
---- | -------- | ------------- | ----------- |
[phpcs](https://pear.php.net/manual/en/package.php.php-codesniffer.usage.php#package.php.php-codesniffer.usage.coding-standard) | Coding standard | PSR2 | Name of existing standard (`PEAR`, `PHPCS`, `PSR1`, `PSR2`, `Squiz`,  `Zend`), or path to your coding standard
[phpmd](http://phpmd.org/documentation/creating-a-ruleset.html) | Ruleset | [Edgedesign's standard](/app/phpmd.xml) | Path to ruleset
[phpcpd](https://github.com/sebastianbergmann/phpcpd/blob/de9056615da6c1230f3294384055fa7d722c38fa/src/CLI/Command.php#L136) | Minimum number of lines/tokens for copy-paste detection | 5 lines, 70 tokens | 

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

```
# use different template for PHPMD, use default for other tools
report:
    phpmd: my-templates/phpmd.xsl
```

Be aware that all **paths are relative to `.phpqa.yml`**. Don't copy-paste section `report`
if you don't have custom templates!

## Requirements

[`xsl` extension](http://php.net/manual/en/class.xsltprocessor.php)
must be installed and enabled for exporting HTML reports.
Otherwise you'll get error `PHP Fatal error:  Class 'XSLTProcessor' not found`.

```bash
# install xsl extension in Ubuntu
sudo apt-get update
sudo apt-get install php5-xsl
sudo service apache2 restart
```

## Jenkins integration

We use [Jenkins-CI](http://jenkins-php.org/) in Edgedesign. Below you can find examples of
[Phing](https://www.phing.info/) and [Robo](http://robo.li/) tasks.

### Project with one directory

Typically in Symfony project you have project with `src` directory with all the code and tests. So you don't need ignore vendors, web directory etc. 

**Phing - `build.xml`**

```xml
<target name="ci-phpqa">
    <exec executable="phpqa" passthru="true">
        <arg value="--analyzedDir=./src" />
        <arg value="--buildDir=./build/logs" />
        <arg value="--ignoredDirs= " />
        <arg value="--ignoredFiles= " />
    </exec>
</target>
```


### Project with multiple directories (src, tests, ...)

When you analyze root directory of your project don't forget to ignore vendors and
other non-code directories. Otherwise the analysis could take a very long time.

**Phing - `build.xml`**

```xml
<target name="ci-phpqa">
    <exec executable="phpqa" passthru="true">
        <arg value="--analyzedDir=./" />
        <arg value="--buildDir=./build/logs" />
        <arg value="--ignoredDirs=app,bin,build,vendor,web" />
        <arg value="--ignoredFiles= " />
        <arg value="--verbose" />
    </exec>
</target>
```

**Robo - `RoboFile.php`**

```php
public function ciPhpqa()
{
    $this->taskExec('phpqa')
        ->option('verbose')
        ->option('analyzedDir', './')
        ->option('buildDir', './build')
        ->option('ignoredDirs', 'build,bin,vendor')
        ->option('ignoredFiles', 'RoboFile.php,error-handling.php')
        ->run();
}
```

## Contributing

Contributions from others would be very much appreciated! Send 
[pull request](https://github.com/EdgedesignCZ/phpqa/pulls)/
[issue](https://github.com/EdgedesignCZ/phpqa/issues). Thanks!

## License

Copyright (c) 2015 Edgedesign.cz. MIT Licensed,
see [LICENSE](/LICENSE) for details.

