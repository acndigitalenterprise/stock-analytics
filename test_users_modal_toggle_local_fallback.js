const puppeteer = require('puppeteer');

async function testUsersPageFinal() {
    let browser;
    
    try {
        console.log('🚀 FINAL TEST - Users page with fixed routes...\n');
        
        browser = await puppeteer.launch({ 
            headless: false,
            defaultViewport: { width: 1200, height: 800 },
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });
        
        const page = await browser.newPage();
        const baseUrl = 'http://tickerai.local';
        
        // Login
        console.log('🔐 Logging in...');
        await page.goto(`${baseUrl}/signin`, { waitUntil: 'networkidle0', timeout: 15000 });
        await page.waitForSelector('input[name="email"]');
        await page.type('input[name="email"]', 'coretechlead@gmail.com');
        await page.type('input[name="password"]', 'PassAman@2025');
        await page.click('button[type="submit"]');
        await new Promise(resolve => setTimeout(resolve, 3000));
        
        // Test users page
        console.log('👥 Testing users page with fixed routes...');
        await page.goto(`${baseUrl}/users`, { waitUntil: 'networkidle0', timeout: 15000 });
        
        const pageContent = await page.evaluate(() => {
            return {
                url: window.location.href,
                hasServerError: document.body.innerText.includes('Internal Server Error'),
                hasRouteError: document.body.innerText.includes('RouteNotFoundException') || document.body.innerText.includes('Route [') || document.body.innerText.includes('not defined'),
                hasUsersHeading: document.body.innerText.includes('Users'),
                hasNewUserButton: document.body.innerText.includes('New User'),
                hasTable: !!document.querySelector('table'),
                bodyStart: document.body.innerText.substring(0, 300)
            };
        });
        
        console.log('📊 FINAL TEST RESULTS:');
        console.log('='.repeat(50));
        console.log(`URL: ${pageContent.url}`);
        console.log(`Has Server Error: ${pageContent.hasServerError}`);
        console.log(`Has Route Error: ${pageContent.hasRouteError}`);
        console.log(`Has Users Heading: ${pageContent.hasUsersHeading}`);
        console.log(`Has New User Button: ${pageContent.hasNewUserButton}`);
        console.log(`Has Table: ${pageContent.hasTable}`);
        console.log('\n📝 PAGE CONTENT:');
        console.log('-'.repeat(50));
        console.log(pageContent.bodyStart);
        console.log('='.repeat(50));
        
        const success = !pageContent.hasServerError && !pageContent.hasRouteError && pageContent.hasUsersHeading;
        
        if (success) {
            console.log('✅ SUCCESS: Users page working properly!');
            console.log('🎉 ALL ROUTES FIXED!');
        } else {
            console.log('❌ STILL FAILING: Need more fixes');
        }
        
        return success;
        
    } catch (error) {
        console.log(`💥 Test failed: ${error.message}`);
        return false;
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

testUsersPageFinal().then((success) => {
    console.log('\n🏁 FINAL STATUS:');
    if (success) {
        console.log('✅ Users page is NOW WORKING!');
    } else {
        console.log('❌ Still need more fixes');
    }
    process.exit(success ? 0 : 1);
});
