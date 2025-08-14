# 📋 QA Session Documentation
**Date:** August 12, 2025  
**Project:** Stock Analytics Application  
**Status:** Ready for QA Testing

## 🎯 Application Overview

**Stock Analytics Request System** - AI-powered stock analysis dengan real-time recommendations

### Core Features:
- **Dynamic Stock Search**: Real-time autocomplete (Yahoo Finance, Alpha Vantage APIs)
- **AI Stock Analysis**: Technical analysis dengan ChatGPT 4.0 (currently using deterministic for consistency)
- **Background Processing**: Queue system untuk advice generation
- **User Management**: Auto user creation, authentication system  
- **Admin Dashboard**: Manage requests, view results
- **Email Notifications**: Automatic confirmations
- **Result Tracking**: WIN/LOSS monitoring system
- **Trading Hours**: 09:00-16:00 WIB validation

### Tech Stack:
- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL (production), SQLite (testing)
- **Queue**: Database driver
- **APIs**: Yahoo Finance, Alpha Vantage, OpenAI
- **Email**: SMTP (mail.gihon7.com)

---

## 🚀 QA Ready Status

### ✅ Completed Tasks:
1. **Database Migration**: All migrations executed, indexes optimized
2. **Test Infrastructure**: Fixed MySQL/SQLite configuration issues
3. **Application Configuration**: Routes cached, config optimized
4. **Queue Workers**: Background processing ready
5. **API Configuration**: All external services configured
6. **Advice Format**: Fixed format consistency issues

### 📊 Database Schema:
**requests table columns:**
- `id`, `full_name`, `mobile_number`, `email`, `stock_code`, `company_name`, `timeframe`
- `advice` (text, nullable) ✅ RUNNING
- `result` (enum: MONITORING/WIN/SUPER_WIN/LOSS/TIMEOUT) ✅ RUNNING  
- `entry_price`, `target_1`, `target_2`, `stop_loss` (decimal)
- `monitoring_until`, `highest_price_reached`, `result_achieved_at`
- `user_id`, `created_at`, `updated_at`

---

## 🌐 QA Login Credentials & URLs

### **Application URLs:**
```bash
Main Application: http://127.0.0.1:8000/stock-analytics
Admin Dashboard:  http://127.0.0.1:8000/stock-analytics/admin  
API Endpoint:     http://127.0.0.1:8000/api/stocks/search?q=BBCA
```

### **Admin Login:**
```bash
Email:    admin@gihon7.com
Password: admin123
Role:     admin
```

### **User Testing:**
- Regular users created automatically when submitting stock requests
- No manual user creation needed

---

## 🧪 QA Test Scenarios

### 1. **Stock Request Submission Flow**
```bash
URL: http://127.0.0.1:8000/stock-analytics
```
**Test Steps:**
1. Type stock code in search box: "BBCA", "TLKM", "NVDA"
2. Select from autocomplete dropdown
3. Fill form: Name, Mobile (+6281234567890), Email
4. Select timeframe: 1h or 1d
5. Submit request
6. Verify redirect to confirmation page
7. Check user auto-creation in database

### 2. **Admin Dashboard Access**
```bash
URL: http://127.0.0.1:8000/stock-analytics/admin
Login: admin@gihon7.com / admin123
```
**Test Steps:**
1. Login with admin credentials
2. View all requests list
3. Test edit request functionality
4. Test delete request functionality
5. View request details with advice
6. Check result tracking (MONITORING/WIN/LOSS)

### 3. **Trading Hours Validation**
**Test Steps:**
1. **Outside Hours** (before 09:00 or after 16:00 WIB):
   - Submit request → Should show error message
   - Error: "Market is closed. Trading hours: 09:00-16:00 WIB"
2. **Within Hours** (09:00-16:00 WIB):
   - Submit request → Should process successfully

### 4. **Stock Search API Testing**
```bash
GET http://127.0.0.1:8000/api/stocks/search?q=BBCA
GET http://127.0.0.1:8000/api/stocks/search?q=TLKM  
GET http://127.0.0.1:8000/api/stocks/search?q=NVDA
```
**Expected Response Format:**
```json
[
  {
    "code": "BBCA.JK",
    "name": "Bank Central Asia Tbk PT",
    "type": "EQUITY",
    "region": "ID"
  }
]
```

### 5. **Rate Limiting Testing**
- Make multiple rapid API calls
- Verify rate limit headers returned
- Test different endpoints with different limits

---

## 📝 Advice Format Specification (Updated)

### **HOLD Format** (Score <4):
```
Current Price: IDR 3,980.00
Price 1 hour ago: IDR 3,900.00 (+2.05%)
Price 30 minutes: IDR 3,940.00 (+1.02%)
Traded Volume: 5,000,000 shares
Action: Hold
Reason:
The stock has a low scalping score of 2/10, indicating limited opportunity for short-term profits. Current market conditions suggest waiting for better entry signals. Technical indicators suggest mixed signals with limited scalping opportunities. Consider waiting for clearer technical signals before entering a position.
```

### **BUY Format** (Score ≥4):
```
Current Price: IDR 3,980.00
Price 1 hour ago: IDR 3,900.00 (+2.05%)
Price 30 minutes: IDR 3,940.00 (+1.02%)
Traded Volume: 5,000,000 shares
Action: Buy
Entry: IDR 3,980.00
Target 1: IDR 4,039.70 (~1.5% profit)
Target 2: IDR 4,099.40 (~3.0% profit)
Stop Loss: IDR 3,900.40 (~2.0% below entry)
Reason:
The stock has a high scalping score of 8/10, indicating a good opportunity for short-term profits. The price is currently above the VWAP and the EMA (9), both of which are bullish signals. The RSI is in the neutral zone at 65, close to the neutral 50 level, suggesting balanced momentum. The Stochastic indicators are in the overbought zone, which in the context of scalping can indicate strong upward momentum. However, the stop loss is set to manage risk and protect against potential market reversals.
```

### **Key Features:**
- ✅ **Current Price**: Real-time stock price
- ✅ **Price 1 hour ago**: Historical comparison with percentage change  
- ✅ **Price 30 minutes**: Mid-term price movement with percentage change
- ✅ **Traded Volume**: Daily trading volume
- ✅ **Action**: Clear Buy/Hold recommendation
- ✅ **Entry/Targets/Stop Loss**: Precise levels with profit percentages (BUY only)
- ✅ **Detailed Technical Reason**: Comprehensive analysis using RSI, VWAP, EMA, Stochastic, Bollinger Bands

---

## 🔧 Technical Configuration

### **AI Configuration:**
- **Model**: ChatGPT 4.0 (gpt-4)
- **API Key**: Configured ✅
- **Max Tokens**: 2000
- **Current Mode**: Deterministic analysis (for format consistency)
- **Fallback**: Always available

### **Database Configuration:**
- **Production**: MySQL (stock_analytics)
- **Testing**: SQLite in-memory  
- **Connection**: 127.0.0.1:3306
- **User**: root (no password)

### **Email Configuration:**
- **SMTP Host**: mail.gihon7.com:465
- **Username**: contact@gihon7.com
- **From Name**: G7 Stock Analytics
- **Encryption**: TLS

### **Queue Configuration:**
- **Driver**: Database
- **Worker Command**: `php artisan queue:work --tries=1 --timeout=30`
- **Background Processing**: Active ✅

---

## 🚨 Common Issues & Solutions

### **Issue 1: Terminal Closed**
**Solution:** Restart development server
```bash
cd C:\xampp\htdocs\stock-analytics
php artisan serve --host=127.0.0.1 --port=8000
```

### **Issue 2: Queue Not Processing**  
**Solution:** Restart queue worker
```bash
php artisan queue:work --tries=1 --timeout=30 &
```

### **Issue 3: Database Connection Error**
**Solution:** Verify MySQL is running and database exists
```bash
php artisan migrate:status
```

### **Issue 4: Wrong Advice Format**
**Solution:** Format is now fixed to deterministic analysis
- Check `app/Services/ChatGPTService.php:22-25`
- Always uses `buildDeterministicAdvice()` method

### **Issue 5: Cache Issues**
**Solution:** Clear application caches
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

## 📋 Quick Start Commands

### **Start Development Environment:**
```bash
cd C:\xampp\htdocs\stock-analytics
php artisan serve --host=127.0.0.1 --port=8000
php artisan queue:work --tries=1 --timeout=30 &
```

### **Run Tests:**
```bash
php artisan test
```

### **Check Application Status:**
```bash
php artisan migrate:status
php artisan config:show database.connections.mysql
php artisan route:list
```

### **Monitor Logs:**
```bash
tail -f storage/logs/laravel.log
```

---

## 🎯 QA Success Criteria

### **Must Pass:**
- [ ] Stock search autocomplete works
- [ ] Stock request submission creates user and advice
- [ ] Admin dashboard login and CRUD operations  
- [ ] Trading hours validation blocks off-hours requests
- [ ] Advice format matches specification (HOLD/BUY)
- [ ] Email notifications logged properly
- [ ] Rate limiting returns proper headers
- [ ] No PHP errors in logs during testing

### **Performance:**
- [ ] Page load times <2 seconds
- [ ] API responses <1 second  
- [ ] Search autocomplete <500ms
- [ ] Queue job processing <30 seconds

### **Security:**
- [ ] Rate limiting prevents abuse
- [ ] CSRF protection active
- [ ] XSS prevention working
- [ ] Input validation prevents injection

---

## 📞 Support Information

**Project Path:** `C:\xampp\htdocs\stock-analytics`  
**Documentation:** This file - `QA_SESSION_DOCUMENTATION.md`  
**Log Files:** `storage/logs/laravel.log`  
**Configuration:** `.env` file  

**Status:** ✅ **READY FOR COMPREHENSIVE QA TESTING**

---

*Generated on August 12, 2025 - QA Preparation Session*