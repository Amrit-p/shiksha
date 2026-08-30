## Tech stack

- Laravel 13
- PHP 8.4
- MySQL 8.0

---

## Setup

Configure the environment once:

```bash
cp .env.example .env
php artisan key:generate
# edit .env → set DB_DATABASE / DB_USERNAME / DB_PASSWORD, APP_URL, APP_ENV, APP_DEBUG
```

### Development

```bash
composer install
npm install
php artisan migrate --seed
php artisan storage:link
npm run dev          # compile assets + watch
php artisan serve    # http://127.0.0.1:8000
```

Set `APP_ENV=local` and `APP_DEBUG=true`.

### Production

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --force      # first deploy only
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
# then serve public/ via Nginx or Apache (php-fpm)
```

Set `APP_ENV=production` and `APP_DEBUG=false`. Re-run `php artisan optimize` after `.env` changes; `php artisan optimize:clear` flushes all caches.
