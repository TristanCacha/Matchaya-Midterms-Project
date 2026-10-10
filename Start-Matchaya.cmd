@echo off
setlocal
cd /d "%~dp0"
set "PHP_EXE=C:\xampp\php\php.exe"
if not exist "%PHP_EXE%" (
  echo XAMPP PHP was not found at C:\xampp\php\php.exe.
  pause
  exit /b 1
)
"%PHP_EXE%" -d extension=php_intl.dll -d extension=php_gd.dll -S 127.0.0.1:8084 -t public vendor\codeigniter4\framework\system\rewrite.php
pause
endlocal
