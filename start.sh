#!/bin/sh
cd "$(dirname "$0")"

if ! command -v php >/dev/null 2>&1; then
    echo "PHP is not installed. Install PHP 8.2 or newer."
    exit 1
fi

if ! php -r "exit(version_compare(PHP_VERSION, '8.2.0', '<') ? 1 : 0);"; then
    echo "PHP 8.2 or newer is required."
    php -v
    exit 1
fi

if ! php -r "exit(extension_loaded('pdo_sqlite') ? 0 : 1);"; then
    echo "The pdo_sqlite extension is missing (on Ubuntu: sudo apt install php-sqlite3)."
    exit 1
fi

if [ ! -f vendor/autoload.php ]; then
    echo "Installing packages..."
    composer install --no-interaction || exit 1
fi

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --force
fi

if [ ! -f database/database.sqlite ]; then
    echo "Creating the database..."
    touch database/database.sqlite
    php artisan migrate --seed --force
fi

echo ""
echo "EduCenter is running on http://127.0.0.1:8000"
echo "Press Ctrl+C to stop it."
echo ""
(sleep 2 && (open http://127.0.0.1:8000 2>/dev/null || xdg-open http://127.0.0.1:8000 2>/dev/null)) &
php artisan serve --port=8000
