const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

async function checkUIStatus() {
    let browser;
    try {
        console.log('🚀 Starting UI Check for tickerai.app...');

        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();
        await page.setViewport({ width: 1920, height: 1080 });

        // Track console logs and errors
        const consoleMessages = [];
        const errors = [];

        page.on('console', msg => {
            consoleMessages.push(`${msg.type()}: ${msg.text()}`);
        });

        page.on('pageerror', error => {
            errors.push(`Page Error: ${error.message}`);
        });

        page.on('requestfailed', request => {
            errors.push(`Failed Request: ${request.url()} - ${request.failure().errorText}`);
        });

        // Test main pages
        const pages = [
            { name: 'Home Page', url: 'https://tickerai.app' },
            { name: 'Sign In', url: 'https://tickerai.app/signin' },
            { name: 'Sign Up', url: 'https://tickerai.app/signup' }
        ];

        const results = [];

        for (const testPage of pages) {
            console.log(`\n📊 Testing: ${testPage.name}`);

            try {
                const response = await page.goto(testPage.url, {
                    waitUntil: 'networkidle2',
                    timeout: 30000
                });

                // Check if page loaded successfully
                const statusCode = response.status();
                console.log(`   Status: ${statusCode}`);

                // Check if CSS is loading
                const cssRequests = [];
                page.on('response', response => {
                    if (response.url().includes('.css')) {
                        cssRequests.push({
                            url: response.url(),
                            status: response.status()
                        });
                    }
                });

                // Wait a bit for CSS to load
                await page.waitForTimeout(2000);

                // Check key CSS files
                const cssTests = [
                    'https://tickerai.app/Admin/admin.css',
                    'https://tickerai.app/Home/home.css',
                    'https://tickerai.app/Home/signin.css',
                    'https://tickerai.app/Home/signup.css'
                ];

                const cssResults = [];
                for (const cssUrl of cssTests) {
                    try {
                        const cssResponse = await page.goto(cssUrl);
                        cssResults.push({
                            url: cssUrl,
                            status: cssResponse.status(),
                            success: cssResponse.status() === 200
                        });
                    } catch (e) {
                        cssResults.push({
                            url: cssUrl,
                            status: 'ERROR',
                            success: false,
                            error: e.message
                        });
                    }
                }

                // Go back to the main page
                await page.goto(testPage.url, { waitUntil: 'networkidle2' });

                // Check if page has proper styling (look for styled elements)
                const hasStyles = await page.evaluate(() => {
                    const body = document.body;
                    const computedStyle = window.getComputedStyle(body);
                    return {
                        hasBackground: computedStyle.backgroundColor !== 'rgba(0, 0, 0, 0)' && computedStyle.backgroundColor !== 'transparent',
                        hasFont: computedStyle.fontFamily !== 'Times' && computedStyle.fontFamily !== 'serif',
                        bodyHeight: body.offsetHeight,
                        hasElements: document.querySelectorAll('*').length > 10
                    };
                });

                // Take screenshot
                const screenshotPath = `screenshot-${testPage.name.replace(/\s+/g, '-').toLowerCase()}.png`;
                await page.screenshot({
                    path: screenshotPath,
                    fullPage: true
                });

                results.push({
                    name: testPage.name,
                    url: testPage.url,
                    statusCode,
                    cssResults,
                    hasStyles,
                    screenshot: screenshotPath,
                    errors: errors.filter(err => errors.indexOf(err) >= 0),
                    success: statusCode === 200 && hasStyles.hasElements
                });

                console.log(`   ✅ Completed: ${testPage.name}`);

            } catch (error) {
                console.log(`   ❌ Error: ${error.message}`);
                results.push({
                    name: testPage.name,
                    url: testPage.url,
                    error: error.message,
                    success: false
                });
            }
        }

        // Generate report
        console.log('\n📋 UI CHECK REPORT');
        console.log('===================');

        let allSuccess = true;
        for (const result of results) {
            console.log(`\n${result.name}:`);
            console.log(`  URL: ${result.url}`);
            console.log(`  Status: ${result.success ? '✅ SUCCESS' : '❌ FAILED'}`);

            if (result.statusCode) {
                console.log(`  HTTP Status: ${result.statusCode}`);
            }

            if (result.cssResults) {
                console.log(`  CSS Files:`);
                result.cssResults.forEach(css => {
                    console.log(`    ${css.success ? '✅' : '❌'} ${css.url} (${css.status})`);
                });
            }

            if (result.hasStyles) {
                console.log(`  Styling: ${result.hasStyles.hasBackground ? '✅' : '❌'} Background, ${result.hasStyles.hasFont ? '✅' : '❌'} Font, ${result.hasStyles.hasElements ? '✅' : '❌'} Elements`);
            }

            if (result.screenshot) {
                console.log(`  Screenshot: ${result.screenshot}`);
            }

            if (result.error) {
                console.log(`  Error: ${result.error}`);
                allSuccess = false;
            } else if (!result.success) {
                allSuccess = false;
            }
        }

        console.log(`\n🎯 OVERALL STATUS: ${allSuccess ? '✅ UI IS WORKING NORMALLY' : '❌ UI HAS ISSUES'}`);

        return { success: allSuccess, results };

    } catch (error) {
        console.error('❌ Fatal Error:', error.message);
        return { success: false, error: error.message };
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

// Run the check
checkUIStatus().then(result => {
    process.exit(result.success ? 0 : 1);
}).catch(error => {
    console.error('Fatal error:', error);
    process.exit(1);
});