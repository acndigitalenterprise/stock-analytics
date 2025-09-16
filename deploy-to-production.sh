#!/bin/bash

echo "🚀 DEPLOYING TO PRODUCTION SERVER: tickerai.app"
echo "=============================================="

SERVER="forge@206.189.95.134"
APP_PATH="/home/forge/tickerai.app"

echo "🔍 Step 1: Checking current status on production..."

# Create deployment commands
DEPLOY_COMMANDS="
cd $APP_PATH
echo 'Current directory:'
pwd
echo ''
echo 'Git status before pull:'
git status --porcelain
echo ''
echo 'Current commit:'
git log -1 --oneline
echo ''
echo '📥 Pulling latest changes...'
git pull origin main
echo ''
echo 'New commit after pull:'
git log -1 --oneline
echo ''
echo '🗂️ Checking if assets are now available...'
ls -la public/Settings/ || echo 'Settings folder not found'
ls -la public/Dashboard/ || echo 'Dashboard folder not found'
ls -la public/Market/ || echo 'Market folder not found'
echo ''
echo '🧹 Clearing Laravel caches...'
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo ''
echo '🔧 Setting proper permissions...'
chmod -R 755 public/
echo ''
echo '✅ Deployment complete!'
echo ''
echo '🔍 Testing asset availability...'
curl -I https://tickerai.app/Settings/settings.css || echo 'Settings CSS test failed'
echo ''
curl -I https://tickerai.app/Dashboard/dashboard.css || echo 'Dashboard CSS test failed'
"

echo "Deployment commands prepared. You need to run this on the production server:"
echo "----------------------------------------"
echo "$DEPLOY_COMMANDS"
echo "----------------------------------------"

echo ""
echo "📋 MANUAL DEPLOYMENT INSTRUCTIONS:"
echo "1. SSH to server: ssh forge@206.189.95.134"
echo "2. Enter sudo password: ^3spx#)fuf{+a<_:hVQQ"
echo "3. Navigate to app: cd /home/forge/tickerai.app"
echo "4. Pull changes: git pull origin main"
echo "5. Clear cache: php artisan cache:clear && php artisan config:clear && php artisan view:clear"
echo "6. Fix permissions: chmod -R 755 public/"
echo "7. Test: curl -I https://tickerai.app/Settings/settings.css"
echo ""
echo "🎯 Expected result: All CSS files should return HTTP 200 OK"