const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

async function captureUIWithoutAuth() {
    let browser;
    try {
        console.log('Starting UI capture without authentication...');
        
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

        // Test signin page first
        console.log('Checking signin page...');
        await page.goto('http://localhost:8000/signin', { waitUntil: 'networkidle0', timeout: 15000 });
        
        await page.setViewport({ width: 1200, height: 800 });
        await page.screenshot({ path: path.join(screenshotDir, 'signin-desktop-final.png'), fullPage: true });
        
        await page.setViewport({ width: 375, height: 667 });
        await page.screenshot({ path: path.join(screenshotDir, 'signin-mobile-final.png'), fullPage: true });
        
        console.log('Signin page captured successfully');
        
        // Test other public pages
        const publicPages = [
            { url: 'http://localhost:8000/signup', name: 'signup-final' },
            { url: 'http://localhost:8000/forgot-password', name: 'forgot-password-final' }
        ];

        for (const pageInfo of publicPages) {
            try {
                console.log(`Checking ${pageInfo.name}...`);
                await page.goto(pageInfo.url, { waitUntil: 'networkidle0', timeout: 15000 });
                
                // Desktop
                await page.setViewport({ width: 1200, height: 800 });
                await page.screenshot({ path: path.join(screenshotDir, `${pageInfo.name}-desktop.png`), fullPage: true });
                
                // Mobile
                await page.setViewport({ width: 375, height: 667 });
                await page.screenshot({ path: path.join(screenshotDir, `${pageInfo.name}-mobile.png`), fullPage: true });
                
                console.log(`${pageInfo.name} captured successfully`);
            } catch (error) {
                console.error(`Error capturing ${pageInfo.name}:`, error.message);
            }
        }
        
        console.log('UI capture completed. Check ui_screenshots/ for results.');
        
    } catch (error) {
        console.error('Error during UI capture:', error);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

// Run the capture
captureUIWithoutAuth().then(() => {
    console.log('All UI captures completed');
    process.exit(0);
}).catch((error) => {
    console.error('UI capture failed:', error);
    process.exit(1);
});