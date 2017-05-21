#!/usr/bin/env sh

WORKINGDIR=`pwd`

echo "# Working in $WORKINGDIR"

echo "## Updated System Image"
apt-get update -yqq >/dev/null 2>&1
apt-get install -yqq git >/dev/null 2>&1
apt-get install -yqq zip >/dev/null 2>&1
apt-get install -yqq unzip >/dev/null 2>&1
apt-get install -yqq wget >/dev/null 2>&1
apt-get install -yqq php-xdebug >/dev/null 2>&1
apt-cache search xdebug
apt-cache search xdebug | grep php

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
