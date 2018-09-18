@echo off

set CADDYFOLDER=C:\Users\bobst\go\src\github.com\mholt\caddy\caddy\
set USECADDY=%1

if "%USECADDY%" == "mu" goto :STARTCADDY

if NOT "%USECADDY%" == "" goto :HELP

:STARTLOCALPHP

start "PHP server for ScoreMaster" /min c:\php\php -S 127.0.0.1:80 -t C:\Users\bobst\go\src\github.com\mholt\caddy\caddy\sm -c c:\php\php.ini 

goto :EOJ

:STARTCADDY
start c:\php\php-cgi -b 192.168.1.147:9000
:: cd C:\Users\bobst\go\src\github.com\mholt\caddy\caddy\
start %CADDYFOLDER%caddy.exe -conf %CADDYFOLDER%caddyfile

:HELP
echo %0 [mu]
echo.
echo mu means multi-user and will fire up Caddy
echo.
echo otherwise I'll just run in development mode under PHP

:EOJ
