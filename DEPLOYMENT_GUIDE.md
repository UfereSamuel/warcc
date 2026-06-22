# RCC Staff Management System - Deployment Guide

## For Ubuntu servers with existing Apache & PHP setup

This guide assumes Apache, PHP 8.2+, and MySQL/MariaDB are available on your server.

## Production checklist

Before go-live, confirm:

- [ ] `APP_ENV=production` and `APP_DEBUG=false`
- [ ] `ENABLE_DEV_LOGIN=false` (disables `/test-accounts` and `/test-login`)
- [ ] Strong `APP_KEY` generated (`php artisan key:generate`)
- [ ] Default super admin password changed
- [ ] Azure AD SSO configured (`MICROSOFT_*` variables) if using staff SSO
- [ ] Microsoft Graph mail sender configured for reminders (`MICROSOFT_MAIL_FROM`)
- [ ] Cron job for `php artisan schedule:run` installed
- [ ] `php artisan migrate --force` completed
- [ ] `php artisan db:seed --class=RolesAndPermissionsSeeder` run (or full `--seed`)
- [ ] `php artisan storage:link` run
- [ ] `php artisan test` passes on staging
- [ ] `public/install.php` removed after web installer (if used)

## Quick deployment steps

### 1. Download and run deployment script

```bash
wget https://raw.githubusercontent.com/UfereSamuel/warcc/main/deploy.sh
chmod +x deploy.sh
sudo ./deploy.sh
```

### 2. Database setup

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE warcc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'warcc_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON warcc.* TO 'warcc_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Configure environment

```bash
cd /var/www/warcc
sudo -u www-data nano .env
```

Essential production settings:

```env
APP_URL=https://warcc.your-domain.com
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Africa/Accra

DB_CONNECTION=mysql
DB_DATABASE=warcc
DB_USERNAME=warcc_user
DB_PASSWORD=your_secure_password

# Security — disable dev test logins in production
ENABLE_DEV_LOGIN=false

# Microsoft SSO (Azure AD app registration)
MICROSOFT_CLIENT_ID=
MICROSOFT_CLIENT_SECRET=
MICROSOFT_TENANT_ID=
MICROSOFT_REDIRECT_URI="${APP_URL}/auth/microsoft/callback"
MICROSOFT_MAIL_FROM=notifications@your-domain.com

# Scheduled email reminders (requires Graph + cron)
REMINDERS_ENABLED=true
REMINDER_ACTIVITY_REPORTS_ENABLED=true
REMINDER_ACTIVITY_REPORT_COOLDOWN_DAYS=3
REMINDER_DAILY_AT=08:00

# Attendance late threshold (24h format)
ATTENDANCE_LATE_AFTER=09:00

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_FROM_ADDRESS=notifications@your-domain.com
```

### 4. Run migrations and seeders

```bash
cd /var/www/warcc
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan db:seed --force
sudo -u www-data php artisan storage:link
```

To re-run roles/permissions only:

```bash
sudo -u www-data php artisan db:seed --class=RolesAndPermissionsSeeder --force
```

### 5. Cache configuration (production)

```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### 6. Apache virtual host

```bash
sudo cp /var/www/warcc/apache-warcc.conf /etc/apache2/sites-available/
sudo nano /etc/apache2/sites-available/apache-warcc.conf
sudo a2ensite apache-warcc.conf
sudo systemctl reload apache2
```

Update `ServerName` and `ServerAlias` to your domain.

### 7. Cron job (required for email reminders)

Laravel's scheduler drives activity report reminders and other scheduled tasks.

```bash
sudo crontab -e -u www-data
```

Add:

```cron
* * * * * cd /var/www/warcc && php artisan schedule:run >> /dev/null 2>&1
```

Verify:

```bash
sudo -u www-data php artisan schedule:list
```

Manual dry-run of reminders:

```bash
sudo -u www-data php artisan reminders:activity-reports --dry-run
```

## Web installer (optional first-time setup)

If using the bundled installer, visit `https://warcc.your-domain.com/install.php` once, then **delete** `public/install.php` and the `install_steps/` directory.

## SSL certificate (recommended)

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d warcc.your-domain.com
```

## Default admin login

After seeding:

- **URL:** `https://warcc.your-domain.com/auth/admin-login`
- **Email:** `admin@africacdc.org`
- **Password:** `admin123`

Change the default password immediately after first login.

## File permissions

```bash
sudo chown -R www-data:www-data /var/www/warcc
sudo chmod -R 755 /var/www/warcc
sudo chmod -R 775 /var/www/warcc/storage
sudo chmod -R 775 /var/www/warcc/bootstrap/cache
```

## Automated tests

Run before each deploy:

```bash
sudo -u www-data php artisan test
```

## Updating the application

```bash
cd /var/www/warcc
sudo -u www-data git pull origin main
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan test
```

### Subdirectory deploy (`https://cbp.africacdc.org/warcc`)

If the app is **not** at the domain root (mounted under `/warcc`), set:

```env
APP_URL=https://cbp.africacdc.org/warcc
```

**Do not run `php artisan route:cache`** in this setup. Laravel’s route cache breaks the homepage with:

> The GET method is not supported for route /. Supported methods: HEAD.

If you already see that error, fix it immediately:

```bash
cd /path/to/warcc
php artisan route:clear
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

If the homepage shows **404 | NOT FOUND** (Laravel page, not Apache), the app is running but the
`/warcc` path prefix is not being stripped before routing. Ensure:

1. `APP_URL=https://cbp.africacdc.org/warcc` in `.env` (no trailing slash)
2. Latest code is deployed (`public/index.php` strips the prefix from `REQUEST_URI`)
3. Caches are cleared:

```bash
cd /var/lib/ACDC_SYSTEMS/warcc
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
# Do NOT run route:cache
```

In `public/.htaccess`, uncomment and set `RewriteBase /warcc` if Apache rewrite rules need it.

Long-term, prefer a **subdomain** with `DocumentRoot` pointing at `public/` (see `apache-warcc.conf`) instead of a subdirectory path.

## Troubleshooting

### 500 error

```bash
sudo tail -f /var/log/apache2/warcc_error.log
sudo -u www-data php artisan config:clear
```

### Permission issues

```bash
cd /var/www/warcc
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache
```

### Reminders not sending

1. Confirm cron is running: `grep schedule:run /var/spool/cron/crontabs/www-data`
2. Check Graph credentials and `MICROSOFT_MAIL_FROM`
3. Run: `php artisan reminders:activity-reports --dry-run`

### Dev routes still accessible

Ensure `ENABLE_DEV_LOGIN=false` and run `php artisan config:cache`.

## Security considerations

1. Use a dedicated subdomain (e.g. `warcc.your-domain.com`)
2. Set `ENABLE_DEV_LOGIN=false` in production
3. Change all default passwords
4. Enable HTTPS
5. Regular database and storage backups
6. Keep Laravel and dependencies updated
7. Remove `install.php` after setup
8. Monitor `storage/logs/laravel.log`

## Support

GitHub: https://github.com/UfereSamuel/warcc
