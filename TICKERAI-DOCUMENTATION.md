# TickerAI - Comprehensive Project Documentation

## Architecture Overview

### Technology Stack

**Backend Framework:**
- Laravel 12 with PHP 8.x
- Composer for dependency management
- Queue system with background job processing
- Artisan commands for automation
- Blade templating engine for views
- MySQL database with structured migrations

**Frontend Technologies:**
- Vanilla JavaScript with ES6+ features
- CSS3 with custom responsive design
- jQuery for DOM manipulation and AJAX
- Custom UI components with consistent styling
- Mobile-first responsive design approach

**AI Integration:**
- Claude AI (Anthropic) for stock analysis
- ChatGPT (OpenAPI) for comparative insights
- Custom prompt engineering for Indonesian stocks
- Parallel AI processing for comprehensive advice

**External APIs:**
- Yahoo Finance API for real-time stock data
- Alpha Vantage API for fallback data
- Technical analysis indicators processing
- IDX (Indonesian Stock Exchange) focus

### Core Components

**Web Pages:**
- `home.blade.php` - Landing page with stock analytics form
- `signin.blade.php` - User authentication portal
- `signup.blade.php` - New user registration
- `dashboard.blade.php` - Admin analytics dashboard
- `requests/index.blade.php` - Request management interface
- `requests/requestdetail.blade.php` - Individual request analysis
- `market.blade.php` - Market insights and trends
- `settings.blade.php` - User profile management

**API Endpoints:**
- `/api/stocks/search` - Stock symbol search and validation
- `/api/stocks/submit` - New analysis request submission
- `/requests/{id}/advice` - AI analysis generation trigger
- `/admin/process-timeouts` - Manual timeout processing
- `/emergency/process-timeouts` - Emergency system recovery
- `/market/refresh` - Market data refresh

**Background Services:**
- `GenerateStockAdvice.php` - AI analysis job processor
- `SendResetPasswordEmail.php` - Email delivery jobs
- `PriceMonitoringService.php` - Real-time price tracking
- `CacheService.php` - Performance optimization layer

### Database Structure

**MySQL Database with Laravel Migrations:**
- `users` - User accounts with role-based access
- `requests` - Stock analysis requests with full lifecycle
- `companies` - Indonesian stock company database
- `jobs` - Laravel queue job tracking
- `failed_jobs` - Failed job recovery and debugging
- `sessions` - User session management
- `password_resets` - Secure password recovery tokens

**Key Database Fields:**
```sql
requests:
- id, created_at, updated_at
- full_name, mobile_number, email, user_id
- stock_code, company_name, timeframe ('1h'|'1d')
- advice, advice_chatgpt (AI analysis results)
- result ('MONITORING'|'WIN'|'SUPER_WIN'|'LOSS'|'TIMEOUT'|'HOLD')
- entry_price, target_1, target_2, stop_loss
- monitoring_until, highest_price_reached, result_achieved_at
```

### Authentication & Authorization

**User Management:**
- Email-based registration with auto-verification
- Password hashing with Laravel's bcrypt
- Session-based authentication system
- Role-based access: `user`, `admin`, `super_admin`
- Mobile number optional validation

**Security Features:**
- CSRF protection on all forms
- Rate limiting on authentication endpoints
- Input validation with custom Laravel requests
- SQL injection prevention with Eloquent ORM
- XSS protection with Blade templating

### Business Logic Features

**AI-Powered Stock Analysis:**
- Dual AI analysis (Claude + ChatGPT) for comprehensive insights
- Technical analysis with price targets and stop losses
- Indonesian stock market specialization (IDX focus)
- Custom prompt engineering for local market conditions
- Real-time data integration with analysis

**Timeframe-Based Analysis:**
1. **1 Hour (1h)** - Short-term trading signals
2. **1 Day (1d)** - Daily trading recommendations
   - Advanced technical analysis
   - Longer processing time optimization
   - Comprehensive market data evaluation

**Real-Time Monitoring System:**
- Automatic price tracking during market hours (09:00-16:00 WIB)
- Target achievement detection (WIN/SUPER_WIN)
- Stop loss monitoring (LOSS)
- Automatic timeout after selected timeframe
- Background monitoring with cron job scheduling

**Performance Tracking:**
- WIN rate statistics and accuracy metrics
- Price achievement tracking (highest_price_reached)
- Result categorization with timestamps
- Admin dashboard with comprehensive analytics

### API Integration

**Stock Data Sources:**
- **Yahoo Finance API** - Primary data source for Indonesian stocks
- **Alpha Vantage API** - Backup data source with API key
- **Local Fallback** - Cached company database for offline operation
- Intelligent failover system between data sources

**AI Services:**
- **Claude AI (Anthropic)** - Primary analysis engine
- **ChatGPT (OpenAI)** - Secondary analysis for comparison
- Custom prompt templates for Indonesian market
- Error handling and retry logic for API failures

**Email Services:**
- **SMTP Integration** - Hostinger email configuration
- **Queue-Based Delivery** - Background email processing
- **Password Reset** - Secure token-based recovery
- **Analysis Notifications** - Request completion alerts

### Queue System Architecture

**Laravel Queue Configuration:**
- Database-driven queue system
- Background job processing with timeouts
- Automatic retry logic (3 attempts)
- Failed job recovery and logging
- Timeframe-specific timeout settings:
  - 1h requests: 120 seconds timeout
  - 1d requests: 300 seconds timeout

**Monitoring Commands:**
```bash
php artisan stock:monitor-prices    # Real-time price monitoring
php artisan stock:process-timeouts  # Timeout processing
php artisan queue:work             # Process background jobs
php artisan queue:failed           # View failed jobs
```

### Caching Strategy

**Multi-Layer Caching:**
- **Stock Search Results** - 15-minute cache for search queries
- **Market Insights** - 15-minute cache for dashboard data
- **Company Database** - Static cache for local fallback
- **Configuration Cache** - Laravel config optimization

**Cache Implementation:**
- Laravel Cache facade with file/database drivers
- Intelligent cache invalidation
- Source-aware caching (Yahoo/Alpha Vantage/Local)
- Performance metrics and hit rate tracking

### Development Patterns

**Code Organization:**
- **Controllers** - Request handling and response formatting
- **Services** - Business logic separation (StockService, CacheService)
- **Jobs** - Background processing with queue integration
- **Requests** - Form validation and input sanitization
- **Middleware** - Authentication and rate limiting

**Error Handling:**
- Comprehensive try-catch blocks
- Laravel logging with contextual information
- User-friendly error messages
- Admin error notifications
- Failed job recovery mechanisms

**Data Validation:**
- Custom Form Request classes
- Real-time input validation
- Stock code format verification (.JK suffix handling)
- Timeframe validation and normalization

### Service Configuration

**Laravel Configuration Files:**
- `config/app.php` - Application settings
- `config/database.php` - Database connections
- `config/queue.php` - Queue system configuration
- `config/mail.php` - Email service settings
- `config/services.php` - External API configurations

**Environment Variables:**
```env
YAHOO_FINANCE_ENABLED=true
ALPHAVANTAGE_API_KEY=your_key
CLAUDE_API_KEY=your_key
OPENAI_API_KEY=your_key
MAIL_HOST=smtp.hostinger.com
```

## Development Notes

### Project Structure
```
├── app/
│   ├── Http/Controllers/     # Request handlers
│   │   ├── AuthController.php
│   │   ├── StockAnalyticsController.php
│   │   └── AdminController.php
│   ├── Jobs/                 # Background jobs
│   │   ├── GenerateStockAdvice.php
│   │   └── SendResetPasswordEmail.php
│   ├── Services/             # Business logic
│   │   ├── YahooFinanceService.php
│   │   ├── PriceMonitoringService.php
│   │   └── CacheService.php
│   ├── Models/               # Database models
│   │   ├── User.php
│   │   └── Request.php
│   └── Console/Commands/     # Artisan commands
├── resources/views/          # Blade templates
├── public/                   # Static assets
├── database/migrations/      # Database schema
└── routes/web.php           # Application routes
```

### Key Development Guidelines

**Database Operations:**
- Use Eloquent ORM for all database interactions
- Implement proper relationship definitions
- Test migrations with `php artisan migrate:refresh`
- Use seeders for development data

**Queue Job Development:**
- Implement `ShouldQueue` interface
- Add timeout and retry properties
- Include comprehensive logging
- Handle failures with `failed()` method

**API Integration:**
- Implement fallback mechanisms
- Add timeout configurations
- Log API requests and responses
- Handle rate limiting gracefully

**Frontend Development:**
- Follow existing CSS class naming conventions
- Implement responsive design principles
- Add proper loading states
- Include error handling for AJAX calls

### Stock Market Specific Features

**Indonesian Stock Exchange (IDX) Focus:**
- Stock code normalization (.JK suffix handling)
- Trading hours validation (09:00-16:00 WIB)
- Local company database with Indonesian names
- Currency formatting (IDR) and number localization

**Technical Analysis Integration:**
- OHLCV data processing
- Moving averages and technical indicators
- Price target calculations
- Risk management with stop losses

**Market Hours Compliance:**
- Automatic trading hours detection
- Request submission restrictions
- Background monitoring during market hours
- After-hours processing limitations

## Production Deployment

### XAMPP Local Development
- Apache web server configuration
- MySQL database with phpMyAdmin
- PHP 8.x with required extensions
- Composer dependency management

### Production Server Setup
- Linux-based hosting environment
- MySQL database with production credentials
- Queue worker as system service
- Cron job scheduling for automation

### Cron Job Configuration
```bash
# Monitor stock prices every 5 minutes during trading hours
*/5 9-16 * * 1-5 cd /path/to/app && php artisan stock:monitor-prices

# Process timeouts every hour
0 * * * * cd /path/to/app && php artisan stock:process-timeouts

# Emergency timeout processing every 30 minutes
*/30 * * * * cd /path/to/app && php artisan stock:process-timeouts
```

### Performance Optimization
- Laravel config caching (`php artisan config:cache`)
- Route caching for production
- Database query optimization
- Asset minification and compression

### Monitoring & Logging
- Laravel log files with rotation
- Queue job monitoring and alerts
- Failed job tracking and recovery
- Performance metrics collection

## Testing Approach

**Manual Testing Workflows:**
- Stock analysis request submission
- AI analysis generation and display
- Real-time monitoring system verification
- Timeout processing validation

**System Integration Testing:**
- Yahoo Finance API connectivity
- Email delivery functionality
- Queue system processing
- Database integrity verification

**Business Logic Testing:**
- Price target achievement detection
- Stop loss triggering
- Timeframe-based timeout processing
- WIN rate calculation accuracy

## Security Considerations

**Data Protection:**
- User password hashing with bcrypt
- Email verification for new accounts
- Secure session management
- Input sanitization and validation

**API Security:**
- Rate limiting on public endpoints
- CSRF token validation
- SQL injection prevention
- XSS protection with Blade templating

**Authentication Security:**
- Password complexity requirements
- Account lockout after failed attempts
- Secure password reset tokens
- Session timeout management

## Troubleshooting

### Common Issues

**Queue System Problems:**
- **Stuck Jobs:** Restart queue worker with `php artisan queue:restart`
- **Failed Jobs:** Check logs and retry with `php artisan queue:retry`
- **Memory Issues:** Increase PHP memory limit in configuration

**API Integration Issues:**
- **Yahoo Finance Timeout:** Check network connectivity and API limits
- **AI Analysis Errors:** Verify API keys and quota limits
- **Email Delivery:** Confirm SMTP settings and authentication

**Database Problems:**
- **Migration Errors:** Check database permissions and syntax
- **Connection Issues:** Verify database credentials and server status
- **Performance:** Analyze slow queries and add indexes

### Development Tools
- **Laravel Telescope** - Application debugging and monitoring
- **Artisan Tinker** - Interactive PHP shell for testing
- **Database Seeder** - Generate test data for development
- **Queue Monitor** - Real-time job processing visualization

### Debugging Commands
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Database operations
php artisan migrate:status
php artisan db:seed --class=CompanySeeder

# Queue debugging
php artisan queue:failed
php artisan queue:retry all

# Log monitoring
tail -f storage/logs/laravel.log
```

## Business Intelligence

### Analytics Dashboard
- **Request Volume** - Daily/weekly/monthly submission trends
- **AI Accuracy** - WIN rate percentage and performance metrics
- **User Engagement** - Active users and retention rates
- **Market Performance** - Stock analysis success rates

### Reporting Features
- **Admin Dashboard** - Comprehensive business metrics
- **User Analytics** - Individual performance tracking
- **Market Insights** - Top performing stocks and trends
- **System Health** - Queue status and error rates

### Data Export
- **CSV Export** - Request data for external analysis
- **Excel Reports** - Formatted business intelligence reports
- **API Access** - Programmatic data access for integrations

## Future Enhancements

### Planned Features
- **Mobile Application** - Native iOS/Android apps
- **Real-time Alerts** - Push notifications for price targets
- **Portfolio Tracking** - Multi-stock portfolio management
- **Advanced Analytics** - Machine learning insights
- **Social Features** - Community analysis sharing

### Technical Improvements
- **Microservices Architecture** - Service separation for scalability
- **Redis Integration** - Advanced caching and session storage
- **Docker Containerization** - Consistent deployment environments
- **API Rate Limiting** - Advanced quota management
- **Real-time WebSockets** - Live price updates

---

*This documentation serves as a comprehensive guide for developers working on the TickerAI project. It covers architecture, development patterns, deployment procedures, and troubleshooting guidelines for maintaining and extending the application.*