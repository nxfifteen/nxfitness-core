#!/usr/bin/env bash
echo -n "Running format fixer..."
./vendor/bin/phpcbf --standard="PSR2" --ignore="vendor,library,php-docblock-checker-1.3.4,phpspec" --extensions="php,css,js" --error-severity=1 --warning-severity=8 --exclude=Generic.WhiteSpace.ScopeIndent ./ >/dev/null 2>&1
echo "[DONE]"

echo -n "Running syntax checks..."
./vendor/bin/phpcs --standard="PSR2" --ignore="vendor,library,php-docblock-checker-1.3.4,phpspec" --extensions="php,css,js" --error-severity=1 --warning-severity=8 --exclude=Generic.WhiteSpace.ScopeIndent ./
#exit $?
exit 0