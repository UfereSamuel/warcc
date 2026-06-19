# WARCC — Western RCC Staff Management System

Laravel 12 application for **Africa CDC Western Regional Collaborating Centre (WARCC)**. It combines a public website, staff self-service portal, and admin back office in one codebase.

## Features

- **Public website** — homepage CMS, about page, events, media, complaints
- **Staff portal** — attendance, weekly trackers, activity calendar, activity requests & reports
- **Admin back office** — staff/roles, attendance, trackers, CMS, complaints, analytics & PDF reports
- **Microsoft SSO** — staff sign-in via Azure AD (configurable)
- **Email reminders** — overdue activity report reminders via Microsoft Graph (scheduled)

## Requirements

- PHP 8.2+
- Composer 2.x
- Node.js 18+ (for Vite asset builds)
- SQLite (local) or MySQL/MariaDB (production)

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Visit `http://127.0.0.1:8000`.

### Development logins

When `APP_ENV=local` (or `ENABLE_DEV_LOGIN=true`):

- Test accounts page: `/test-accounts`
- One-click login: `/test-login/RCC-001` (super admin), `RCC-002` (staff)

Default super admin (password login): `admin@africacdc.org` / `admin123` — change after first login.

### Seed roles & permissions

On a fresh database:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

This is included in `php artisan db:seed` via `DatabaseSeeder`.

## Testing

```bash
php artisan test
```

Tests use an in-memory SQLite database (`phpunit.xml`).

## Production deployment

See **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** for Apache/MySQL setup, environment variables, cron, and security checklist.

Key production settings in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
ENABLE_DEV_LOGIN=false
REMINDERS_ENABLED=true
```

**Required cron entry** (runs Laravel scheduler for email reminders):

```cron
* * * * * cd /path/to/warcc && php artisan schedule:run >> /dev/null 2>&1
```

Verify scheduled tasks:

```bash
php artisan schedule:list
```

## Documentation

| Document | Purpose |
|----------|---------|
| [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) | Server deployment & production checklist |
| [TESTING_GUIDE.md](TESTING_GUIDE.md) | Manual QA flows and test accounts |

## License

Proprietary — Africa CDC Western RCC.
