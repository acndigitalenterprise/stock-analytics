const puppeteer = require('puppeteer');

async function testLoginAndUsers() {
    let browser;
    try {
        console.log('Testing login flow...');
        
        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();
        
        // Go to signin page
        console.log('Going to signin page...');
        await page.goto('http://tickerai.local/signin', { waitUntil: 'networkidle0' });
        
        // Fill login form
        console.log('Filling login form...');
        await page.type('#email', 'admin@example.com');
        await page.type('#password', 'password123');
        
        // Submit form and wait for redirect
        console.log('Submitting login form...');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('button[type="submit"]')
        ]);
        
        const afterLoginUrl = page.url();
        console.log('After login URL:', afterLoginUrl);
        
        if (afterLoginUrl.includes('/signin')) {
            console.log('Login failed - still on signin page');
            
            // Check for error messages
            const errors = await page.evaluate(() => {
                const errorElements = document.querySelectorAll('.auth-error-block, .auth-error-message');
                return Array.from(errorElements).map(el => el.textContent.trim());
            });
            
            if (errors.length > 0) {
                console.log('Error messages:', errors);
            }
            
            return;
        }
        
        console.log('Login successful! Now testing users page...');
        
        // Try to access users page
        await page.goto('http://tickerai.local/users', { waitUntil: 'networkidle0' });
        
        const usersPageUrl = page.url();
        console.log('Users page URL:', usersPageUrl);
        
        if (usersPageUrl.includes('/users')) {
            console.log('SUCCESS: Users page accessible!');
            
            // Take screenshot
            await page.screenshot({ path: 'users_page_success.png', fullPage: true });
            console.log('Screenshot saved: users_page_success.png');
            
            // Check page content
            const pageContent = await page.evaluate(() => {
                return {
                    title: document.title,
                    hasTable: document.querySelector('table') !== null,
                    hasForm: document.querySelector('form') !== null,
                    hasUsers: document.querySelector('[data-user-id], .user-row, .user-item') !== null
                };
            });
            
            console.log('Page analysis:', pageContent);
            
        } else {
            console.log('FAILED: Redirected to:', usersPageUrl);
        }
        
    } catch (error) {
        console.error('Error during test:', error);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

testLoginAndUsers().then(() => {
    console.log('Test completed');
    process.exit(0);
}).catch((error) => {
    console.error('Test failed:', error);
    process.exit(1);
});