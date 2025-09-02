const puppeteer = require('puppeteer');

async function testUsersPageWithoutMiddleware() {
    let browser;
    
    try {
        console.log('🚀 Testing users page WITHOUT middleware...\n');
        
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
        
        console.log('✅ Login completed');
        
        // Now test users page WITHOUT middleware
        console.log('👥 Testing users page (middleware removed)...');
        await page.goto(`${baseUrl}/users`, { waitUntil: 'networkidle0', timeout: 15000 });
        
        // Get page content
        const pageContent = await page.evaluate(() => {
            return {
                title: document.title,
                url: window.location.href,
                hasServerError: document.body.innerText.includes('Internal Server Error'),
                hasMiddlewareError: document.body.innerText.includes('admin.access'),
                hasUsersHeading: document.body.innerText.includes('Users') || document.body.innerText.includes('User Management'),
                hasTable: !!document.querySelector('table'),
                bodyStart: document.body.innerText.substring(0, 200)
            };
        });
        
        console.log('📊 USERS PAGE TEST RESULTS:');
        console.log('='.repeat(50));
        console.log(`URL: ${pageContent.url}`);
        console.log(`Title: ${pageContent.title}`);
        console.log(`Has Server Error: ${pageContent.hasServerError}`);
        console.log(`Has Middleware Error: ${pageContent.hasMiddlewareError}`);
        console.log(`Has Users Content: ${pageContent.hasUsersHeading}`);
        console.log(`Has Table: ${pageContent.hasTable}`);
        console.log('\n📝 PAGE START:');
        console.log('-'.repeat(50));
        console.log(pageContent.bodyStart);
        console.log('='.repeat(50));
        
        if (!pageContent.hasServerError && !pageContent.hasMiddlewareError) {
            console.log('✅ SUCCESS: Users page working without middleware!');
            console.log('🔧 SOLUTION: Apache middleware cache issue confirmed');
            return true;
        } else {
            console.log('❌ STILL FAILING: Even without middleware');
            return false;
        }
        
    } catch (error) {
        console.log(`💥 Test failed: ${error.message}`);
        return false;
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

testUsersPageWithoutMiddleware().then((success) => {
    console.log('\n🏁 FINAL DIAGNOSIS:');
    if (success) {
        console.log('✅ CONFIRMED: Issue is Apache middleware cache');
        console.log('💡 SOLUTION: Restart Apache in XAMPP Control Panel');
    } else {
        console.log('❌ DEEPER ISSUE: Not just middleware cache');
    }
    process.exit(success ? 0 : 1);
}).catch((error) => {
    console.error('💥 Test runner failed:', error);
    process.exit(1);
});
