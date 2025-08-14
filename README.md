<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# 📈 Stock Analytics Application

A comprehensive Laravel-based stock analytics platform for Indonesian stock market analysis with AI-powered investment advice and real-time market insights.

## ✨ Key Features

### 🎯 Core Features
- **Real-time Stock Analysis**: Comprehensive analysis for Indonesian stocks (IDX)
- **AI-Powered Investment Advice**: Automated stock recommendations using advanced algorithms
- **Market Insights Dashboard**: View top 10 most active and promising stocks daily
- **Dynamic Stock Search**: Real-time stock code search with intelligent autocomplete
- **Multi-Role User System**: Admin, Super Admin, and User roles with different permissions
- **Email Notifications**: Automated alerts for new requests and user management
- **Mobile-Responsive Design**: Seamless experience on desktop and mobile

### 📊 Market Insights (NEW!)
- **Top Active Stocks**: Real-time data of most traded Indonesian stocks
- **Promising Stocks**: AI-scored recommendations based on technical indicators
- **Yahoo Finance Integration**: Live market data with 30-minute intelligent caching
- **Market Sentiment Analysis**: Technical analysis with price momentum scoring

### 👥 Advanced User Management
- **Comprehensive Analytics**: Track user engagement and request patterns
- **Role-Based Dashboards**: Different dashboard views for each user role
- **Profile Management**: Complete user profile customization
- **Request History**: Detailed tracking of all stock analysis requests

### 🤖 AI & Automation Features
- **Background Job Processing**: Automated advice generation via queue system
- **Technical Analysis Engine**: Volume, momentum, and 52-week positioning analysis
- **Investment Scoring Algorithm**: Proprietary scoring system for stock recommendations
- **Automated Email Workflows**: Smart notification system for all user actions

## Stock Search API

The application now supports dynamic stock search with the following features:

### API Endpoints

- `GET /api/stocks/search?q={query}` - Search stocks by code or company name

### API Sources (in order of priority)

1. **Yahoo Finance API** - Primary source for global stocks
2. **Alpha Vantage API** - Secondary source with broader coverage
3. **Local Database** - Fallback with popular Indonesian and global stocks

### Supported Stock Examples

- **Indonesian Stocks**: BBCA, TLKM, BBRI, GIHON, etc.
- **Global Stocks**: NVDA, AAPL, GOOGL, MSFT, TSLA, etc.

### Features

- **Debounced Search**: 300ms delay to prevent excessive API calls
- **Keyboard Navigation**: Arrow keys, Enter, Escape support
- **Loading Indicators**: Visual feedback during search
- **Error Handling**: Graceful fallback to local data
- **Responsive Design**: Works on desktop and mobile

## Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure database
4. Run migrations: `php artisan migrate`
5. Seed admin user: `php artisan db:seed`
6. Start server: `php artisan serve`

## Configuration

### Required Environment Variables

Add the following to your `.env` file:

```env
# OpenAI API Key (Required for AI Stock Advice)
OPENAI_API_KEY=your_openai_api_key_here

# Optional API Keys for better stock search results
ALPHA_VANTAGE_API_KEY=your_alpha_vantage_key
YAHOO_FINANCE_API_KEY=your_yahoo_key
```

### OpenAI API Setup

1. Get your API key from [OpenAI Platform](https://platform.openai.com/api-keys)
2. Add it to your `.env` file as `OPENAI_API_KEY`
3. The AI advice feature requires this key to function

### Local Development

The application works without API keys using the local fallback data, but AI advice generation requires OpenAI API key.

## Usage

1. Visit `/stock-analytics`
2. Start typing a stock code (e.g., "GIHON", "NVDA", "AAPL")
3. Select from the auto-suggestions
4. Fill in other required fields
5. Submit your request

## Admin Access

- URL: `/stock-analytics/admin`
- Default admin: Check the AdminUserSeeder for credentials

## API Response Format

```json
[
  {
    "code": "NVDA",
    "name": "NVIDIA Corporation",
    "type": "EQUITY",
    "region": "US"
  }
]
```

## Error Handling

- Network timeouts: 5 seconds per API call
- Graceful degradation to local data
- User-friendly error messages
- Console logging for debugging

## Performance

- Debounced search prevents excessive API calls
- Caching can be implemented for frequently searched terms
- Local fallback ensures functionality even without internet
