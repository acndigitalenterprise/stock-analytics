# Production Cron Job Setup for TickerAI

## Commands to run on production server:

```bash
# 1. Edit crontab
crontab -e

# 2. Add these lines:
# Monitor stock prices every 5 minutes during trading hours (9 AM - 4 PM WIB)
*/5 9-16 * * 1-5 cd /home/tickerai/public_html && php artisan stock:monitor-prices >> /home/tickerai/logs/monitoring.log 2>&1

# Process timeouts every hour to catch any missed timeouts
0 * * * * cd /home/tickerai/public_html && php artisan stock:process-timeouts >> /home/tickerai/logs/timeout.log 2>&1

# Emergency timeout check every 30 minutes
*/30 * * * * cd /home/tickerai/public_html && php artisan stock:process-timeouts >> /home/tickerai/logs/emergency.log 2>&1
```

## Manual test commands:
```bash
# Test monitoring
cd /home/tickerai/public_html && php artisan stock:monitor-prices

# Test timeout processing
cd /home/tickerai/public_html && php artisan stock:process-timeouts

# Check Laravel scheduler
cd /home/tickerai/public_html && php artisan schedule:list
```