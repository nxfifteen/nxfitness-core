#!/usr/bin/env bash
./vendor/bin/phpcbf --standard="PSR2" --ignore="vendor,library,php-docblock-checker-1.3.4,phpspec" --extensions="php,css,js" --error-severity=1 --warning-severity=8 --exclude=Generic.WhiteSpace.ScopeIndent ./
./vendor/bin/phpcs --standard="PSR2" --ignore="vendor,library,php-docblock-checker-1.3.4,phpspec" --extensions="php,css,js" --error-severity=1 --warning-severity=8 --exclude=Generic.WhiteSpace.ScopeIndent ./
return $?