#!/usr/bin/env bash

if [ ! -f "composer.phar" ]; then
    echo "downloading composer"
    curl -sS https://getcomposer.org/installer | php >/dev/null 2>&1
fi

php composer.phar install --dev >/dev/null 2>&1

if [ ! -d "php-docblock-checker-1.3.4" ]; then
    echo "downloading php-docblock-checker"
    wget https://github.com/Block8/php-docblock-checker/archive/1.3.4.zip >/dev/null 2>&1
    unzip 1.3.4.zip >/dev/null 2>&1
    rm 1.3.4.zip
    cd php-docblock-checker-1.3.4/
    php ../composer.phar install >/dev/null 2>&1
    cd ../
fi

if [ ! -f "phpunit.phar" ]; then
    echo "downloading phpunit"
    wget https://phar.phpunit.de/phpunit.phar >/dev/null 2>&1
fi