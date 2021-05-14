<?php

$legacyTestClass = 'PHPUnit_Framework_TestCase';
if (!class_exists($legacyTestClass)) {
    // `class_alias('PHPUnit\Framework\TestCase', $legacyTestClass);` does not work for psalm:
    // > Could not get class storage for phpunit_framework_testcase"
    // > https://github.com/EdgedesignCZ/phpqa/runs/2581795905?check_suite_focus=true#step:7:246
    // > https://github.com/psalm/psalm-plugin-phpunit/issues/30#issuecomment-485485187
    include_once(__DIR__ . "/fix-psalm-testcase.php");
}
