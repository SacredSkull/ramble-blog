@echo off
php.exe composer.phar update --prefer-dist -o

cd Config\Propel
php.exe ..\..\vendor\propel\propel\bin\propel.php config:convert
php.exe ..\..\vendor\propel\propel\bin\propel.php model:build
php.exe ..\..\vendor\propel\propel\bin\propel.php sql:build
php.exe ..\..\vendor\propel\propel\bin\propel.php sql:insert


pause
