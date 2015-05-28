
# PHPQA CLI

Analyze PHP code with one command.

[![License](https://poser.pugx.org/edgedesign/phpqa/license)](https://packagist.org/packages/edgedesign/phpqa)
[![Latest Stable Version](https://poser.pugx.org/edgedesign/phpqa/v/stable)](https://packagist.org/packages/edgedesign/phpqa)
[![Dependency Status](https://www.versioneye.com/user/projects/5562ac79366466001b5a0000/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5562ac79366466001b5a0000)

## Why?

Every analyzer has different arguments and options in different formats *(no surprise in PHP world :)*.
If you ever tried to get ignoring directories to work then you know that I mean. On the other hand
CLI tools are cool because you can analyze any directory or file.
Unfortunately [Jenkins](http://jenkins-php.org/automation.html),
[Travis](https://github.com/libis/plugin-Mailer/blob/095cc1154fd6d7beb3be4425329868ecfa2043d9/.travis.yml),
[Scrutiziner](https://github.com/antonbabenko/imagepush2/blob/db88b1c65a34250ba98e01d584d72421aedfaeac/.scrutinizer.yml) 
needs special configuration file. What if you want to analyze every bundle in your Symfony app?
Will you create e.g. Jenkins project/task for each bundle?

* I want to analyze selected directory without complex configuration and creating extra files/tasks
* I don't care about format of [ignored directories](https://github.com/EdgedesignCZ/phpqa/blob/master/tests/IgnoredPathsTest.php) in phploc, phpmd, ...
* I don't want to update all projects when QA tool is updated or if I've found cool tool like [PHPMetrics](https://github.com/Halleck45/PhpMetrics)
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
# install php-a-cli
git@github.com:EdgedesignCZ/phpqa.git
composer install --no-dev

# make phpqa is globally accessible
## you can symlink binary
sudo ln -s /path-to-phpqa-cli-repository/phpqa /usr/bin/phpqa
## or add this directory to your PATH in your ~/.bash_profile (or ~/.bashrc)
export PATH=~/path-to-phpqa-cli-repository-from-pwd:$PATH
```

### Composer

```bash
composer global require 'edgedesign/phpqa=*' --update-no-dev
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

## show versions of available tools
phpqa tools
```

## Jenkins integration

We use [Jenkins-CI](http://jenkins-php.org/) in Edgedesign. Below you can find examples of
[Phing](https://www.phing.info/) and [Robo](http://robo.li/) tasks. Right now Edgedesign's
phpmd rulesets are [“hard-coded”](https://github.com/EdgedesignCZ/phpqa/blob/master/app/phpmd.xml).
That happens when you open-source internal app ([contributions are welcomed](#contributing)).

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
    </exec>
</target>
```

**Robo - `RoboFile.php`**

```php
public function ciPhpqa()
{
    $this->taskExec('phpqa')
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

