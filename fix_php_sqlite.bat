@echo off
set PHP_INI=D:\php_runtime\php.ini
powershell -Command "(Get-Content %PHP_INI%) -replace ';extension=pdo_sqlite', 'extension=pdo_sqlite' -replace ';extension=sqlite3', 'extension=sqlite3' | Set-Content %PHP_INI%"
