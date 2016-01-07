#!/bin/sh

php composer.phar update -o

php vendor/propel/propel/bin/propel.php config:convert
php vendor/propel/propel/bin/propel.php model:build
php vendor/propel/propel/bin/propel.php diff
php vendor/propel/propel/bin/propel.php migrate

echo "Finished!"
