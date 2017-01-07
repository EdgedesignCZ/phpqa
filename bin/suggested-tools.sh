#!/bin/sh

# Usage:
#  $ bin/suggested-tools.sh install
#  $ bin/suggested-tools.sh remove

mode="$1"

if [ $mode = "install" ]
then
    echo "Installing suggested tools"
    composer require jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan
else
    echo "Removing suggested tools"
    composer remove jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan
fi
