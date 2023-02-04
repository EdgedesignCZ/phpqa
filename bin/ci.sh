#!/bin/sh

ALLOWED_SECURITY_ERRORS=${ALLOWED_SECURITY_ERRORS:-""}

run () {
    hotfix_security_checker_for_php5
    run_phpqa
}

hotfix_security_checker_for_php5 () {
    if [ -n "$ALLOWED_SECURITY_ERRORS" ]; then
        echo "Updating security-checker errors to $ALLOWED_SECURITY_ERRORS..."
        sed -i -e "s/security-checker:0/security-checker:$ALLOWED_SECURITY_ERRORS/g" tests/.ci/.phpqa.yml
    fi
}

run_phpqa () {
    ./phpqa --config tests/.ci
}

run

