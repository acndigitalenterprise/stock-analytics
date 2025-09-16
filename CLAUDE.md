# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TickerAI is a Laravel 12-based stock analytics application that provides AI-powered stock analysis and recommendations, primarily focused on Indonesian (IDX) stocks. The application allows users to submit stock analysis requests and receive both deterministic and ChatGPT-powered investment advice.

## Key Commands

### Development
```bash
# Start development server with all services
composer dev
# Or manually:
php artisan serve
php artisan queue:listen --tries=1
npm run dev

# Database operations
php artisan migrate
php artisan db:seed
php artisan tinker

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Testing
```bash
# Run tests
composer test
# Or:
php artisan test
```

### Code Quality
```bash
# Laravel Pint (code formatting)
./vendor/bin/pint
```

### Production Deployment
```bash
# Deploy to production server (SSH-based)
# See MANUAL_DEPLOYMENT_GUIDE.md for detailed steps
ssh forge@206.189.95.134
cd /home/forge/tickerai.app
git pull origin main
php artisan cache:clear && php artisan config:clear
```

## Architecture Overview

### Core Components

**Controllers:**
- `StockAnalyticsController` - Main stock analysis logic and API endpoints
- `AdminController` - Admin dashboard, user management, request management
- `AuthController` - User authentication (signin/signup/password reset)
- `MarketController` - Market data display and refresh
- `SettingsController` - User profile management

**Services:**
- `ChatGPTService` - OpenAI integration for AI stock advice
- `StockService` - Stock data utilities and company name lookups
- `YahooFinanceService` - Yahoo Finance API integration
- `AlphaVantageService` - Alpha Vantage API integration
- `TechnicalAnalysisService` - Technical indicators calculation
- `CacheService` - Redis/cache management for stock data
- `PriceMonitoringService` - Background price monitoring

**Models:**
- `User` - User management with roles (admin/user)
- `Request` - Stock analysis requests with status tracking

**Jobs:**
- `GenerateStockAdvice` - Queued job for AI analysis generation
- `SendStockAdviceEmail` - Email delivery for completed analysis

### Database Structure

- SQLite for development, MySQL for production
- Key tables: `users`, `requests`, plus Laravel cache/jobs tables
- Foreign key relationships: `requests.user_id` → `users.id`
- Indexes optimized for performance on frequently queried columns

### Authentication & Authorization

- Custom session-based authentication (not Laravel's default)
- Middleware: `auth.session`, `admin.access`, `rate_limit`
- Role-based access: `admin` users can manage all users/requests
- Email verification system with token-based verification

### API Integration

**Stock Data Sources:**
1. Yahoo Finance API (primary) - Indonesian stocks (.JK suffix)
2. Alpha Vantage API (fallback)
3. Local company data (final fallback)

**AI Integration:**
- OpenAI ChatGPT for dynamic stock advice
- Fallback deterministic analysis when AI unavailable
- Dual advice system: Claude (deterministic) + ChatGPT (AI-powered)

### Caching Strategy

- Redis/file-based caching for stock search results
- Dashboard statistics caching
- API response caching with configurable TTL
- Cache invalidation on data updates

### Email System

- Comprehensive email templates for all user actions
- Queue-based email delivery
- Email templates: verification, password reset, stock advice, notifications

### Queue System

- Laravel queues for background processing
- Jobs: stock advice generation, email sending
- Configurable retry mechanisms

## Development Patterns

### Stock Code Handling
- Always ensure `.JK` suffix for Indonesian stocks using `StockService::ensureJKFormat()`
- Company name lookups via `StockService::getCompanyName()`

### Error Handling
- Comprehensive logging throughout the application
- Graceful API fallbacks (Yahoo → Alpha Vantage → Local data)
- User-friendly error messages

### Rate Limiting
- API endpoints protected with custom rate limiting middleware
- Different limits for auth vs stock search operations

### Trading Hours Validation
- Market hours checking (09:00-16:00 WIB) for new requests
- Timezone-aware operations using Asia/Jakarta

## Configuration

### Environment Variables
Key configs in `.env`:
- Database: `DB_*` settings
- OpenAI: `OPENAI_API_KEY`, `OPENAI_ORGANIZATION_ID`
- Alpha Vantage: `ALPHAVANTAGE_API_KEY`
- Email: `MAIL_*` settings
- Cache: `CACHE_DRIVER`, `REDIS_*` settings

### Service Configuration
Stock search limits, API timeouts, and fallback behaviors configured in `config/services.php`

## Production Notes

- Application deployed to forge@206.189.95.134 via SSH
- Domain: tickerai.app
- Critical: Ensure all public assets (CSS/JS) are tracked in git
- Monitor logs: `php artisan pail --timeout=0`
- Database backups: Regular SQLite exports

## Testing Approach

- PHPUnit configured for Feature and Unit tests
- SQLite in-memory database for testing
- Test coverage for critical user flows and API integrations