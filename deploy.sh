#!/bin/bash

# RCC Staff Management System - Ubuntu Server Deployment Script
# For servers with existing Apache and PHP installations
# Run this script on your Ubuntu server

echo "🚀 Starting RCC Staff Management System Deployment..."
echo "⚠️  This script assumes Apache and PHP are already installed"

# Check if Apache is running
if ! systemctl is-active --quiet apache2; then
    echo "❌ Apache is not running. Please ensure Apache is installed and running."
    exit 1
fi

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP first."
    exit 1
fi

echo "✅ Apache and PHP detected"

# Update system packages (optional, comment out if you don't want system updates)
echo "📦 Updating package lists..."
sudo apt update

# Install git if not present
if ! command -v git &> /dev/null; then
    echo "🔧 Installing Git..."
    sudo apt install -y git
fi

# Install Composer if not present
if ! command -v composer &> /dev/null; then
    echo "🎼 Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

# Check PHP extensions (don't install if missing, just warn)
echo "🔍 Checking PHP extensions..."
php_extensions=("mysql" "xml" "gd" "curl" "mbstring" "zip" "bcmath" "intl")
missing_extensions=()

for ext in "${php_extensions[@]}"; do
    if ! php -m | grep -q "^$ext$"; then
        missing_extensions+=($ext)
    fi
done

if [ ${#missing_extensions[@]} -ne 0 ]; then
    echo "⚠️  Missing PHP extensions: ${missing_extensions[*]}"
    echo "⚠️  Please install these extensions for your PHP version"
    echo "⚠️  Example: sudo apt install php-mysql php-xml php-gd php-curl php-mbstring php-zip php-bcmath php-intl"
fi

# Create web directory (or use existing one)
echo "📁 Setting up web directory..."
if [ ! -d "/var/www/warcc" ]; then
    sudo mkdir -p /var/www/warcc
fi

# Clone the project
echo "📥 Cloning project from GitHub..."
cd /var/www
if [ -d "warcc" ]; then
    echo "⚠️  Directory /var/www/warcc already exists. Backing up to /var/www/warcc.backup.$(date +%Y%m%d_%H%M%S)"
    sudo mv warcc warcc.backup.$(date +%Y%m%d_%H%M%S)
fi

sudo git clone https://github.com/UfereSamuel/warcc.git warcc
sudo chown -R www-data:www-data /var/www/warcc

# Navigate to project directory
cd /var/www/warcc

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
sudo -u www-data composer install --optimize-autoloader --no-dev

# Create .env file
echo "⚙️ Setting up environment file..."
if [ ! -f ".env" ]; then
    sudo -u www-data cp .env.example .env
    echo "📝 .env file created from .env.example"
else
    echo "⚠️  .env file already exists, skipping creation"
fi

# Generate application key if not set
echo "🔑 Checking application key..."
if ! sudo -u www-data php artisan env:get APP_KEY | grep -q "base64:"; then
    sudo -u www-data php artisan key:generate
    echo "🔑 New application key generated"
else
    echo "🔑 Application key already exists"
fi

# Set proper permissions
echo "🔒 Setting file permissions..."
sudo chown -R www-data:www-data /var/www/warcc
sudo chmod -R 755 /var/www/warcc
sudo chmod -R 775 /var/www/warcc/storage
sudo chmod -R 775 /var/www/warcc/bootstrap/cache

# Enable required Apache modules
echo "🔧 Enabling Apache modules..."
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod expires

echo "✅ Basic setup complete!"
echo ""
echo "🎉 WARCC Staff Management System is ready for installation!"
echo ""
echo "📋 Next steps:"
echo "1. Copy apache-warcc.conf to /etc/apache2/sites-available/"
echo "2. Enable the Apache virtual host: sudo a2ensite apache-warcc.conf"
echo "3. Update your domain DNS to point to this server"
echo "4. Reload Apache: sudo systemctl reload apache2"
echo "5. Open your browser and visit: http://your-domain.com/install.php"
echo "6. Follow the web installer to complete setup"
echo ""
echo "🚀 WEB INSTALLER FEATURES:"
echo "✓ Automatic database creation and setup"
echo "✓ Environment configuration through web interface"
echo "✓ Custom admin account creation"
echo "✓ Email settings configuration"
echo "✓ System requirements checking"
echo "✓ One-click installation process"
echo ""
echo "📂 Configuration files created:"
echo "- apache-warcc.conf (Apache virtual host)"
echo "- .env.production (production environment template)"
echo "- install.php (Web installer - delete after installation)"
echo ""
echo "🌐 Recommended subdomain: warcc.your-domain.com"
echo ""
echo "⚠️  IMPORTANT: Delete install.php and install_steps/ directory after installation for security!"
