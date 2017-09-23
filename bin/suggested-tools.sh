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
    composer require jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan friendsofphp/php-cs-fixer:~2.2 vimeo/psalm $requireMode
else
    echo "Removing suggested tools"
    composer remove jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan friendsofphp/php-cs-fixer vimeo/psalm
fi
