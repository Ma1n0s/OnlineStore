# О проекте

Интернет магазин Абсолют техно
написан на Nuxt 3 + Laravel 12

# Для Dev запуска проекта

```
cd backend
composer install
php artisan serve
php artisan horizon

cd frontend/nuxt-app
npm install
npm run dev
```

# Для Production

```
cd backend
composer install --optimize-autoloader --no-dev
php artisan optimize:clear
php artisan horizon
sudo chown -R www-data:www-data /var/www/api.absolutetechno.ru/storage/
php artisan storage:link

cd frontend/nuxt-app
npm install
npm run build
```

> В идеале на horizon создать службу

> тестирование redis

```
apt-get install redis-cli
redis-cli ping
```

## установка redis через docker

```
docker pull redis
docker run --name redis -p 6379:6379 -d redis redis-server --save 60 1 --loglevel warning
docker start redis
```

### Для запуска прошлых задач в horizon

```
php artisan queue:retry all
```
