
# Changelog

## v1.18.0

* **phpstan**
    * [#108](https://github.com/EdgedesignCZ/phpqa/issues/108) - pretty html report, checkstyle output (_BC, requires phpstan >= [0.8](https://github.com/phpstan/phpstan/releases/tag/0.8)_)
    * [#110](https://github.com/EdgedesignCZ/phpqa/issues/110) - use entire phpstan config, not just parameters

![screenshot from 2018-02-08 19 41 21](https://user-images.githubusercontent.com/7994022/35991629-11ffea0a-0d08-11e8-9b3b-9cf8afa6941a.png)

## v1.17.0

* [#98](https://github.com/EdgedesignCZ/phpqa/pull/98) - allow multiple configurations in `--config`
* [#104](https://github.com/EdgedesignCZ/phpqa/pull/104) - add support for extensions in `parallel-lint`
* _Internal changes_
    * [#99](https://github.com/EdgedesignCZ/phpqa/pull/99), [#101](https://github.com/EdgedesignCZ/phpqa/pull/101), [#106](https://github.com/EdgedesignCZ/phpqa/pull/106) Travis build (fix composer/psalm errors, add PHP 7.2)

## v1.16.0

* **Optional tools**
   * [#97](https://github.com/EdgedesignCZ/phpqa/pull/97) sensiolabs/security-checker (stable)
* [#95](https://github.com/EdgedesignCZ/phpqa/issues/95) Download assets if `--report=offline`
   
![--report=offline preview](https://user-images.githubusercontent.com/7994022/32001321-bbe1af3a-b999-11e7-9553-1995d840e2e1.png)

## v1.15.0

* **Optional tools**
    * [#94](https://github.com/EdgedesignCZ/phpqa/pull/94) vimeo/psalm - dynamic configuration
* [#92](https://github.com/EdgedesignCZ/phpqa/pull/92) Improved reports (summary, [page load](https://github.com/EdgedesignCZ/phpqa/commit/96ea12438e1cb51362ad156a463fc7d5b9cff1a6))
    ![](https://user-images.githubusercontent.com/7994022/31012952-2121b600-a514-11e7-8a5c-3b9fadca7152.png)
* Upgrade QA tools (phpcs)
* _Internal changes_
    * [#93](https://github.com/EdgedesignCZ/phpqa/pull/93) Refactoring tools (tool per class, experimental configuration in `.phpqa.yml`)
    * [#94](https://github.com/EdgedesignCZ/phpqa/pull/94) Fix `composer.lock`, enable phpstan/psalm in travis

## v1.14.0

* **Optional tools**
    * [#80](https://github.com/EdgedesignCZ/phpqa/issues/85) vimeo/psalm (stable)
* [#88](https://github.com/EdgedesignCZ/phpqa/issues/88) Allow using custom binary (e.g. `phpunit.binary`)    
    * show [skipped tools in summary](https://travis-ci.org/EdgedesignCZ/phpqa/jobs/278929601#L417)
    * refactoring printing versions in `phpqa tools`
    * **`phpunit.config` BC** - `phpunit.config` is relative to `.phpqa.yml`, previously it was relative to `cwd`
* [#91](https://github.com/EdgedesignCZ/phpqa/pull/91) Docker support ([zdenekdrahos/phpqa](https://hub.docker.com/r/zdenekdrahos/phpqa/))

## v1.13.0

* **Optional tools**
    * [#80](https://github.com/EdgedesignCZ/phpqa/pull/80) phpunit (experimental)
* [#72](https://github.com/EdgedesignCZ/phpqa/pull/72) Support phploc v4.X (drop `--progress` option)
* [#74](https://github.com/EdgedesignCZ/phpqa/pull/74) Support phpmetrics v1 configuration, evaluate exit code
* [#83](https://github.com/EdgedesignCZ/phpqa/pull/83) Make php extensions configurable in `.phpqa.yml`
* _Bugfixes_
    * [#75](https://github.com/EdgedesignCZ/phpqa/issues/75) Fix ignoring phpmd/pdepend directories on Windows
    * [#76](https://github.com/EdgedesignCZ/phpqa/issues/76) Fix escaping binary path
    * [#77](https://github.com/EdgedesignCZ/phpqa/issues/77) Don't use default 60s timeout is non-parallel execution 
    * [#79](https://github.com/EdgedesignCZ/phpqa/pull/79) Fix typo in docker example in Readme

## v1.12.1

* [#69](https://github.com/EdgedesignCZ/phpqa/issues/69) Composer - fix version constrains (robo, twig), add php-cs-fixer to suggested tools

## v1.12.0

* **Optional tools**
    * [#60](https://github.com/EdgedesignCZ/phpqa/pull/60) php-cs-fixer (stable)
* [#68](https://github.com/EdgedesignCZ/phpqa/pull/68) Support phpstan v0.7
* [#65](https://github.com/EdgedesignCZ/phpqa/pull/65) Support phpcs v3.X
* [#61](https://github.com/EdgedesignCZ/phpqa/pull/61) Support Robo v1.X
* [#58](https://github.com/EdgedesignCZ/phpqa/pull/58) Support Twig 2
* _Internal changes_
    * [#50](https://github.com/EdgedesignCZ/phpqa/pull/55), [#62](https://github.com/EdgedesignCZ/phpqa/pull/62) Optimize speed on Travis (Precise + HHVM on Trusty)

## v1.11.0

* [#54](https://github.com/EdgedesignCZ/phpqa/pull/54) Advanced phpcs configuration
    * define [custom reports](https://github.com/EdgedesignCZ/phpqa/blob/master/tests/.travis/.phpqa.yml#L2) in `phpcs.reports`
    * [#53](https://github.com/EdgedesignCZ/phpqa/issues/53) - allow ignoring warnings in `phpcs.ignoreWarnings`

## v1.10.0

* [#50](https://github.com/EdgedesignCZ/phpqa/pull/50) Support phpmetrics 2.0 ([new html report](https://edgedesigncz.github.io/phpqa/report/phpmetrics/))
* Upgrade QA tools (phpcs, phpmd, pdepend)

## v1.9.1

* Fix phpqa version (version constant not changed in `v1.9`)
* _Internal changes_
    * Deploy changelog to github pages 
    * Update copyright

## v1.9.0

* **Optional tools**
    - [#41](https://github.com/EdgedesignCZ/phpqa/pull/41) parallel-lint (stable)
    - [#43](https://github.com/EdgedesignCZ/phpqa/pull/43) phpstan `v0.5` (experimental)
* [#40](https://github.com/EdgedesignCZ/phpqa/pull/40) Support exit code and summary in CLI mode<br />
    ![](https://cloud.githubusercontent.com/assets/7994022/21391059/33730d76-c78a-11e6-913a-84b3c7836c28.png)
* [#42](https://github.com/EdgedesignCZ/phpqa/pull/42) Show parsing errors in phpmd report
    ![](https://cloud.githubusercontent.com/assets/7994022/21394949/f7b28706-c79a-11e6-8fe9-ddc5906fa544.png)
* [#44](https://github.com/EdgedesignCZ/phpqa/pull/44) `phpqa tools` - load package information from composer 
    ![](https://cloud.githubusercontent.com/assets/7994022/21451392/296f40b2-c8ff-11e6-8871-bd98a21b9e5a.png)
* [#47](https://github.com/EdgedesignCZ/phpqa/pull/47) Custom binary location ([#46](https://github.com/EdgedesignCZ/phpqa/issues/46) Changing composer bin-dir breaks tools path)
* [#48](https://github.com/EdgedesignCZ/phpqa/pull/48) Refine documentation

## v1.8.0

* [#37](https://github.com/EdgedesignCZ/phpqa/pull/37#issuecomment-266218735) Stop phpqa when `.phpqa.yml` or specified standard doesn't exist
* [#39](https://github.com/EdgedesignCZ/phpqa/pull/39) Analyze multiple directories + deprecate analyzing one directory
    * **Before**: `phpqa --analyzedDir ./`
    * **After**: `phpqa --analyzedDirs ./`
* Drop outdated versioneye badge in favor of `composer outdated --direct`
* Upgrade QA tools (phpcs, phpmd, pdepend)

## v1.7.3

* [#35](https://github.com/EdgedesignCZ/phpqa/pull/35) Upgrade robo, pdepend, phpmd
    * don't use `henrikbjorn/lurker: dev-master`
    * fix PHP7’s null coalesce operator in [phpmd](https://github.com/phpmd/phpmd/issues/347) and [pdepend](https://github.com/pdepend/pdepend/pull/267)
* [#34](https://github.com/EdgedesignCZ/phpqa/pull/34) Explicit `ext-xsl` requirement

## v1.7.2

* [#33](https://github.com/EdgedesignCZ/phpqa/issues/33) Improved [reports](https://edgedesigncz.github.io/phpqa/report/phpqa.html)
    * unified layout (bootstrap3) + phpqa version
        ![screenshot from 2016-10-22 09 00 15](https://cloud.githubusercontent.com/assets/7994022/19617638/2b8852a2-9836-11e6-8e35-e5551c684451.png)
    * overview in phpcs, phpmd, phpcpd
        ![screenshot from 2016-10-22 09 00 35](https://cloud.githubusercontent.com/assets/7994022/19617639/2ba27f74-9836-11e6-83ef-e333c59e89dd.png)
    * relative file paths
        ![screenshot from 2016-10-22 09 01 04](https://cloud.githubusercontent.com/assets/7994022/19617642/2bb524d0-9836-11e6-98ca-1dfa4e5bfe55.png)
    * interactive pdepend metrics
        ![screenshot from 2016-10-22 09 01 37](https://cloud.githubusercontent.com/assets/7994022/19617640/2bb3d274-9836-11e6-97a8-22def6ea1901.png)

## v1.7.1

* [#32](https://github.com/EdgedesignCZ/phpqa/pull/32) Improved reports
    * pdepend - dependencies, [#31](https://github.com/EdgedesignCZ/phpqa/issues/31) summary report

        ![screenshot from 2016-10-15 10 41 19](https://cloud.githubusercontent.com/assets/7994022/19408696/ef3d1200-92c3-11e6-90c1-258b63051ec0.png)
        ![screenshot from 2016-10-15 10 40 33](https://cloud.githubusercontent.com/assets/7994022/19408695/ef390d68-92c3-11e6-9035-266cb6a77d0a.png)

    * open tabs from navbar

        ![screenshot from 2016-10-15 10 38 12](https://cloud.githubusercontent.com/assets/7994022/19408678/888fe802-92c3-11e6-86ac-9595d576a9dd.png)


## v1.7.0

* [#22](https://github.com/EdgedesignCZ/phpqa/issues/22) Support Symfony3 components
* [#30](https://github.com/EdgedesignCZ/phpqa/pull/30) Add PHP 7.1 support, circle.ci example
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