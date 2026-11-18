@echo off
if exist "%USERPROFILE%\.config\herd\bin\php85\php.exe" (
    "%USERPROFILE%\.config\herd\bin\php85\php.exe" "%~dp0artisan" %*
) else (
    php "%~dp0artisan" %*
)

