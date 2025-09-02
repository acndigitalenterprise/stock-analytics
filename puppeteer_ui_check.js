const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

async function checkUILayouts() {
    let browser;
    try {
        console.log('Starting Puppeteer UI checks...');
        
        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();
        
        // Create screenshots directory
        const screenshotDir = path.join(__dirname, 'ui_screenshots');
        if (!fs.existsSync(screenshotDir)) {
            fs.mkdirSync(screenshotDir);
        }

        // Login first to access protected pages
        console.log('Logging in...');
        await page.goto('http://localhost:8000/signin');
        await page.waitForSelector('#email');
        
        // Try to login (assuming there's a test user)
        await page.type('#email', 'admin@example.com');
        await page.type('#password', 'password123');
        await page.click('button[type="submit"]');
        
        // Wait and see if we're redirected or get error
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        const currentUrl = page.url();
        console.log('After login attempt, current URL:', currentUrl);
        
        if (currentUrl.includes('/signin')) {
            console.log('Login failed or no test user exists. Checking public pages only...');
            
            // Check signin page
            await checkPage(page, 'http://localhost:8000/signin', 'signin', screenshotDir);
            await checkPage(page, 'http://localhost:8000/signup', 'signup', screenshotDir);
            await checkPage(page, 'http://localhost:8000/forgot-password', 'forgot-password', screenshotDir);
            
        } else {
            console.log('Login successful, checking protected pages...');
            
            // Check all protected pages
            const pages = [
                { url: 'http://localhost:8000/dashboard', name: 'dashboard' },
                { url: 'http://localhost:8000/requests', name: 'requests' },
                { url: 'http://localhost:8000/users', name: 'users' },
                { url: 'http://localhost:8000/settings', name: 'settings' }
            ];

            for (const pageInfo of pages) {
                await checkPage(page, pageInfo.url, pageInfo.name, screenshotDir);
            }
            
            // Try to check detail pages if possible
            try {
                await checkPage(page, 'http://localhost:8000/requests/1', 'request-detail', screenshotDir);
            } catch (e) {
                console.log('Request detail page not accessible:', e.message);
            }
            
            try {
                await checkPage(page, 'http://localhost:8000/users/1', 'user-detail', screenshotDir);
            } catch (e) {
                console.log('User detail page not accessible:', e.message);
            }
        }
        
        console.log('UI checks completed. Screenshots saved in ui_screenshots/');
        
    } catch (error) {
        console.error('Error during UI checks:', error);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

async function checkPage(page, url, name, screenshotDir) {
    try {
        console.log(`Checking page: ${name} (${url})`);
        
        await page.goto(url, { waitUntil: 'networkidle0', timeout: 10000 });
        
        // Desktop view
        await page.setViewport({ width: 1200, height: 800 });
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        const desktopScreenshot = path.join(screenshotDir, `${name}-desktop.png`);
        await page.screenshot({ path: desktopScreenshot, fullPage: true });
        console.log(`Desktop screenshot saved: ${desktopScreenshot}`);
        
        // Mobile view
        await page.setViewport({ width: 375, height: 667 });
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        const mobileScreenshot = path.join(screenshotDir, `${name}-mobile.png`);
        await page.screenshot({ path: mobileScreenshot, fullPage: true });
        console.log(`Mobile screenshot saved: ${mobileScreenshot}`);
        
        // Check for common UI elements
        const hasTable = await page.$('table') !== null;
        const hasForm = await page.$('form') !== null;
        const hasSidebar = await page.$('.sidebar, .admin-sidebar') !== null;
        const hasHeader = await page.$('.header, .admin-header') !== null;
        
        console.log(`Page ${name} analysis:`);
        console.log(`  - Has table: ${hasTable}`);
        console.log(`  - Has form: ${hasForm}`);
        console.log(`  - Has sidebar: ${hasSidebar}`);
        console.log(`  - Has header: ${hasHeader}`);
        
        // Check for responsive elements
        const responsiveElements = await page.evaluate(() => {
            const elements = document.querySelectorAll('.mobile-only, .desktop-only, .mobile-menu, .mobile-sidebar');
            return elements.length;
        });
        
        console.log(`  - Responsive elements found: ${responsiveElements}`);
        
    } catch (error) {
        console.error(`Error checking page ${name}:`, error.message);
    }
}

// Run the checks
checkUILayouts().then(() => {
    console.log('All UI checks completed');
    process.exit(0);
}).catch((error) => {
    console.error('UI check failed:', error);
    process.exit(1);
});