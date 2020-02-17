@echo off

:: MakeSM.bat

:: I make standalone, distributable, installations of ScoreMaster
:: ready to be burned to CD/DVD/USB

:: DOS/Windows

setlocal

set SMFLAVOUR=v2.4.1
set PORT=:80

for /f %%x in ('wmic path win32_localtime get /format:list ^| findstr "="') do set %%x
set SMDATE=%Year%-%Month%-%Day%
set CADDYFOLDER=C:\Users\bobst\go\src\github.com\mholt\caddy\caddy\
set PHPFOLDER=C:\PHP
set SMFOLDER=%CADDYFOLDER%sm
set EXECNAME=debugsm.bat
set RBLRCERTS=rblrcerts.sql
set DB2USE=
set OK=

set DESTFOLDER=%1

if "%DESTFOLDER%" == "" goto :HELP

shift
:PARSE
if [%1] == [] goto PARAMSDONE
if /i [%1] == [ok] set OK=ok
if /i [%1] == [v] set DB2USE=VIRGIN
if /i [%1] == [l] set DB2USE=LIVE
if /i [%1] == [r] set DB2USE=RBLR
if /i [%1] == [rblr] set DB2USE=RBLR
shift
goto PARSE
:PARAMSDONE

echo.
echo Making a ScoreMaster ( %SMFLAVOUR% ) installation in %DESTFOLDER%
echo.

if /i [%DB2USE%] == [VIRGIN] goto CHECKDEST
if /i [%DB2USE%] == [RBLR] goto CHECKDEST
if /i [%DB2USE%] == [LIVE] goto USELIVE

echo I normally provide a virgin copy of the database. If you want to supply the current live ScoreMaster.db
echo instead of that or you want to setup for the RBLR please choose that now. 
echo.


choice /c vrl /d v /t 10 /m "Establish Virgin database, Live database or RBLR"
if errorlevel 3 goto USELIVE
set DB2USE=VIRGIN
if errorlevel 2 set DB2USE=RBLR
goto CHECKDEST

:USELIVE
echo Using LIVE ScoreMaster.db
set DB2USE=LIVE

:CHECKDEST
if exist %DESTFOLDER% if /i [%OK%] == [ok] goto ZAPDEST
if not exist %DESTFOLDER% goto STARTCOPYING

set _t=
for /f "delims=" %%a in ('dir /b %DESTFOLDER%') do set _t=%%a
if {%_t%} == {} goto STARTCOPYING

echo.
echo Destination %DESTFOLDER% is not empty, use 'ok' to overwrite
echo.
goto :EOJ

:ZAPDEST
echo.
echo Overwriting %DESTFOLDER%
rmdir %DESTFOLDER% /s /q
if exist %DESTFOLDER% echo FAILED!! && goto EOJ
echo.
:STARTCOPYING

echo.
echo Establishing %DB2USE% ScoreMaster in %DESTFOLDER%
mkdir %DESTFOLDER%
mkdir %DESTFOLDER%\sm
mkdir %DESTFOLDER%\sm\certificates
mkdir %DESTFOLDER%\sm\uploads
mkdir %DESTFOLDER%\caddy
echo Copying components ...
echo     PHP
xcopy %PHPFOLDER% %DESTFOLDER%\php /e /i>nul
echo     PHPSpreadsheet
xcopy %SMFOLDER%\vendor %DESTFOLDER%\sm\vendor /e /i>nul
xcopy %SMFOLDER%\PhpSpreadsheet %DESTFOLDER%\sm\PhpSpreadsheet /e /i>nul
echo     Jodit
mkdir %DESTFOLDER%\sm\jodit

copy %SMFOLDER%\jodit-master\build\jodit.min.js %DESTFOLDER%\sm\jodit\jodit.min.js>nul
copy %SMFOLDER%\jodit-master\build\jodit.min.css %DESTFOLDER%\sm\jodit\jodit.min.css>nul
for %%a in (fields.png,borders.png) do copy %SMFOLDER%\images\icons\%%a %DESTFOLDER%\sm\jodit>nul

echo     images
:: xcopy %SMFOLDER%\images %DESTFOLDER%\sm\images /e /i>nul
mkdir %DESTFOLDER%\sm\images
for %%a in (ibauk.png,ibauk90.png) do copy %SMFOLDER%\images\%%a %DESTFOLDER%\sm\images>nul

if #%DB2USE% == #RBLR for %%a in (ss1000.jpg,smallpoppy.png,rblr.png,poppy.png,
				rblrhead.png,bb1500.jpg,bbg1500.png,route500AC.jpg,route500CW.jpg) do copy %SMFOLDER%\images\%%a %DESTFOLDER%\sm\images>nul
::echo     certificates
:: xcopy %SMFOLDER%\certificates %DESTFOLDER%\sm\certificates /e /i>nul
echo.
echo Copying main SM application ...
for %%a in (about.php,admin.php,bbrspec.php,certificate.php,common.php,
			score.css,setup.php,favicon.ico,bblspec.php,utils.php,
			entrants.php,exportxls.php,importxls.php,index.php,
			jorvicspec.php,licence.txt,rblrspec.php,readme.txt,customvars.php,
			custom.js,score.js,score.php,sm.php,specfiles.php,teams.php,
			certedit.php,reboot.css,certificate.css,speeding.php) do copy %SMFOLDER%\%%a %DESTFOLDER%\sm>nul

echo Copying %DB2USE% database ...
if NOT %DB2USE%==LIVE sqlite3 %DESTFOLDER%\sm\ScoreMaster.db <%SMFOLDER%\scoremaster.sql>nul
if %DB2USE%==RBLR sqlite3 %DESTFOLDER%\sm\ScoreMaster.db <%SMFOLDER%\rblrcerts.sql>nul
if %DB2USE%==LIVE copy %SMFOLDER%\scoremaster.db %DESTFOLDER%\sm\ScoreMaster.db>nul

copy %CADDYFOLDER%\caddy.exe %DESTFOLDER%\caddy>nul
echo *:80 > %DESTFOLDER%\caddy\caddyfile
echo root sm >> %DESTFOLDER%\caddy\caddyfile
echo fastcgi / 127.0.0.1:9000 php >> %DESTFOLDER%\caddy\caddyfile

:: Copy the latest portable binary
copy %SMFOLDER%\runsm.exe %DESTFOLDER%>nul

:: Now build the debug executable
echo @echo off >%DESTFOLDER%\%EXECNAME%
echo cls>> %DESTFOLDER%\%EXECNAME%
echo set CDIR=%%cd%%>> %DESTFOLDER%\%EXECNAME%
echo chdir %%~dp0>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo echo ** IBAUK ScoreMaster  %SMFLAVOUR% as at %SMDATE%>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo set PORT=%PORT%>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo goto :SINGLEUSER>> %DESTFOLDER%\%EXECNAME%
echo set MU=MU>> %DESTFOLDER%\%EXECNAME%
echo echo If you're going to run ScoreMaster on this machine only, please choose Single-user mode>> %DESTFOLDER%\%EXECNAME%
echo echo otherwise, to allow two or more computers at the same time, choose Multi-user mode.>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo if /i #%%1==#s goto SINGLEUSER>> %DESTFOLDER%\%EXECNAME%
echo choice /c smq /t 30 /d s /m "Should I run Single-user or Multi-user (Q=Quit)">> %DESTFOLDER%\%EXECNAME%
echo if errorlevel 3 goto :EOJ>> %DESTFOLDER%\%EXECNAME%
echo if errorlevel 2 goto :MULTIUSER>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo :SINGLEUSER>> %DESTFOLDER%\%EXECNAME%
echo set MU=SU>> %DESTFOLDER%\%EXECNAME%

echo :MULTIUSER>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo echo Starting PHP service>> %DESTFOLDER%\%EXECNAME%
echo set PHP_FCGI_MAX_REQUESTS=0 >> %DESTFOLDER%\%EXECNAME%
echo set t=none>> %DESTFOLDER%\%EXECNAME%
echo for /f "Delims=:-. " %%%%a in ('tasklist /fi "IMAGENAME eq php-cgi.exe" /nh') do if not "%%%%a" == "INFO" set t=%%%%a>> %DESTFOLDER%\%EXECNAME%
echo for /f "Delims=:-. " %%%%a in ('tasklist /fi "IMAGENAME eq php.exe" /nh') do if not "%%%%a" == "INFO" set t=%%%%a>> %DESTFOLDER%\%EXECNAME%
echo if "%%t%%" == "php" goto :SKIPPHP>> %DESTFOLDER%\%EXECNAME%
echo if "%%MU%%"=="SU" start "PHP service for ScoreMaster"  /min php\php -S 127.0.0.1%%PORT%% -t sm -c php\php.ini>> %DESTFOLDER%\%EXECNAME%

echo if "%%MU%%"=="MU" start "PHP service for ScoreMaster" /min php\php-cgi -b 127.0.0.1:9000>> %DESTFOLDER%\%EXECNAME%
echo :SKIPPHP>> %DESTFOLDER%\%EXECNAME%
echo if "%%MU%%"=="SU" goto :WEBDONE>> %DESTFOLDER%\%EXECNAME%
echo echo Starting Webserver>> %DESTFOLDER%\%EXECNAME%
echo echo ^*%%PORT%%^>caddy\caddyfile>> %DESTFOLDER%\%EXECNAME%
echo echo root sm^>^>caddy\caddyfile>> %DESTFOLDER%\%EXECNAME%
echo echo errors caddy/error.log^>^>caddy\caddyfile>> %DESTFOLDER%\%EXECNAME%
echo echo fastcgi / 127.0.0.1:9000 php^>^>caddy\caddyfile>> %DESTFOLDER%\%EXECNAME%
echo start "Webserver for ScoreMaster" /min caddy\caddy.exe -agree -conf caddy\caddyfile>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo :WEBDONE>>%DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo if %%PORT%% == :80 set PORT=>> %DESTFOLDER%\%EXECNAME%
echo echo Now open a browser and visit 'localhost%%PORT%%'>> %DESTFOLDER%\%EXECNAME%
echo start http://localhost%%PORT%%>> %DESTFOLDER%\%EXECNAME%
echo if "%%MU%%"=="SU" goto :WEBDONE2>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo echo If you want to run with multiple users, you'll need to know the IP address of this machine. Clicking the Rally>> %DESTFOLDER%\%EXECNAME%
echo echo Administration option [About ScoreMaster] will show the relevant information which is also shown below.>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo ipconfig ^| find "IPv4 Address">> %DESTFOLDER%\%EXECNAME%
echo echo. >>%DESTFOLDER%\%EXECNAME%
echo :WEBDONE2>>%DESTFOLDER%\%EXECNAME%
echo echo. >>%DESTFOLDER%\%EXECNAME%
echo echo If prompted, please allow access through your firewall.>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo echo If you're nerdy enough you can adjust this startup script to suit yourself. You don't need to run with my webserver,>> %DESTFOLDER%\%EXECNAME%
echo echo almost any one will do. Use any PHP you fancy as long as it supports SQLite and you're happy to validate it.>> %DESTFOLDER%\%EXECNAME%
echo echo You don't need to run under Windows; Linux, Apple, Android and others all offer perfectly good hosting environments.>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo echo.>> %DESTFOLDER%\%EXECNAME%
echo timeout /t 30>> %DESTFOLDER%\%EXECNAME%
echo :EOJ>> %DESTFOLDER%\%EXECNAME%
echo chdir %CDIR%>> %DESTFOLDER%\%EXECNAME%
echo.
echo %DB2USE% ScoreMaster distribution setup in %DESTFOLDER%
echo.
goto :EOJ


:HELP
echo.
echo.
echo %~n0 destinationfolder [ OK ] [ V ^| L ] [ RBLR ]
echo.
echo I make distributions of ScoreMaster but I do need you to specify
echo a destination folder. 
echo.
echo Would you like to have another go
echo.

:EOJ
