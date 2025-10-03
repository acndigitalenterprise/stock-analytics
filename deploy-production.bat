@echo off
echo Deploying to production server...
echo.

:: SSH to production and update
ssh forge@206.189.95.134 "cd /home/forge/tickerai.app && git pull origin main && php artisan cache:clear && php artisan config:clear && php artisan view:clear && echo 'Production updated successfully!'"

echo.
echo Deployment completed!
pause