@echo off

:: ScoreMaster v2.1

:: Windows standalone launcher, PHP server


echo.
echo Launching ScoreMaster ...
start "PHP server for ScoreMaster" /min php\php -S 127.0.0.1:8000 -t sm -c php\php.ini 

start sm.html
echo.
echo Switch to your browser and carry on
echo.
