#!/usr/bin/env bash

cd ./binaries/;

curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
chmod +x phpcs.phar

curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar
chmod +x phpcbf.phar

cd ../

echo -n "Running format fixer..."
./binaries/phpcbf.phar --standard="PSR2" --ignore="bundle,vendor,library,php-docblock-checker,phpspec" --extensions="php,css,js" --error-severity=1 --warning-severity=8 --exclude=Generic.WhiteSpace.ScopeIndent ./ >/dev/null 2>&1
echo "[DONE]"

echo -n "Running syntax checks..."
./binaries/phpcs.phar --standard="PSR2" --ignore="bundle,vendor,library,php-docblock-checker,phpspec" --extensions="php,css,js" --error-severity=1 --warning-severity=8 --exclude=Generic.WhiteSpace.ScopeIndent ./
if [ $? == 0 ]; then
    echo "[DONE]"
    exit 0
else
    exit 1
fi
