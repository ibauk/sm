@echo off 
cls
set CDIR=%cd%
chdir %~dp0
echo.
echo ** IBAUK ScoreMaster  v2.4.1 as at 2019-10-1
echo.
set PORT=:80
echo.
echo.
set MU=MU
echo If you're going to run ScoreMaster on this machine only, please choose Single-user mode
echo otherwise, to allow two or more computers at the same time, choose Multi-user mode.
echo.
if /i #%1==#s goto SINGLEUSER
choice /c smq /t 30 /d s /m "Should I run Single-user or Multi-user (Q=Quit)"
if errorlevel 3 goto :EOJ
if errorlevel 2 goto :MULTIUSER
echo.
:SINGLEUSER
set MU=SU
:MULTIUSER
echo.
echo Starting PHP service
set PHP_FCGI_MAX_REQUESTS=0 
set t=none
for /f "Delims=:-. " %%a in ('tasklist /fi "IMAGENAME eq php-cgi.exe" /nh') do if not "%%a" == "INFO" set t=%%a
for /f "Delims=:-. " %%a in ('tasklist /fi "IMAGENAME eq php.exe" /nh') do if not "%%a" == "INFO" set t=%%a
if "%t%" == "php" goto :SKIPPHP
if "%MU%"=="SU" start "PHP service for ScoreMaster"  /min php\php -S 127.0.0.1%PORT% -t sm -c php\php.ini
if "%MU%"=="MU" start "PHP service for ScoreMaster" /min php\php-cgi -b 127.0.0.1:9000
:SKIPPHP
if "%MU%"=="SU" goto :WEBDONE
echo Starting Webserver
echo *%PORT%>caddy\caddyfile
echo root sm>>caddy\caddyfile
echo errors caddy/error.log>>caddy\caddyfile
echo fastcgi / 127.0.0.1:9000 php>>caddy\caddyfile
start "Webserver for ScoreMaster" /min caddy\caddy.exe -agree -conf caddy\caddyfile
echo.
:WEBDONE
echo.
if %PORT% == :80 set PORT=
echo Now open a browser and visit 'localhost%PORT%'
start http://localhost%PORT%
if "%MU%"=="SU" goto :WEBDONE2
echo.
echo If you want to run with multiple users, you'll need to know the IP address of this machine. Clicking the Rally
echo Administration option [About ScoreMaster] will show the relevant information which is also shown below.
echo.
ipconfig | find "IPv4 Address"
echo. 
:WEBDONE2
echo. 
echo If prompted, please allow access through your firewall.
echo.
echo.
echo If you're nerdy enough you can adjust this startup script to suit yourself. You don't need to run with my webserver,
echo almost any one will do. Use any PHP you fancy as long as it supports SQLite and you're happy to validate it.
echo You don't need to run under Windows; Linux, Apple, Android and others all offer perfectly good hosting environments.
echo.
echo.
timeout /t 30
:EOJ
chdir 
