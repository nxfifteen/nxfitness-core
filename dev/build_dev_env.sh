#!/usr/bin/env sh

WORKINGDIR=`pwd`

echo "# Working in $WORKINGDIR"

# echo "## Updated System Image"
# apt-get update -yqq >/dev/null 2>&1
# apt-get install -yqq git zip unzip wget php5-xdebug >/tmp/apt 2>&1
# if [ $? != 0 ]; then
#     cat /tmp/apt
# fi

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
php "$WORKINGDIR/binaries/composer.phar" install >/tmp/composer 2>&1
if [ $? != 0 ]; then
    cat /tmp/composer
fi
