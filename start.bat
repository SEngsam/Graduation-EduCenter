@echo off
cd /d "%~dp0"

where php >nul 2>nul
if errorlevel 1 (
    echo PHP is not installed or not in PATH. Install PHP 8.2 or newer: https://windows.php.net/download
    pause
    exit /b 1
)

php -r "exit(version_compare(PHP_VERSION, '8.2.0', '<') ? 1 : 0);"
if errorlevel 1 (
    echo PHP 8.2 or newer is required.
    php -v
    pause
    exit /b 1
)

php -r "exit(extension_loaded('pdo_sqlite') ? 0 : 1);"
if errorlevel 1 (
    echo The pdo_sqlite extension is off. Open php.ini and remove the ; before: extension=pdo_sqlite
    pause
    exit /b 1
)

if not exist vendor\autoload.php (
    echo Installing packages...
    call composer install --no-interaction
    if errorlevel 1 (
        pause
        exit /b 1
    )
)

if not exist .env (
    copy .env.example .env >nul
    php artisan key:generate --force
)

if not exist database\database.sqlite (
    echo Creating the database...
    type nul > database\database.sqlite
    php artisan migrate --seed --force
)

echo.
echo EduCenter is running on http://127.0.0.1:8000
echo Close this window to stop it.
echo.
start "" cmd /c "timeout /t 2 >nul & start http://127.0.0.1:8000"
php artisan serve --port=8000
pause
