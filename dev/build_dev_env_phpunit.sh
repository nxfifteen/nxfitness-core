#!/usr/bin/env sh

WORKINGDIR=`pwd`

echo "# Working in $WORKINGDIR"

if [ ! -f "$WORKINGDIR/binaries/phpunit.phar" ]; then
    echo "### Downloading phpunit"
    cd "$WORKINGDIR/binaries"
    cp /home/gitlab-runner/tools/phpunit.phar ./
    cd "$WORKINGDIR/"
fi