#!/usr/bin/env sh

WORKINGDIR=`pwd`

echo "# Working in $WORKINGDIR"

if [ ! -f "$WORKINGDIR/binaries/phpunit.phar" ]; then
    echo "### Downloading phpunit"
    cd "$WORKINGDIR/binaries"
    wget https://phar.phpunit.de/phpunit.phar >/dev/null 2>&1
    cd "$WORKINGDIR/"
fi