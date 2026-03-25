# Deployment Checklist

## Environment
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Configure `APP_KEY`
- Use secure `APP_URL` (https)

## Performance
- Use Redis for cache/session/queue (`CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`)
- Run:
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`

## Queue Worker
- Run queue workers via Supervisor/systemd:
  - `php artisan queue:work --tries=3 --timeout=120`

## Database
- Run migrations:
  - `php artisan migrate --force`
