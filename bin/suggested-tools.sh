#!/bin/bash

# PHP_VERSION=number UPDATE_COMPOSER=yes|no bin/suggested-tools.sh <install|remove> <composer_require_modes>
#
# Help:
#  $ bin/suggested-tools.sh
#  $ bin/suggested-tools.sh install
#  $ bin/suggested-tools.sh install --update-no-dev
#  $ bin/suggested-tools.sh remove
#
# Update existings tools before installing suggested tools:
#  $ UPDATE_COMPOSER="yes" bin/suggested-tools.sh
#
# Usage for PHP5:
#  $ PHP_VERSION="5" bin/suggested-tools.sh

COMMAND="$0"
SELECTED_MODE="$1"
COMPOSER_REQUIRE_MODES="$2"

PHP_VERSION=${PHP_VERSION:-"7"}
UPDATE_COMPOSER=${UPDATE_COMPOSER:-"no"}

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
    echo "$ PHP_VERSION=$PHP_VERSION UPDATE_COMPOSER=$UPDATE_COMPOSER $COMMAND $MODE $COMPOSER_REQUIRE_MODES"
    echo
}

get_tools () {
    TOOLS="php-parallel-lint/php-parallel-lint php-parallel-lint/php-console-highlighter sensiolabs/security-checker friendsofphp/php-cs-fixer:>=2"
    if [[ ${PHP_VERSION:0:1} != "5" ]]; then
        TOOLS="${TOOLS} vimeo/psalm:>=2 phpstan/phpstan nette/neon"
    fi
    echo $TOOLS
}

install () {
    TOOLS=$1
    if [[ $UPDATE_COMPOSER == "yes" ]]; then
        echo "> Updating installed tools"
        echo
        composer update
    fi
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
