@echo off
%~dp0\..\..\bin\php\php.exe vendor\propel\propel\bin\propel.php config:convert
%~dp0\..\..\bin\php\php.exe vendor\propel\propel\bin\propel.php model:build
%~dp0\..\..\bin\php\php.exe vendor\propel\propel\bin\propel.php sql:build
%~dp0\..\..\bin\php\php.exe vendor\propel\propel\bin\propel.php sql:insert

set gitdir=E:\Lupo\MyApps\GitPortable\App\Git
set path=%gitdir%\cmd;%path%

%~dp0\..\..\bin\php\php.exe composer.phar update --prefer-dist -o

pause