
# Changelog

## v1.7.0

* [#22](https://github.com/EdgedesignCZ/phpqa/issues/22) Support Symfony3 components
* [#28](https://github.com/EdgedesignCZ/phpqa/pull/28) Add PHP 7.1 support, circle.ci example
* Add [docker usage](https://github.com/EdgedesignCZ/phpqa/commit/89cb494) to readme

## v1.6.0

* [#25](https://github.com/EdgedesignCZ/phpqa/issues/25) phpcs's custom path to standard isn't relative to .phpqa.yml, but current directory
* [#28](https://github.com/EdgedesignCZ/phpqa/pull/28)
    * Show summary after analysis
    * Allow failing build in travis/circle CI (return non-zero exit code when errors count > allowed errors count)

![screenshot from 2016-07-23 13 53 34](https://cloud.githubusercontent.com/assets/7994022/17077767/e18bcb2a-50dc-11e6-86bc-0dfc8e22d98c.png)

* [#29](https://github.com/EdgedesignCZ/phpqa/pull/29) Add .gitattributes (don't export tests and dev files)
* Upgrade QA tools (phpcs)

## v1.5.1

* [#18](https://github.com/EdgedesignCZ/phpqa/pull/18) phpmetrics - generate XML report
* Upgrade QA tools (phpcs)

## v1.5.0

* [#16](https://github.com/EdgedesignCZ/phpqa/pull/16) phpmd - scan only *.php files (fix typo in --suffixes option)
* Upgrade QA tools (phpmd, phpmetrics, phpcpd)
    * phpmetrics [generates bigger report](https://github.com/phpmetrics/PhpMetrics/issues/217)

## v1.4.0

* [#15](https://github.com/EdgedesignCZ/phpqa/issues/15) Rename halleck45/phpmetrics to phpmetrics/phpmetrics
* Upgrade QA tools

## v1.3.0

* [#13](https://github.com/EdgedesignCZ/phpqa/issues/13) Option for disabling parallel execution
* [#861](https://github.com/squizlabs/PHP_CodeSniffer/issues/861) Upgrade CodeSniffer
* Add [support](https://getcomposer.org/doc/04-schema.md#support) section to composer.json

## v1.2.1

* [#12](https://github.com/EdgedesignCZ/phpqa/issues/12) Report is not saved in `--buildDir` if `--buildDir` doesn't end with `/`

## v1.2.0

* [#7](https://github.com/EdgedesignCZ/phpqa/issues/7) HTML reports
* [#8](https://github.com/EdgedesignCZ/phpqa/issues/8) Advanced configuration - `.phpqa.yml` (define standards for phpcs, phpmd, phpcpd)
* [Github page](https://edgedesigncz.github.io/phpqa/)

## v1.1.0

* Upgrade QA tools
* [#6](https://github.com/EdgedesignCZ/phpqa/issues/6) Upgrade Robo
* Add travis-ci

## v1.0.6

* [#5](https://github.com/EdgedesignCZ/phpqa/issues/5) Tools command is broken
* Upgrade QA tools

## v1.0.5 - CLI improvements

* [#2](https://github.com/EdgedesignCZ/phpqa/issues/2) --verbose --quiet options
* [#3](https://github.com/EdgedesignCZ/phpqa/issues/3) output to stdout instead of files: --output cli

## v1.0.0 - v1.0.4

* QA tools - phploc, phpcpd, phpcs, pdepend, phpmd, phpmetrics
* Ignored directories