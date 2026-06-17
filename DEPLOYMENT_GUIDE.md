# RCC Staff Management System - Deployment Guide

## For Ubuntu Servers with Existing Apache & PHP Setup

This guide assumes you already have Apache, PHP, and MySQL running on your Ubuntu server with other Laravel projects.

## Quick Deployment Steps

### 1. Download and Run Deployment Script

```bash
# On your server, download the repository
wget https://raw.githubusercontent.com/UfereSamuel/warcc/main/deploy.sh
chmod +x deploy.sh
sudo ./deploy.sh
```

### 2. Database Setup

Create a new database for the RCC Staff system:

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE warcc;
CREATE USER 'warcc_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON warcc.* TO 'warcc_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Configure Environment

Edit the `.env` file:

```bash
cd /var/www/warcc
sudo -u www-data nano .env
```

Update these essential settings:

```env
APP_URL=https://warcc.your-domain.com
APP_ENV=production
APP_DEBUG=false

DB_DATABASE=warcc
DB_USERNAME=warcc_user
DB_PASSWORD=your_secure_password

# Add your email settings for notifications
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
```

### 4. Setup Apache Virtual Host

Copy the Apache configuration:

```bash
sudo cp /var/www/warcc/apache-warcc.conf /etc/apache2/sites-available/
```

Edit the configuration file to match your domain:

```bash
sudo nano /etc/apache2/sites-available/apache-warcc.conf
```

Update the `ServerName` and `ServerAlias` to your actual domain/subdomain.

Enable the site:

```bash
sudo a2ensite apache-warcc.conf
sudo systemctl reload apache2
```

### 5. Run Database Migrations

```bash
cd /var/www/warcc
sudo -u www-data php artisan migrate --seed
```

### 6. Set up Cron Jobs (Optional)

Add Laravel's scheduler to crontab:

```bash
sudo crontab -e -u www-data
```

Add this line:

```
* * * * * cd /var/www/warcc && php artisan schedule:run >> /dev/null 2>&1
```

## DNS Configuration

Point your subdomain to your server:

- **A Record**: `warcc.your-domain.com` → `your-server-ip`
- **CNAME Record**: `www.warcc.your-domain.com` → `warcc.your-domain.com`

## SSL Certificate (Recommended)

If using Let's Encrypt:

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d warcc.your-domain.com -d www.warcc.your-domain.com
```

## Default Admin Login

After setup, you can login to the admin panel:

- **URL**: `https://warcc.your-domain.com/auth/admin-login`
- **Email**: `admin@africacdc.org`
- **Password**: `admin123`

⚠️ **Important**: Change the default admin password immediately after first login!

## File Permissions

The deployment script sets these permissions:

```bash
sudo chown -R www-data:www-data /var/www/warcc
sudo chmod -R 755 /var/www/warcc
sudo chmod -R 775 /var/www/warcc/storage
sudo chmod -R 775 /var/www/warcc/bootstrap/cache
```

## Troubleshooting

### Common Issues

1. **500 Error**: Check Apache error logs
   ```bash
   sudo tail -f /var/log/apache2/warcc_error.log
   ```

2. **Permission Issues**: Re-run permission commands
   ```bash
   cd /var/www/warcc
   sudo chown -R www-data:www-data .
   sudo chmod -R 775 storage bootstrap/cache
   ```

3. **Database Connection**: Verify `.env` database settings
   ```bash
   sudo -u www-data php artisan config:clear
   sudo -u www-data php artisan config:cache
   ```

### Useful Commands

```bash
# Clear all caches
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear

# Check application status
sudo -u www-data php artisan about

# Run specific seeder
sudo -u www-data php artisan db:seed --class=SuperAdminSeeder
```

## Updating the Application

To update from GitHub:

```bash
cd /var/www/warcc
sudo -u www-data git pull origin main
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data php artisan migrate
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

## Security Considerations

1. **Use a subdomain** to isolate from other applications
2. **Change default passwords** immediately
3. **Set up SSL certificate** for HTTPS
4. **Regular backups** of database and uploaded files
5. **Keep Laravel and dependencies updated**
6. **Monitor logs** regularly

## Support

For issues specific to the RCC Staff Management System, check the GitHub repository:
https://github.com/UfereSamuel/warcc 
