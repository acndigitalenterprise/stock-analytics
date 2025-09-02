const puppeteer = require('puppeteer');

async function testUsersPageDetailed() {
    let browser;
    
    try {
        console.log('🚀 Detailed test of users page...\n');
        
        browser = await puppeteer.launch({ 
            headless: false,
            defaultViewport: { width: 1200, height: 800 },
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });
        
        const page = await browser.newPage();
        const baseUrl = 'http://tickerai.local';
        
        // Login first
        console.log('🔐 Logging in...');
        await page.goto(`${baseUrl}/signin`, { waitUntil: 'networkidle0', timeout: 15000 });
        await page.waitForSelector('input[name="email"]');
        await page.type('input[name="email"]', 'coretechlead@gmail.com');
        await page.type('input[name="password"]', 'PassAman@2025');
        await page.click('button[type="submit"]');
        await new Promise(resolve => setTimeout(resolve, 3000));
        
        // Now test users page
        console.log('👥 Accessing users page...');
        await page.goto(`${baseUrl}/users`, { waitUntil: 'networkidle0', timeout: 15000 });
        
        // Take a screenshot first
        await page.screenshot({ path: 'users_page_screenshot.png', fullPage: true });
        console.log('📸 Screenshot saved as users_page_screenshot.png');
        
        // Get page content
        const pageContent = await page.evaluate(() => {
            return {
                title: document.title,
                url: window.location.href,
                bodyText: document.body.innerText.substring(0, 500),
                hasTable: !!document.querySelector('table'),
                hasUsers: !!document.querySelector('.users'),
                hasAdminContent: !!document.querySelector('.admin-content'),
                hasError: document.body.innerText.includes('Error'),
                hasServerError: document.body.innerText.includes('Internal Server Error'),
                hasMiddlewareError: document.body.innerText.includes('admin.access'),
                hasBindingError: document.body.innerText.includes('BindingResolutionException')
            };
        });
        
        console.log('📊 PAGE ANALYSIS:');
        console.log('='.repeat(50));
        console.log(`URL: ${pageContent.url}`);
        console.log(`Title: ${pageContent.title}`);
        console.log(`Has Table: ${pageContent.hasTable}`);
        console.log(`Has Users Content: ${pageContent.hasUsers}`);
        console.log(`Has Admin Content: ${pageContent.hasAdminContent}`);
        console.log(`Has Error: ${pageContent.hasError}`);
        console.log(`Has Server Error: ${pageContent.hasServerError}`);
        console.log(`Has Middleware Error: ${pageContent.hasMiddlewareError}`);
        console.log(`Has Binding Error: ${pageContent.hasBindingError}`);
        console.log('\n📝 FIRST 500 CHARS OF PAGE:');
        console.log('-'.repeat(50));
        console.log(pageContent.bodyText);
        console.log('='.repeat(50));
        
        // Check specific error details
        if (pageContent.hasServerError) {
            console.log('❌ CONFIRMED: Internal Server Error detected');
            
            if (pageContent.hasMiddlewareError && pageContent.hasBindingError) {
                console.log('❌ CONFIRMED: admin.access middleware binding error');
                console.log('🔧 DIAGNOSIS: Apache still using old cached middleware configuration');
                console.log('💡 SOLUTION: Need to restart Apache service in XAMPP');
            }
        } else if (pageContent.hasUsers || pageContent.hasAdminContent || pageContent.hasTable) {
            console.log('✅ SUCCESS: Users page loaded properly!');
        } else {
            console.log('⚠️  UNKNOWN: Page loaded but content unclear');
        }
        
        return !pageContent.hasServerError;
        
    } catch (error) {
        console.log(`💥 Test failed: ${error.message}`);
        return false;
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

// Run the test
testUsersPageDetailed().then((success) => {
    console.log('\n🏁 FINAL RESULT:');
    if (success) {
        console.log('✅ Users page is working properly!');
    } else {
        console.log('❌ Users page still has errors - Apache restart needed');
    }
    process.exit(success ? 0 : 1);
}).catch((error) => {
    console.error('💥 Test runner failed:', error);
    process.exit(1);
});