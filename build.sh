#!/bin/sh

php vendor/propel/propel/bin/propel.php config:convert
php vendor/propel/propel/bin/propel.php model:build
php vendor/propel/propel/bin/propel.php sql:build
php vendor/propel/propel/bin/propel.php sql:insert

php composer.phar update -o
echo "Finished!"
