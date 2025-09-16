#!/bin/bash

echo "🚀 CRITICAL DEPLOYMENT: Fixing missing assets on tickerai.app"
echo "========================================================"

# Step 1: Pull latest code
echo "1. Pulling latest code from repository..."
git pull origin main

# Step 2: Ensure all asset directories exist
echo "2. Creating missing asset directories..."
mkdir -p public/Dashboard
mkdir -p public/Market
mkdir -p public/Settings
mkdir -p public/Users
mkdir -p public/Requests
mkdir -p public/Home

# Step 3: Verify critical CSS files exist (these should be in repo)
echo "3. Verifying critical CSS files..."
if [ ! -f "public/Dashboard/dashboard.css" ]; then
    echo "❌ CRITICAL: Dashboard/dashboard.css missing!"
fi
if [ ! -f "public/Market/market.css" ]; then
    echo "❌ CRITICAL: Market/market.css missing!"
fi
if [ ! -f "public/Settings/settings.css" ]; then
    echo "❌ CRITICAL: Settings/settings.css missing!"
fi
if [ ! -f "public/Users/users.css" ]; then
    echo "❌ CRITICAL: Users/users.css missing!"
fi
if [ ! -f "public/Requests/requests.css" ]; then
    echo "❌ CRITICAL: Requests/requests.css missing!"
fi

# Step 4: Set proper permissions
echo "4. Setting proper file permissions..."
chmod -R 755 public/
chown -R www-data:www-data public/

# Step 5: Clear Laravel caches
echo "5. Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Step 6: Test critical assets
echo "6. Testing asset availability..."
echo "Testing key CSS files:"
curl -I https://tickerai.app/Settings/settings.css || echo "❌ Settings CSS still missing"
curl -I https://tickerai.app/Dashboard/dashboard.css || echo "❌ Dashboard CSS still missing"
curl -I https://tickerai.app/Market/market.css || echo "❌ Market CSS still missing"

echo ""
echo "✅ DEPLOYMENT COMPLETE!"
echo "🔍 Check https://tickerai.app to verify UI is restored"
echo "========================================================"