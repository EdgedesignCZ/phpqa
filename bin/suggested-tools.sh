#!/bin/bash

# PHP_VERSION=number bin/suggested-tools.sh <install|remove> <composer_require_modes>
#
# Help:
#  $ bin/suggested-tools.sh
#  $ bin/suggested-tools.sh install
#  $ bin/suggested-tools.sh install --update-no-dev
#  $ bin/suggested-tools.sh remove
#
# Usage for PHP5:
#  $ PHP_VERSION="5" bin/suggested-tools.sh

COMMAND="$0"
SELECTED_MODE="$1"
COMPOSER_REQUIRE_MODES="$2"

PHP_VERSION=${PHP_VERSION:-"7"}
PREINSTALLED_TOOLS=${PREINSTALLED_TOOLS:-""}

run () {
    TOOLS=$(get_tools)
    if [ -z "$SELECTED_MODE" ]; then
        show_help "install"
        show_help "remove"
        echo "Available tools: $TOOLS"
        exit 1
    fi
    show_help $SELECTED_MODE
    if [[ $SELECTED_MODE = "install" ]]; then
        install "$TOOLS"
    else
        remove "$TOOLS"
    fi
}

show_help() {
    MODE=$1
    echo "$ PHP_VERSION=$PHP_VERSION $COMMAND $MODE $COMPOSER_REQUIRE_MODES"
    echo
}

get_tools () {
    TOOLS="squizlabs/php_codesniffer phpmd/phpmd pdepend/pdepend phpmetrics/phpmetrics phploc/phploc sebastian/phpcpd"
    if [[ $PREINSTALLED_TOOLS != "legacy" ]]; then
        TOOLS="${TOOLS} php-parallel-lint/php-parallel-lint php-parallel-lint/php-console-highlighter friendsofphp/php-cs-fixer"
        if [[ ${PHP_VERSION:0:1} != "5" ]]; then
            TOOLS="${TOOLS} psalm/phar phpstan/phpstan nette/neon deptrac/deptrac enlightn/security-checker"
        fi
    fi
    echo $TOOLS
}

install () {
    TOOLS=$1
    echo "> Installing suggested tools"
    echo "$ composer require $TOOLS $COMPOSER_REQUIRE_MODES"
    echo
    composer require $TOOLS $COMPOSER_REQUIRE_MODES
}

remove () {
    TOOLS=${1/:>=2/""}
    echo "> Removing suggested tools"
    echo "$ composer require $TOOLS $COMPOSER_REQUIRE_MODES"
    echo
    composer remove $TOOLS
}

run
