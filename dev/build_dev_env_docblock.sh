#!/usr/bin/env sh

WORKINGDIR=`pwd`
DWVERSION='1.3.4'

echo "# Working in $WORKINGDIR"

if [ ! -f "$ROOTPATH/binaries/composer.phar" ]; then
    echo "***downloading composer"
    cd "$ROOTPATH/binaries"
    cp /home/gitlab-runner/tools/composer.phar ./
    cd "$ROOTPATH"
fi

if [ ! -d "php-docblock-checker" ]; then
    echo "### Downloading php-docblock-checker"
    cd "$WORKINGDIR/binaries"
    cp /home/gitlab-runner/tools/$DWVERSION.zip ./
    unzip "$WORKINGDIR/binaries/$DWVERSION.zip" >/dev/null 2>&1
    rm "$WORKINGDIR/binaries/$DWVERSION.zip"
    mv "$WORKINGDIR/binaries/php-docblock-checker-$DWVERSION" "$WORKINGDIR/binaries/php-docblock-checker"
    cd "$WORKINGDIR/binaries/php-docblock-checker"
    php "$WORKINGDIR/binaries/composer.phar" install >/dev/null 2>&1
    cd "$WORKINGDIR/"
fi
