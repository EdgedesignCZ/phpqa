#!/bin/sh

./phpqa --verbose --report --config tests/.travis --tools phpcs:0,php-cs-fixer:0,phpmd:0,phpcpd:0,parallel-lint:0,phpstan,phpmetrics,phploc,pdepend
