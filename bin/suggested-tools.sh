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
        composer require symfony/filesystem:~2 symfony/process:~2 symfony/finder:~2 jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan:~0.12.0 nette/neon friendsofphp/php-cs-fixer:~2.2 vimeo/psalm:~1 sensiolabs/security-checker:~5 $requireMode 
    else
        # symfony 3
        composer require jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan nette/neon friendsofphp/php-cs-fixer:~2.2 vimeo/psalm sensiolabs/security-checker
    fi

    # Special case of local-php-security-checker who have no composer install
    rm -f bin/local-php-security-checker
    curl -s https://api.github.com/repos/fabpot/local-php-security-checker/releases/latest | grep -E "browser_download_url(.+)linux_386" | cut -d : -f 2,3 | tr -d \" | wget -i -
    mv local-php-security-checker_* bin/local-php-security-checker
else
    echo "Removing suggested tools"
    composer remove jakub-onderka/php-parallel-lint jakub-onderka/php-console-highlighter phpstan/phpstan friendsofphp/php-cs-fixer vimeo/psalm sensiolabs/security-checker
    rm -f bin/local-php-security-checker
fi
