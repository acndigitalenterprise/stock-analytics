const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

async function checkProtectedPages() {
    let browser;
    try {
        console.log('Starting Puppeteer checks for protected pages...');
        
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

        // Manually set session to simulate logged in user
        console.log('Setting up session for admin user...');
        
        // Go to signin page first to establish session
        await page.goto('http://localhost:8000/signin');
        
        // Set session cookie to simulate login
        await page.setCookie({
            name: 'laravel_session',
            value: 'fake_session_value',
            domain: 'localhost'
        });

        // Try to access protected pages directly
        const pages = [
            { url: 'http://localhost:8000/dashboard', name: 'dashboard' },
            { url: 'http://localhost:8000/requests', name: 'requests' },
            { url: 'http://localhost:8000/users', name: 'users' },
            { url: 'http://localhost:8000/settings', name: 'settings' }
        ];

        for (const pageInfo of pages) {
            await checkPage(page, pageInfo.url, pageInfo.name, screenshotDir);
        }
        
        // Try detail pages with sample IDs
        try {
            await checkPage(page, 'http://localhost:8000/requests/1', 'request-detail', screenshotDir);
        } catch (e) {
            console.log('Request detail page not accessible');
        }
        
        try {
            await checkPage(page, 'http://localhost:8000/users/1', 'user-detail', screenshotDir);
        } catch (e) {
            console.log('User detail page not accessible');
        }
        
        console.log('Protected pages check completed.');
        
    } catch (error) {
        console.error('Error during protected pages check:', error);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

async function checkPage(page, url, name, screenshotDir) {
    try {
        console.log(`Checking page: ${name} (${url})`);
        
        const response = await page.goto(url, { waitUntil: 'networkidle0', timeout: 15000 });
        
        // Check if redirected to signin (means not authenticated)
        const currentUrl = page.url();
        if (currentUrl.includes('/signin')) {
            console.log(`Page ${name} redirected to signin - authentication required`);
            return;
        }
        
        // Desktop view
        await page.setViewport({ width: 1200, height: 800 });
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        const desktopScreenshot = path.join(screenshotDir, `${name}-desktop.png`);
        await page.screenshot({ path: desktopScreenshot, fullPage: true });
        console.log(`Desktop screenshot saved: ${desktopScreenshot}`);
        
        // Mobile view for table pages
        await page.setViewport({ width: 375, height: 667 });
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        const mobileScreenshot = path.join(screenshotDir, `${name}-mobile.png`);
        await page.screenshot({ path: mobileScreenshot, fullPage: true });
        console.log(`Mobile screenshot saved: ${mobileScreenshot}`);
        
        // Analyze page elements
        const analysis = await page.evaluate(() => {
            const hasTable = document.querySelector('table') !== null;
            const hasForm = document.querySelector('form') !== null;
            const hasSidebar = document.querySelector('.sidebar, .admin-sidebar, [class*="sidebar"]') !== null;
            const hasHeader = document.querySelector('.header, .admin-header, [class*="header"]') !== null;
            const responsiveElements = document.querySelectorAll('.mobile-only, .desktop-only, .mobile-menu, .mobile-sidebar, [class*="mobile"]').length;
            
            // Check for mobile-responsive table elements
            const hasMobileTable = document.querySelector('.table-responsive, .mobile-table, [class*="table"][class*="mobile"]') !== null;
            const hasOverflowScroll = Array.from(document.querySelectorAll('*')).some(el => 
                getComputedStyle(el).overflowX === 'scroll' || getComputedStyle(el).overflowX === 'auto'
            );
            
            return {
                hasTable,
                hasForm,
                hasSidebar,
                hasHeader,
                responsiveElements,
                hasMobileTable,
                hasOverflowScroll,
                title: document.title,
                url: window.location.href
            };
        });
        
        console.log(`Page ${name} analysis:`);
        console.log(`  - Title: ${analysis.title}`);
        console.log(`  - Has table: ${analysis.hasTable}`);
        console.log(`  - Has form: ${analysis.hasForm}`);
        console.log(`  - Has sidebar: ${analysis.hasSidebar}`);
        console.log(`  - Has header: ${analysis.hasHeader}`);
        console.log(`  - Responsive elements: ${analysis.responsiveElements}`);
        console.log(`  - Mobile table features: ${analysis.hasMobileTable}`);
        console.log(`  - Has horizontal scroll: ${analysis.hasOverflowScroll}`);
        
        // Special check for table pages on mobile
        if (analysis.hasTable && name.includes(['dashboard', 'requests', 'users'])) {
            console.log(`  - TABLE PAGE: Mobile responsiveness analysis for ${name}`);
        }
        
    } catch (error) {
        console.error(`Error checking page ${name}:`, error.message);
    }
}

// Run the checks
checkProtectedPages().then(() => {
    console.log('All protected pages checks completed');
    process.exit(0);
}).catch((error) => {
    console.error('Protected pages check failed:', error);
    process.exit(1);
});