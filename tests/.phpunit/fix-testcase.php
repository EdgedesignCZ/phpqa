<?php

$legacyTestClass = 'PHPUnit_Framework_TestCase';
if (!class_exists($legacyTestClass)) {
    class_alias('PHPUnit\Framework\TestCase', $legacyTestClass);
}
