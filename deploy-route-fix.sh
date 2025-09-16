#!/bin/bash

# Route Cache Fix Script for Forge Deployment
# Run this via Forge Commands or SSH

echo "🔄 Clearing Laravel Route Cache..."

# Clear all caches
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo "📝 Rebuilding optimized caches..."

# Rebuild optimized caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔧 Restarting PHP-FPM..."

# Restart PHP-FPM (Forge specific)
sudo service php8.2-fpm reload

echo "✅ Route cache fix completed!"
echo "Test URLs:"
echo "- https://tickerai.app/dashboard/"
echo "- https://tickerai.app/requests/"  
echo "- https://tickerai.app/settings/"