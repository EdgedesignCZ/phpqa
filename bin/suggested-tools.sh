#!/bin/sh

# Usage:
#  $ bin/suggested-tools.sh install
#  $ bin/suggested-tools.sh install --update-no-dev
#  $ bin/suggested-tools.sh remove

mode="$1"
requireMode="$2"

if [ $mode = "install" ]
then
    echo "Installing suggested tools"
    if [ ! -z "$requireMode" ]; then
        # docker build OR travis + php 7.0 OR symfony2 (default composer.lock)
        composer require symfony/filesystem:~2 symfony/process:~2 symfony/finder:~2 jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan:~0.8.0 friendsofphp/php-cs-fixer:~2.2 vimeo/psalm:~1 sensiolabs/security-checker:~5 $requireMode
    else
        # symfony 3
        composer require jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan-src friendsofphp/php-cs-fixer:~2.2 vimeo/psalm sensiolabs/security-checker
    fi
else
    echo "Removing suggested tools"
    composer remove jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan friendsofphp/php-cs-fixer vimeo/psalm sensiolabs/security-checker
fi
