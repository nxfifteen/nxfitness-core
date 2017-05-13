#!/usr/bin/env sh

pwd

if [ ! -d "binaries" ]; then
    echo "creating binaries"
    mkdir binaries
fi

if [ ! -f "composer.phar" ]; then
    echo "downloading composer"
    cd binaries
    curl -sS https://getcomposer.org/installer | php >/dev/null 2>&1
    cd ../
fi

cp composer_dev.json composer.json
php binaries/composer.phar install --dev >/dev/null 2>&1

if [ ! -d "php-docblock-checker-1.3.4" ]; then
    cd binaries
    pwd
    echo "downloading php-docblock-checker"
    curl -o 1.3.4.zip https://github.com/Block8/php-docblock-checker/archive/1.3.4.zip
    unzip 1.3.4.zip
    rm 1.3.4.zip
    cd php-docblock-checker-1.3.4/
    php ../composer.phar install #>/dev/null 2>&1
    cd ../../
fi

if [ ! -f "phpunit.phar" ]; then
    echo "downloading phpunit"
    cd binaries
    wget https://phar.phpunit.de/phpunit.phar >/dev/null 2>&1
    cd ../
fi