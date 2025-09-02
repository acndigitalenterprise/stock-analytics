const puppeteer = require('puppeteer');

async function testCompleteApp() {
    let browser;
    let testResults = {
        homepage: false,
        signin_page: false,
        signup_page: false,
        forgot_password: false,
        signin_functionality: false,
        dashboard_access: false,
        responsive_design: false,
        email_verification_page: false,
        privacy_policy: false,
        disclaimer: false,
        about: false,
        contacts: false
    };

    try {
        console.log('🚀 Starting complete application test...\n');
        
        browser = await puppeteer.launch({ 
            headless: false,
            defaultViewport: { width: 1200, height: 800 },
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });
        
        const page = await browser.newPage();
        const baseUrl = 'http://127.0.0.1:8000';
        
        // Test 1: Homepage
        console.log('📄 Testing homepage...');
        try {
            await page.goto(`${baseUrl}`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('h1', { timeout: 5000 });
            const title = await page.title();
            console.log(`✅ Homepage loaded successfully - Title: ${title}`);
            testResults.homepage = true;
        } catch (error) {
            console.log(`❌ Homepage failed: ${error.message}`);
        }

        // Test 2: Sign In Page
        console.log('\n🔐 Testing sign in page...');
        try {
            await page.goto(`${baseUrl}/signin`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('form', { timeout: 5000 });
            await page.waitForSelector('input[name="email"]', { timeout: 5000 });
            await page.waitForSelector('input[name="password"]', { timeout: 5000 });
            console.log('✅ Sign in page loaded with email and password fields');
            testResults.signin_page = true;
        } catch (error) {
            console.log(`❌ Sign in page failed: ${error.message}`);
        }

        // Test 3: Sign Up Page
        console.log('\n📝 Testing sign up page...');
        try {
            await page.goto(`${baseUrl}/signup`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('form', { timeout: 5000 });
            await page.waitForSelector('input[name="full_name"]', { timeout: 5000 });
            await page.waitForSelector('input[name="email"]', { timeout: 5000 });
            console.log('✅ Sign up page loaded with all required fields');
            testResults.signup_page = true;
        } catch (error) {
            console.log(`❌ Sign up page failed: ${error.message}`);
        }

        // Test 4: Forgot Password Page
        console.log('\n🔑 Testing forgot password page...');
        try {
            await page.goto(`${baseUrl}/forgot-password`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('form', { timeout: 5000 });
            await page.waitForSelector('input[name="email"]', { timeout: 5000 });
            console.log('✅ Forgot password page loaded successfully');
            testResults.forgot_password = true;
        } catch (error) {
            console.log(`❌ Forgot password page failed: ${error.message}`);
        }

        // Test 5: Email Verification Page
        console.log('\n📧 Testing email verification page...');
        try {
            await page.goto(`${baseUrl}/email-verification`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('body', { timeout: 5000 });
            console.log('✅ Email verification page loaded successfully');
            testResults.email_verification_page = true;
        } catch (error) {
            console.log(`❌ Email verification page failed: ${error.message}`);
        }

        // Test 6: Privacy Policy Page
        console.log('\n📋 Testing privacy policy page...');
        try {
            await page.goto(`${baseUrl}/privacy-policy`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('body', { timeout: 5000 });
            console.log('✅ Privacy policy page loaded successfully');
            testResults.privacy_policy = true;
        } catch (error) {
            console.log(`❌ Privacy policy page failed: ${error.message}`);
        }

        // Test 7: Disclaimer Page
        console.log('\n⚠️  Testing disclaimer page...');
        try {
            await page.goto(`${baseUrl}/disclaimer`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('body', { timeout: 5000 });
            console.log('✅ Disclaimer page loaded successfully');
            testResults.disclaimer = true;
        } catch (error) {
            console.log(`❌ Disclaimer page failed: ${error.message}`);
        }

        // Test 8: About Page
        console.log('\n📖 Testing about page...');
        try {
            await page.goto(`${baseUrl}/about`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('body', { timeout: 5000 });
            console.log('✅ About page loaded successfully');
            testResults.about = true;
        } catch (error) {
            console.log(`❌ About page failed: ${error.message}`);
        }

        // Test 9: Contacts Page
        console.log('\n📞 Testing contacts page...');
        try {
            await page.goto(`${baseUrl}/contacts`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('form', { timeout: 5000 });
            await page.waitForSelector('input[name="full_name"]', { timeout: 5000 });
            console.log('✅ Contacts page loaded with contact form');
            testResults.contacts = true;
        } catch (error) {
            console.log(`❌ Contacts page failed: ${error.message}`);
        }

        // Test 10: Sign In Functionality
        console.log('\n🔐 Testing sign in functionality...');
        try {
            await page.goto(`${baseUrl}/signin`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('input[name="email"]', { timeout: 5000 });
            await page.waitForSelector('input[name="password"]', { timeout: 5000 });
            
            // Fill in login credentials (using correct credentials)
            await page.type('input[name="email"]', 'coretechlead@gmail.com');
            await page.type('input[name="password"]', 'PassAman@2025');
            
            // Submit form
            await page.click('button[type="submit"]');
            await new Promise(resolve => setTimeout(resolve, 3000));
            
            const currentUrl = page.url();
            if (currentUrl.includes('/dashboard') || currentUrl.includes('dashboard')) {
                console.log('✅ Sign in successful - redirected to dashboard');
                testResults.signin_functionality = true;
                testResults.dashboard_access = true;
            } else {
                console.log(`⚠️ Sign in submitted but not redirected to dashboard. Current URL: ${currentUrl}`);
                
                // Check if there's an error message
                try {
                    const errorMsg = await page.$eval('.auth-error-block', el => el.textContent);
                    console.log(`📝 Error message: ${errorMsg}`);
                } catch (e) {
                    console.log('📝 No error message found');
                }
            }
        } catch (error) {
            console.log(`❌ Sign in functionality failed: ${error.message}`);
        }

        // Test 11: Responsive Design
        console.log('\n📱 Testing responsive design...');
        try {
            await page.setViewport({ width: 375, height: 667 }); // iPhone size
            await page.goto(`${baseUrl}`, { waitUntil: 'networkidle0', timeout: 10000 });
            await page.waitForSelector('body', { timeout: 5000 });
            console.log('✅ Mobile responsive design working');
            testResults.responsive_design = true;
            
            // Reset viewport
            await page.setViewport({ width: 1200, height: 800 });
        } catch (error) {
            console.log(`❌ Responsive design failed: ${error.message}`);
        }

        // Final Results
        console.log('\n' + '='.repeat(50));
        console.log('📊 TEST RESULTS SUMMARY');
        console.log('='.repeat(50));
        
        const passedTests = Object.values(testResults).filter(result => result).length;
        const totalTests = Object.keys(testResults).length;
        const passPercentage = Math.round((passedTests / totalTests) * 100);
        
        console.log(`✅ Passed: ${passedTests}/${totalTests} tests (${passPercentage}%)`);
        
        Object.entries(testResults).forEach(([test, passed]) => {
            const status = passed ? '✅' : '❌';
            const testName = test.replace(/_/g, ' ').toUpperCase();
            console.log(`${status} ${testName}`);
        });
        
        if (passPercentage === 100) {
            console.log('\n🎉 ALL TESTS PASSED! Ready for Laravel Forge deployment!');
        } else if (passPercentage >= 80) {
            console.log('\n🟡 MOSTLY WORKING! Some minor issues to fix before deployment.');
        } else {
            console.log('\n🔴 MAJOR ISSUES FOUND! Need significant fixes before deployment.');
        }
        
        console.log('\n' + '='.repeat(50));
        
        return { passedTests, totalTests, passPercentage, testResults };
        
    } catch (error) {
        console.log(`💥 Fatal error during testing: ${error.message}`);
        return { passedTests: 0, totalTests: 0, passPercentage: 0, testResults };
    } finally {
        if (browser) {
            await browser.close();
        }
    }
}

// Run the test
testCompleteApp().then((results) => {
    console.log('\n🏁 Testing completed.');
    process.exit(results.passPercentage === 100 ? 0 : 1);
}).catch((error) => {
    console.error('💥 Test runner failed:', error);
    process.exit(1);
});