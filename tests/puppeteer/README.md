# Stock Analytics - Puppeteer Testing Suite

## Overview
Comprehensive visual testing and flow verification system untuk Stock Analytics Laravel application. Sistem ini memungkinkan developer untuk melakukan testing visual dan functional dengan browser automation.

## Features
- 🖥️ **Visual Testing** - Screenshot otomatis untuk setiap test step
- 🔄 **Flow Verification** - Testing complete user journey
- 📱 **Responsive Testing** - Testing pada berbagai viewport
- 📊 **Detailed Reporting** - JSON reports dengan metrics
- 🎯 **Error Detection** - Screenshot otomatis saat test gagal
- 🚀 **Multiple Test Modes** - Smoke test, visual regression, comprehensive testing

## Installation
Puppeteer sudah ter-install sebagai dependency. Pastikan Laravel server berjalan di `http://127.0.0.1:8000`.

## Usage Commands

### Quick Commands
```bash
# Quick smoke test (2-3 menit)
npm run test:quick

# Visual regression test (ambil screenshot semua halaman)
npm run test:visual

# Test authentication flow saja
npm run test:auth

# Test admin features saja  
npm run test:admin

# Comprehensive test (semua test)
npm test
```

### Direct Test Files
```bash
# Test specific area
npm run verify:signin    # Authentication flow tests
npm run verify:dashboard # Admin features tests
```

## Test Categories

### 1. Authentication Flow Tests (`auth-flow-tests.js`)
- ✅ Homepage loading dan display
- ✅ Sign-in dengan invalid credentials (error handling)
- ✅ Sign-in dengan valid credentials
- ✅ Admin dashboard navigation
- ✅ Users page accessibility
- ✅ Logout functionality
- ✅ Responsive design verification

### 2. Admin Features Tests (`admin-features-tests.js`)
- ✅ Dashboard metrics display
- ✅ User management functionality
- ✅ New User modal testing
- ✅ Requests page functionality
- ✅ Profile settings access
- ✅ Mobile responsiveness
- ✅ Navigation consistency

## Screenshots & Reports
Semua screenshot dan report tersimpan di:
```
tests/puppeteer/screenshots/
├── [timestamp]_test_name.png        # Test screenshots
├── FAILED_[test_name].png          # Failure screenshots  
└── test-report.json                # Detailed test report
```

## Configuration
Test runner menggunakan konfigurasi:
- **Base URL**: `http://127.0.0.1:8000`
- **Default Viewport**: 1920x1080
- **Browser Mode**: Non-headless (bisa lihat browser)
- **Default Admin Credentials**: admin@gihon7.com / admin123

## Test Structure
```javascript
// Example test structure
await testRunner.test('Test Name', async () => {
    // Test implementation
    await testRunner.page.goto(url);
    await testRunner.waitForElement(selector);
    await testRunner.takeScreenshot('screenshot_name');
});
```

## Best Practices
1. **Always take screenshots** pada step penting
2. **Use descriptive test names** untuk mudah debugging
3. **Test error states** tidak hanya happy path
4. **Verify responsive design** pada berbagai device
5. **Check both visual dan functional** aspects

## Debugging
- Browser terbuka non-headless untuk visual debugging
- Console logs ditampilkan di terminal
- Screenshot otomatis saat test gagal
- Detailed error messages dengan context

## Integration dengan Development Workflow
```bash
# Setelah implement feature baru
npm run test:quick          # Quick verification

# Sebelum commit
npm run test:visual        # Visual regression check

# Full testing
npm test                   # Comprehensive testing
```

## Example Output
```
🚀 Initializing Stock Analytics Test Runner...
✅ Browser initialized successfully

🧪 Running test: Homepage loads and displays correctly
📸 Screenshot saved: 2025-01-14T10-00-00-000Z_homepage_loaded.png
✅ Test PASSED: Homepage loads and displays correctly (1250ms)

📊 TEST SUMMARY
================
Total Tests: 7
Passed: 7
Failed: 0
Success Rate: 100.0%
```

## Troubleshooting
1. **Laravel server not running**: Pastikan `php artisan serve` berjalan
2. **Browser tidak terbuka**: Check Chrome/Chromium installation
3. **Test timeout**: Increase timeout di test runner configuration
4. **Permission errors**: Run sebagai administrator jika perlu

## Extending Tests
Untuk menambah test baru:

1. Buat file test baru di `tests/puppeteer/`
2. Import `StockAnalyticsTestRunner`
3. Implement test functions
4. Add ke main test suite
5. Update package.json scripts jika perlu

## Visual Verification Workflow
1. Run test suite
2. Check generated screenshots
3. Verify UI tampil dengan benar
4. Check responsive behavior
5. Validate error states
6. Confirm user flows

Sistem ini memungkinkan development yang lebih confident dengan visual verification yang comprehensive!