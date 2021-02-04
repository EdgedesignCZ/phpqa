#!/bin/sh

# Usage:
#  $ bin/suggested-tools.sh install
#  $ bin/suggested-tools.sh install --update-no-dev
#  $ bin/suggested-tools.sh remove

mode="$1"
requireMode="$2"

PHP7=${PHP7:-"yes"}
SYMFONY2=${SYMFONY2:-"no"}

if [ $mode = "install" ]
then
    echo "Installing suggested tools"
    # psalm 0.2 for php 5.4, v2 can be minimum when php5.4 and symfony2 components are dropped
    TOOLS="php-parallel-lint/php-parallel-lint php-parallel-lint/php-console-highlighter sensiolabs/security-checker vimeo/psalm:>=0.2 friendsofphp/php-cs-fixer:>=2"
    if [[ ${PHP7} == "yes" ]]; then
        TOOLS="${TOOLS} phpstan/phpstan nette/neon"
    fi
    if [[ ${SYMFONY2} == "yes" ]]; then
        # https://github.com/EdgedesignCZ/phpqa/commit/94e9b49
        # https://github.com/EdgedesignCZ/phpqa/commit/13a8025
        TOOLS="${TOOLS} symfony/filesystem:~2 symfony/process:~2 symfony/finder:~2"
    fi
    echo
    echo "composer require $TOOLS $requireMode"
    echo
    composer require $TOOLS $requireMode
else
    echo "Removing suggested tools"
    composer remove php-parallel-lint/php-parallel-lint php-parallel-lint/php-console-highlighter phpstan/phpstan friendsofphp/php-cs-fixer vimeo/psalm sensiolabs/security-checker
fi
