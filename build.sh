#!/bin/sh

php vendor/propel/propel/bin/propel.php config:convert
php vendor/propel/propel/bin/propel.php model:build
php vendor/propel/propel/bin/propel.php diff
php vendor/propel/propel/bin/propel.php migrate

php composer.phar update -o
echo "Finished!"
