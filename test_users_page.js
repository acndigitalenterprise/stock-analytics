const puppeteer = require('puppeteer');

async function testUsersPage() {
    let browser;
    
    try {
        console.log('🚀 Testing users page with login...\n');
        
        browser = await puppeteer.launch({ 
            headless: false,
            defaultViewport: { width: 1200, height: 800 },
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });
        
        const page = await browser.newPage();
        const baseUrl = 'http://tickerai.local';
        
        // Step 1: Go to sign in page
        console.log('🔐 Going to sign in page...');
        await page.goto(`${baseUrl}/signin`, { waitUntil: 'networkidle0', timeout: 15000 });
        
        // Step 2: Fill login form
        console.log('📝 Filling login credentials...');
        await page.waitForSelector('input[name="email"]', { timeout: 10000 });
        await page.waitForSelector('input[name="password"]', { timeout: 10000 });
        
        await page.type('input[name="email"]', 'coretechlead@gmail.com');
        await page.type('input[name="password"]', 'PassAman@2025');
        
        // Step 3: Submit login
        console.log('✈️  Submitting login form...');
        await page.click('button[type="submit"]');
        await new Promise(resolve => setTimeout(resolve, 3000));
        
        const currentUrl = page.url();
        console.log(`📍 After login, current URL: ${currentUrl}`);
        
        if (currentUrl.includes('/dashboard')) {
            console.log('✅ Login successful - redirected to dashboard');
        } else {
            console.log('❌ Login may have failed - not on dashboard');
            
            // Check for error messages
            try {
                const errorMsg = await page.$eval('.auth-error-block', el => el.textContent);
                console.log(`📝 Error message: ${errorMsg}`);
            } catch (e) {
                console.log('📝 No error message found on page');
            }
        }
        
        // Step 4: Now test users page
        console.log('\n👥 Testing users page access...');
        await page.goto(`${baseUrl}/users`, { waitUntil: 'networkidle0', timeout: 15000 });
        
        const usersUrl = page.url();
        console.log(`📍 Users page URL: ${usersUrl}`);
        
        // Check if we got to users page or were redirected
        if (usersUrl.includes('/users')) {
            console.log('✅ Successfully accessed users page');
            
            // Check if page loaded properly
            try {
                await page.waitForSelector('body', { timeout: 5000 });
                const title = await page.title();
                console.log(`📖 Page title: ${title}`);
                
                // Look for users table or content
                const hasUsersContent = await page.$('table') || await page.$('.users') || await page.$('.admin-content');
                if (hasUsersContent) {
                    console.log('✅ Users page content loaded successfully');
                } else {
                    console.log('⚠️ Users page loaded but no content found');
                }
            } catch (error) {
                console.log(`❌ Error loading users page content: ${error.message}`);
            }
            
        } else if (usersUrl.includes('/signin')) {
            console.log('❌ Redirected to signin - middleware not recognizing session');
        } else if (usersUrl.includes('/dashboard')) {
            console.log('❌ Redirected to dashboard - user may not have admin access');
        } else {
            console.log(`❌ Unexpected redirect to: ${usersUrl}`);
        }
        
        // Check for any error messages on current page
        try {
            const pageText = await page.evaluate(() => document.body.innerText);
            if (pageText.includes('Internal Server Error')) {
                console.log('❌ Internal Server Error detected');
            } else if (pageText.includes('admin.access') && pageText.includes('does not exist')) {
                console.log('❌ Middleware admin.access still not found');
            } else if (pageText.includes('admin access')) {
                console.log('⚠️ User does not have admin access');
            }
        } catch (e) {
            // Ignore
        }
        
        return true;
        
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
testUsersPage().then((success) => {
    console.log('\n🏁 Users page test completed.');
    if (success) {
        console.log('✅ Test ran successfully');
    } else {
        console.log('❌ Test encountered errors');
    }
    process.exit(0);
}).catch((error) => {
    console.error('💥 Test runner failed:', error);
    process.exit(1);
});