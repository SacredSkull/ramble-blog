#!/bin/bash
cd `dirname "$(readlink -f "$0")"`

installComposer() {
    echo "--- Composer Phar is missing, fixing... ---"
    $EXEC_AS curl https://getcomposer.org/installer | php
    echo "--- Downloaded composer. ---"
}

existing(){
    propel migration:up
    if [ ! $? -eq 0 ]; then
        if [ $# -eq 0 ]; then
            read -p '\e[33m\e[1m>>> There is an issue with your database. Would you like to try resetting it?\e[0m\e[39m Answering "y" or "yes" will wipe all data (in the database). [y/n] ' -n 1 -r
        else
            REPLY="Y"
        fi

        echo    # (optional) move to a new line
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            propel sql:build --overwrite
            propel sql:insert
            REPLY=""
        else
            exit
        fi
    fi
}


export DB_HOST="${DB_HOST:-127.0.0.1}"
export DB_NAME="${DB_NAME:-blog}"
export DB_PORT="${DB_PORT:-3306}"
export DB_USER="${DB_USER:-blog}"
export DB_PASS="${DB_PASS:-sacredskullBlog}"

COMPOSER_CMD="php composer.phar"

hash composer 2>/dev/null
if [ $? -eq 0 ]; then
    COMPOSER_CMD="composer"
elif [ ! -e composer.phar ]; then
    installComposer
fi

echo "--- Composer ready. ---"
$COMPOSER_CMD update -o
echo "--- Installed/updated dependencies. ---"
export PATH="$PWD/vendor/bin":$PATH

# Work on Propel now
cd ./Config/Propel || exit
propel config:convert
propel model:build

if [[ $* == *--ci* ]]; then
    exit
fi

if [ -e ../../.existing-ramble-blog ]; then
    existing
else
    if [ $# -eq 0 ]; then
        read -p $'\e[33m\e[1m>>> Is this a new install?\e[0m\e[39m Answering \'yes\' will wipe any existing database. [y/n] ' -n 1 -r
    else
        echo "-- DIRTY DATABASE! Manual intervention required. --"
        REPLY="n"
    fi
    echo    # (optional) move to a new line
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        propel sql:build --overwrite
        propel sql:insert
    else
        existing
    fi
    REPLY=""
    touch ../../.existing-ramble-blog
fi

echo "--- Propel finished. ---"

cd "${0%/*}"

hash gulp || exit
echo "--- Detected gulp. ---"
cd ../.. || exit
gulp batch
if [ $? -ne 0 ]; then
    echo "--- Batch operation, attempting a fix & retrying... ---"
    npm install
    npm rebuild node-sass
    gulp batch
    if [ $? -ne 0 ]; then
        echo "--- The operation failed twice. Try deleting your node_modules folder and running again. ---"
    fi
fi
echo "--- Completely done! ---"
