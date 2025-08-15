# Stock Analytics Application

A comprehensive stock analytics platform built with Laravel 12 that provides AI-powered trading advice, market insights, and real-time stock monitoring.

## 🚀 Features

### Core Features
- **AI-Powered Stock Advice**: Generate intelligent trading recommendations using advanced technical analysis
- **Market Insights Dashboard**: View top 10 most active and promising stocks
- **Real-time Monitoring**: Automatic tracking of trading advice performance with WIN/LOSS analysis
- **Role-based Access Control**: Support for User, Admin, and Super Admin roles
- **Email Notifications**: Automated email alerts for new requests and trading advice

### Technical Features
- **Advanced Technical Analysis**: RSI, MACD, Bollinger Bands, EMA, VWAP, Stochastic, and more
- **Scalping Analysis**: Specialized indicators optimized for short-term trading (1h/1d timeframes)
- **Background Job Processing**: Queue-based email and advice generation
- **Comprehensive Testing**: PHPUnit unit tests and Puppeteer E2E testing
- **Performance Optimization**: Database indexing and intelligent caching

## 🛠️ Technology Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Database**: MySQL with optimized indexes
- **Frontend**: Blade Templates, Custom CSS, Vanilla JavaScript
- **APIs**: Yahoo Finance, AlphaVantage
- **Testing**: PHPUnit, Puppeteer
- **Queue System**: Laravel Jobs for background processing
- **Email**: Laravel Mail with multiple templates

## 📊 System Requirements

- PHP 8.2 or higher
- MySQL 5.7+ or MySQL 8.0+
- Composer
- Node.js (for Puppeteer testing)
- Apache/Nginx web server

## 🚀 Installation

1. Clone the repository:
```bash
git clone https://github.com/acndigitalenterprise/stock-analytics.git
cd stock-analytics
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies (for testing):
```bash
npm install
```

4. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock_analytics
DB_USERNAME=root
DB_PASSWORD=your_password
```

6. Run database migrations:
```bash
php artisan migrate
```

7. Seed admin user:
```bash
php artisan db:seed --class=AdminUserSeeder
```

8. Start the application:
```bash
php artisan serve
```

## 🔧 Configuration

### Background Processes

Start queue worker for background jobs:
```bash
php artisan queue:work --daemon
```

Start stock monitoring (every 5 minutes):
```bash
while true; do php artisan stock:monitor-prices; sleep 300; done
```

### API Configuration

Configure API keys in `.env`:
```env
ALPHA_VANTAGE_API_KEY=your_alpha_vantage_key
YAHOO_FINANCE_API_KEY=your_yahoo_finance_key
```

## 🧪 Testing

Run PHPUnit tests:
```bash
php artisan test
```

Run Puppeteer E2E tests:
```bash
npm run test:quick      # Quick smoke tests
npm run test:visual     # Visual regression tests
npm run test:auth       # Authentication tests
npm run test:admin      # Admin functionality tests
```

## 👥 User Roles

### User
- Submit stock analysis requests
- View personal trading history
- Access market insights dashboard

### Admin
- Manage all user requests
- View comprehensive user management
- Access full analytics dashboard
- Create/edit/delete requests

### Super Admin
- All admin privileges
- User role management
- System-wide configuration
- Complete user administration

## 📈 AI Trading Analysis

The system provides comprehensive technical analysis including:

### Indicators
- **RSI** (Relative Strength Index)
- **MACD** (Moving Average Convergence Divergence)
- **Bollinger Bands**
- **EMA** (Exponential Moving Average)
- **VWAP** (Volume Weighted Average Price)
- **Stochastic Oscillator**
- **Parabolic SAR**
- **Support/Resistance Levels**

### Scalping Features
- Optimized for 1-hour and 1-day timeframes
- Market hours consideration for Indonesian stocks (IDX)
- Intelligent entry, target, and stop-loss calculation
- Risk management with proper stop-loss positioning

## 🔄 Background Monitoring

The system automatically monitors active trading advice and updates results:

- **WIN**: Target price reached
- **SUPER WIN**: Target 2 price reached
- **LOSS**: Stop loss triggered
- **TIMEOUT**: Monitoring period expired
- **MONITORING**: Currently tracking

## 🚨 Recent Updates

### Version 2.0 - Bug Fixes & Enhancements (August 15, 2025)
- **🐛 Critical Fix**: Stop loss calculation logic corrected (min vs max function)
- **✨ Enhanced UI**: Full Name column added for Admin/Super Admin roles
- **🧹 Project Cleanup**: Removed 25+ unnecessary development files
- **⚡ Performance**: Optimized database queries and indexing
- **🎯 Testing**: Comprehensive Puppeteer test suite implementation

## 📞 Support

For support and questions:
- **Email**: coretechlead@gmail.com
- **GitHub Issues**: [Create an issue](https://github.com/acndigitalenterprise/stock-analytics/issues)

## 📄 License

This project is proprietary software developed by ACN Digital Enterprise.

## 🤝 Contributing

This is a private project. Contact the development team for contribution guidelines.

---

**Developed by ACN Digital Enterprise**  
**© 2025 All Rights Reserved**