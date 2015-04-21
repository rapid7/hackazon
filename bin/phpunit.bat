@ECHO OFF
SET BIN_TARGET=%~dp0/../vendor/phpunit/phpunit/composer/bin/phpunit
php "%BIN_TARGET%" %*
