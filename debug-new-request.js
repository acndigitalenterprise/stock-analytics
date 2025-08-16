const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({ headless: false });
    const page = await browser.newPage();

    // Listen to console logs
    page.on('console', msg => console.log('BROWSER:', msg.text()));
    page.on('response', response => {
        if (response.url().includes('/admin/request') && response.request().method() === 'POST') {
            console.log('POST Response:', response.status(), response.statusText());
            response.text().then(text => console.log('Response body:', text));
        }
    });

    try {
        // Go to login page
        await page.goto('http://localhost/stock-analytics');
        await page.waitForSelector('input[name="email"]', { timeout: 5000 });

        // Login
        await page.type('input[name="email"]', 'dewiyiwan@gmail.com');
        await page.type('input[name="password"]', 'vhV4xj2eYz');
        await page.click('button[type="submit"]');
        
        // Wait for redirect to admin
        await page.waitForNavigation();
        console.log('Current URL after login:', page.url());

        // Go to requests page
        await page.goto('http://localhost/stock-analytics/admin/requests');
        await page.waitForSelector('.btn', { timeout: 5000 });

        // Click New Request button
        await page.click('button:contains("New Request")');
        await page.waitForSelector('#new_stock_code', { timeout: 5000 });

        // Fill form
        await page.type('#new_stock_code', 'BBCA');
        await page.select('#new_timeframe', '1h');

        // Submit form
        console.log('Submitting form...');
        await page.click('#submitBtn');
        
        // Wait for response
        await page.waitForTimeout(3000);
        
    } catch (error) {
        console.error('Error:', error);
    }

    await browser.close();
})();