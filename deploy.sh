#!/bin/bash

# WARCC Staff Management System - Ubuntu Server Deployment Script
# For servers with existing Apache and PHP installations

set -e

echo "🚀 Starting WARCC Staff Management System Deployment..."
echo "⚠️  This script assumes Apache and PHP 8.2+ are already installed"

if ! systemctl is-active --quiet apache2; then
    echo "❌ Apache is not running. Please ensure Apache is installed and running."
    exit 1
fi

if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP first."
    exit 1
fi

echo "✅ Apache and PHP detected"

echo "📦 Updating package lists..."
sudo apt update

if ! command -v git &> /dev/null; then
    echo "🔧 Installing Git..."
    sudo apt install -y git
fi

if ! command -v composer &> /dev/null; then
    echo "🎼 Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

echo "🔍 Checking PHP extensions..."
php_extensions=("pdo_mysql" "xml" "gd" "curl" "mbstring" "zip" "bcmath" "intl")
missing_extensions=()

for ext in "${php_extensions[@]}"; do
    if ! php -m | grep -qi "^${ext}$"; then
        missing_extensions+=("$ext")
    fi
done

if [ ${#missing_extensions[@]} -ne 0 ]; then
    echo "⚠️  Missing PHP extensions: ${missing_extensions[*]}"
    echo "⚠️  Install before production use, e.g.:"
    echo "    sudo apt install php-mysql php-xml php-gd php-curl php-mbstring php-zip php-bcmath php-intl"
fi

echo "📁 Setting up web directory..."
if [ ! -d "/var/www/warcc" ]; then
    sudo mkdir -p /var/www/warcc
fi

echo "📥 Cloning project from GitHub..."
cd /var/www
if [ -d "warcc/.git" ]; then
    echo "⚠️  Existing installation found — pulling latest changes"
    cd warcc
    sudo -u www-data git pull origin main
else
    if [ -d "warcc" ]; then
        echo "⚠️  Directory /var/www/warcc exists without git — backing up"
        sudo mv warcc "warcc.backup.$(date +%Y%m%d_%H%M%S)"
    fi
    sudo git clone https://github.com/UfereSamuel/warcc.git warcc
    cd warcc
fi

echo "📦 Installing PHP dependencies..."
sudo -u www-data composer install --optimize-autoloader --no-dev

echo "⚙️ Setting up environment file..."
if [ ! -f ".env" ]; then
    sudo -u www-data cp .env.example .env
    echo "📝 .env created — edit before go-live (see DEPLOYMENT_GUIDE.md)"
fi

echo "🔑 Checking application key..."
if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    sudo -u www-data php artisan key:generate --force
    echo "🔑 Application key generated"
fi

echo "🔗 Storage symlink..."
sudo -u www-data php artisan storage:link 2>/dev/null || true

echo "🗄️  Running migrations (skipped if DB not configured yet)..."
if sudo -u www-data php artisan migrate --force 2>/dev/null; then
    echo "✅ Migrations complete"
    sudo -u www-data php artisan db:seed --force 2>/dev/null || echo "⚠️  Seeding skipped or failed — run manually after DB setup"
else
    echo "⚠️  Migrations skipped — configure DB_* in .env first, then run:"
    echo "    php artisan migrate --force && php artisan db:seed --force"
fi

echo "⚡ Caching configuration..."
sudo -u www-data php artisan config:cache 2>/dev/null || true
sudo -u www-data php artisan route:cache 2>/dev/null || true
sudo -u www-data php artisan view:cache 2>/dev/null || true

echo "🔒 Setting file permissions..."
sudo chown -R www-data:www-data /var/www/warcc
sudo chmod -R 755 /var/www/warcc
sudo chmod -R 775 /var/www/warcc/storage
sudo chmod -R 775 /var/www/warcc/bootstrap/cache

echo "🔧 Enabling Apache modules..."
sudo a2enmod rewrite headers expires 2>/dev/null || true

echo ""
echo "✅ WARCC deployment script complete!"
echo ""
echo "📋 Next steps (see DEPLOYMENT_GUIDE.md):"
echo "1. Configure .env — set APP_ENV=production, APP_DEBUG=false, ENABLE_DEV_LOGIN=false"
echo "2. Configure MySQL credentials and run: php artisan migrate --force && php artisan db:seed --force"
echo "3. Copy and enable apache-warcc.conf, reload Apache"
echo "4. Add cron: * * * * * cd /var/www/warcc && php artisan schedule:run >> /dev/null 2>&1"
echo "5. Configure Microsoft SSO (MICROSOFT_*) and Graph mail (MICROSOFT_MAIL_FROM)"
echo "6. Run: php artisan test"
echo "7. Remove public/install.php after web installer (if used)"
echo ""
echo "🌐 Admin login: /auth/admin-login (admin@africacdc.org — change default password!)"
