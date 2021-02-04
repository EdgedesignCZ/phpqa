#!/bin/sh

# Usage:
#  $ bin/suggested-tools.sh install
#  $ bin/suggested-tools.sh install --update-no-dev
#  $ bin/suggested-tools.sh remove

mode="$1"
requireMode="$2"

PHP7=${PHP7:-"yes"}
PSALM_VERSION=${PSALM_VERSION:-""}

if [ $mode = "install" ]
then
    echo "Installing suggested tools"
    TOOLS="php-parallel-lint/php-parallel-lint php-parallel-lint/php-console-highlighter sensiolabs/security-checker vimeo/psalm$PSALM_VERSION"
    if [[ ${PHP7} == "yes" ]]; then
        TOOLS="${TOOLS} phpstan/phpstan nette/neon"
    fi
    echo $TOOLS
    echo

    if [ ! -z "$requireMode" ]; then
        # docker build OR travis + php 7.0 OR symfony2 (default composer.lock)
        composer require symfony/filesystem:~2 symfony/process:~2 symfony/finder:~2 $TOOLS friendsofphp/php-cs-fixer:~2.2 $requireMode 
    else
        # symfony 3
        composer require $TOOLS friendsofphp/php-cs-fixer
    fi
else
    echo "Removing suggested tools"
    composer remove php-parallel-lint/php-parallel-lint php-parallel-lint/php-console-highlighter phpstan/phpstan friendsofphp/php-cs-fixer vimeo/psalm sensiolabs/security-checker
fi
