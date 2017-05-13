#!/usr/bin/env sh

echo "## Updated System Image"
apt-get update >/dev/null 2>&1
apt-get install -y zip unzip wget >/dev/null 2>&1

if [ ! -d "binaries" ]; then
    echo "## Creating Binaries Folder"
    mkdir binaries
fi

if [ ! -f "composer.phar" ]; then
    echo "### Downloading Composer"
    cd binaries
    curl -sS https://getcomposer.org/installer | php >/dev/null 2>&1
    cd ../
fi

echo "#### Installing Composer DEV"
cp composer_dev.json composer.json
php binaries/composer.phar install --dev >/dev/null 2>&1

if [ ! -d "php-docblock-checker-1.3.4" ]; then
    echo "### Downloading php-docblock-checker"
    cd binaries
    wget https://github.com/Block8/php-docblock-checker/archive/1.3.4.zip >/dev/null 2>&1
    unzip 1.3.4.zip >/dev/null 2>&1
    rm 1.3.4.zip
    cd php-docblock-checker-1.3.4/
    php ../composer.phar install #>/dev/null 2>&1
    cd ../../
fi

if [ ! -f "phpunit.phar" ]; then
    echo "### Downloading phpunit"
    cd binaries
    wget https://phar.phpunit.de/phpunit.phar >/dev/null 2>&1
    cd ../
fi