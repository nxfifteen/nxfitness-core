#!/usr/bin/env sh

WORKINGDIR=`pwd`

echo "# Working in $WORKINGDIR"

echo "## Updated System Image"
apt-get update -yqq
apt-get install -yqq git zip unzip wget php-xdebug

if [ ! -d "binaries" ]; then
    echo "## Creating Binaries Folder"
    mkdir "$WORKINGDIR/binaries"
fi

if [ ! -f "composer.phar" ]; then
    echo "### Downloading Composer"
    cd "$WORKINGDIR/binaries"
    curl -sS https://getcomposer.org/installer | php >/dev/null 2>&1
    cd "$WORKINGDIR/"
fi

echo "#### Installing Composer DEV"
cp composer_dev.json composer.json
php "$WORKINGDIR/binaries/composer.phar" install --dev >/dev/null 2>&1

if [ ! -d "php-docblock-checker-1.3.4" ]; then
    echo "### Downloading php-docblock-checker"
    cd "$WORKINGDIR/binaries"
    wget https://github.com/Block8/php-docblock-checker/archive/1.3.4.zip -O "$WORKINGDIR/binaries/1.3.4.zip"
    unzip "$WORKINGDIR/binaries/1.3.4.zip" >/dev/null 2>&1
    rm "$WORKINGDIR/binaries/1.3.4.zip"
    cd "$WORKINGDIR/php-docblock-checker-1.3.4"
    php "$WORKINGDIR/binaries/composer.phar" install #>/dev/null 2>&1
    cd "$WORKINGDIR/"
fi

if [ ! -f "$WORKINGDIR/binaries/phpunit.phar" ]; then
    echo "### Downloading phpunit"
    cd "$WORKINGDIR/binaries"
    wget https://phar.phpunit.de/phpunit.phar >/dev/null 2>&1
    cd "$WORKINGDIR/"
fi