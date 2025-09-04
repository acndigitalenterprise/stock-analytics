#!/usr/bin/env bash

# Forge Deployment Script for Ticker AI
# This script will be used by Laravel Forge for deployment

echo "🚀 Starting Ticker AI Deployment..."

cd $FORGE_SITE_PATH

# Ensure we're on the main branch
git pull origin main

# Install/update composer dependencies (production only)
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Run database migrations (if needed)
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear and cache config for better performance
echo "⚡ Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Restart queue workers (important for AI advice system)
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R forge:www-data storage bootstrap/cache

echo "✅ Deployment completed successfully!"

# Check system status
echo "📊 Checking AI advice system status..."
php artisan advice:monitor