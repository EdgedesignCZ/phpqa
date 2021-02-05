#!/bin/bash

# Usage:
#  $ bin/suggested-tools.sh install
#  $ bin/suggested-tools.sh install --update-no-dev
#  $ bin/suggested-tools.sh remove

MODE="$1"
COMPOSER_REQUIRE_MODES="$2"

PHP_VERSION=${PHP_VERSION:-"7"}
UPDATE_COMPOSER=${UPDATE_COMPOSER:-"no"}
IS_NOT_PHP5="yes"
if [[ ${PHP_VERSION:0:1} == "5" ]]; then
    IS_NOT_PHP5="no"
fi

echo "$ PHP_VERSION=$PHP_VERSION UPDATE_COMPOSER=$UPDATE_COMPOSER bin/suggested-tools.sh $MODE"
echo

if [ $MODE = "install" ]
then
    if [[ $UPDATE_COMPOSER == "yes" ]]; then
        echo "> Updating installed tools"
        echo
        composer update
    fi
    echo "> Installing suggested tools"
    TOOLS="php-parallel-lint/php-parallel-lint php-parallel-lint/php-console-highlighter sensiolabs/security-checker friendsofphp/php-cs-fixer:>=2"
    if [[ $IS_NOT_PHP5 == "yes" ]]; then
        TOOLS="${TOOLS} vimeo/psalm:>=2 phpstan/phpstan nette/neon"
    fi
    echo "$ composer require $TOOLS $COMPOSER_REQUIRE_MODES"
    echo
    composer require $TOOLS $COMPOSER_REQUIRE_MODES
else
    echo "> Removing suggested tools"
    composer remove php-parallel-lint/php-parallel-lint php-parallel-lint/php-console-highlighter phpstan/phpstan friendsofphp/php-cs-fixer vimeo/psalm sensiolabs/security-checker
fi
