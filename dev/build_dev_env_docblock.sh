#!/usr/bin/env sh

WORKINGDIR=`pwd`

echo "# Working in $WORKINGDIR"

if [ ! -d "php-docblock-checker-1.3.4" ]; then
    echo "### Downloading php-docblock-checker"
    cd "$WORKINGDIR/binaries"
    wget https://github.com/Block8/php-docblock-checker/archive/1.3.4.zip -O "$WORKINGDIR/binaries/1.3.4.zip" >/dev/null 2>&1
    unzip "$WORKINGDIR/binaries/1.3.4.zip" >/dev/null 2>&1
    rm "$WORKINGDIR/binaries/1.3.4.zip"
    cd "$WORKINGDIR/binaries/php-docblock-checker-1.3.4"
    php "$WORKINGDIR/binaries/composer.phar" install >/dev/null 2>&1
    cd "$WORKINGDIR/"
fi
