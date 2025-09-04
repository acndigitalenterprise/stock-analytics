# Laravel Forge Setup for Ticker AI (tickerai.app)

## Prerequisites
- Laravel Forge account
- Server provider account (DigitalOcean recommended)
- Domain tickerai.app DNS configured
- GitHub/GitLab repository with this code

## Step 1: Create Server on Forge

### Server Specs (Recommended)
- **Provider**: DigitalOcean
- **Server Size**: 2GB RAM, 50GB SSD (minimum for AI processing)
- **PHP Version**: 8.2
- **Database**: MySQL 8.0
- **Node.js**: Latest LTS
- **Region**: Choose closest to your users

### Server Name: `tickerai-production`

## Step 2: Server Configuration

### Install Additional Packages
```bash
# Redis for caching (optional but recommended)
sudo apt-get install redis-server

# Additional PHP extensions if needed
sudo apt-get install php8.2-redis php8.2-curl php8.2-zip
```

## Step 3: Create Site on Forge

### Site Configuration
- **Root Domain**: `tickerai.app`
- **Aliases**: `www.tickerai.app`
- **Web Directory**: `/public`
- **PHP Version**: 8.2
- **Project Type**: Laravel

### Repository
- **Provider**: GitHub/GitLab
- **Repository**: `your-username/ai-stock-analytics`
- **Branch**: `main`

## Step 4: Environment Variables

Copy contents from `.env.production` to Forge Environment panel and update:

```env
# Update these values on Forge:
DB_PASSWORD=YOUR_FORGE_DATABASE_PASSWORD
MAIL_FROM_ADDRESS=noreply@tickerai.app

# Keep your OpenAI keys secure - add them in Forge Environment panel
OPENAI_API_KEY=your_key_here
OPENAI_ORGANIZATION_ID=your_org_here
```

## Step 5: Database Setup

### Create Database
- **Name**: `tickerai_production`
- **User**: `forge` (default)

### Run Migrations
```bash
php artisan migrate --force
```

## Step 6: SSL Certificate

- Enable SSL through Forge (Let's Encrypt)
- Force HTTPS redirect

## Step 7: Queue Workers Setup

### Add Queue Worker in Forge
- **Command**: `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- **User**: `forge`
- **Directory**: `/home/forge/tickerai.app`
- **Processes**: 2

### Add Queue Monitoring (Optional)
- **Command**: `php artisan queue:monitor --sleep=60`

## Step 8: Scheduled Tasks (Cron)

Add these to Forge Scheduler:

```bash
# Monitor stock prices and update results (every 5 minutes during market hours)
*/5 * * * * php artisan stock:monitor-prices

# Monitor advice system health (every hour)  
0 * * * * php artisan advice:monitor

# Clear logs weekly (every Sunday at 2 AM)
0 2 * * 0 php artisan log:clear
```

## Step 9: Deploy

### Deployment Script
Use the provided `forge-deploy.sh` script in Forge deployment settings.

### First Deployment
1. Click "Deploy Now" in Forge
2. Monitor deployment logs
3. Test the application
4. Check queue workers are running

## Step 10: Domain Configuration

### DNS Settings (at your domain registrar)
```
Type: A Record
Name: @
Value: [Your Forge Server IP]

Type: A Record  
Name: www
Value: [Your Forge Server IP]
```

### Security Headers (Optional)
Add to nginx configuration through Forge:

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self' 'unsafe-inline' 'unsafe-eval' *.tickerai.app data: blob: https:;" always;
```

## Step 11: Monitoring & Maintenance

### Health Checks
- **URL**: `https://tickerai.app/dashboard`
- **Database**: Connection test
- **Queue**: `php artisan queue:work` status
- **AI System**: `php artisan advice:monitor`

### Log Monitoring
- Monitor `/storage/logs/` for errors
- Setup log rotation
- Consider external monitoring (Bugsnag, Sentry)

## Production Checklist

- [ ] Server created and configured
- [ ] Domain DNS pointed to server
- [ ] SSL certificate installed  
- [ ] Database created and migrated
- [ ] Environment variables set
- [ ] Queue workers running
- [ ] Scheduled tasks configured
- [ ] Deployment script working
- [ ] Application accessible at tickerai.app
- [ ] AI advice system functional
- [ ] Email notifications working
- [ ] OpenAI API integration working

## Important Notes

1. **OpenAI API Keys**: Keep secure, add usage monitoring
2. **Queue Workers**: Essential for AI advice generation
3. **Database Backups**: Enable automated backups on Forge
4. **Security**: Enable firewall, disable root login
5. **Performance**: Consider Redis caching for better performance

## Support Commands

```bash
# Check application status
php artisan about

# Monitor AI system
php artisan advice:monitor

# Test queue processing
php artisan queue:work --once

# Check logs
tail -f storage/logs/laravel.log

# Restart services
php artisan queue:restart
sudo service nginx restart
sudo service php8.2-fpm restart
```