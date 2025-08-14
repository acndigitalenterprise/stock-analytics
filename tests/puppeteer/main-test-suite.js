/**
 * Main Test Suite Runner
 * Comprehensive testing suite for Stock Analytics application
 */

const { runAuthFlowTests } = require('./auth-flow-tests');
const { runAdminFeaturesTests } = require('./admin-features-tests');

async function runAllTests() {
    console.log('🚀 Starting Stock Analytics Comprehensive Test Suite');
    console.log('====================================================');
    
    const startTime = Date.now();
    
    try {
        // Run Authentication Flow Tests
        console.log('\n📝 PHASE 1: Authentication Flow Tests');
        console.log('=====================================');
        await runAuthFlowTests();
        
        console.log('\n⏳ Waiting 3 seconds before next test phase...');
        await new Promise(resolve => setTimeout(resolve, 3000));
        
        // Run Admin Features Tests
        console.log('\n🔧 PHASE 2: Admin Features Tests');
        console.log('=================================');
        await runAdminFeaturesTests();
        
        const totalTime = Date.now() - startTime;
        console.log('\n🎉 ALL TESTS COMPLETED SUCCESSFULLY!');
        console.log('=====================================');
        console.log(`Total execution time: ${(totalTime / 1000).toFixed(2)} seconds`);
        console.log('📁 Check screenshots and reports in: tests/puppeteer/screenshots/');
        
    } catch (error) {
        console.error('\n❌ Test suite failed:', error.message);
        process.exit(1);
    }
}

// Additional utility functions for quick testing

async function runQuickSmokeTest() {
    console.log('🔥 Running Quick Smoke Test');
    
    const StockAnalyticsTestRunner = require('./test-runner');
    const testRunner = new StockAnalyticsTestRunner();
    
    try {
        await testRunner.init();
        
        await testRunner.test('Quick Smoke Test - Homepage loads', async () => {
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics`);
            await testRunner.waitForText('Welcome to AI Stock Analytics');
            await testRunner.takeScreenshot('smoke_test_homepage', 'Quick smoke test - homepage loaded');
        });
        
        await testRunner.test('Quick Smoke Test - Admin login', async () => {
            await testRunner.fillForm({
                'input[name="email"]': 'admin@gihon7.com',
                'input[name="password"]': 'admin123'
            });
            
            await testRunner.clickAndWait('form[action*="signin"] button[type="submit"]', 3000);
            
            const currentUrl = testRunner.page.url();
            if (!currentUrl.includes('/admin')) {
                throw new Error(`Login failed, current URL: ${currentUrl}`);
            }
            
            await testRunner.takeScreenshot('smoke_test_admin', 'Quick smoke test - admin logged in');
        });
        
        const report = testRunner.generateReport();
        console.log('✅ Quick smoke test completed!');
        
    } catch (error) {
        console.error('❌ Quick smoke test failed:', error.message);
    } finally {
        await testRunner.close();
    }
}

async function runVisualRegressionTest() {
    console.log('👀 Running Visual Regression Test');
    
    const StockAnalyticsTestRunner = require('./test-runner');
    const testRunner = new StockAnalyticsTestRunner();
    
    try {
        await testRunner.init();
        
        const pages = [
            { url: '/stock-analytics', name: 'homepage' },
            { url: '/stock-analytics/admin/dashboard', name: 'admin_dashboard', needsAuth: true },
            { url: '/stock-analytics/admin/users', name: 'admin_users', needsAuth: true },
            { url: '/stock-analytics/admin/requests', name: 'admin_requests', needsAuth: true }
        ];
        
        // Login once if needed
        let isLoggedIn = false;
        
        for (const page of pages) {
            await testRunner.test(`Visual regression - ${page.name}`, async () => {
                if (page.needsAuth && !isLoggedIn) {
                    await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics`);
                    await testRunner.fillForm({
                        'input[name="email"]': 'admin@gihon7.com',
                        'input[name="password"]': 'admin123'
                    });
                    await testRunner.clickAndWait('form[action*="signin"] button[type="submit"]', 3000);
                    isLoggedIn = true;
                }
                
                await testRunner.page.goto(`${testRunner.baseUrl}${page.url}`);
                await new Promise(resolve => setTimeout(resolve, 2000)); // Let page fully load
                
                await testRunner.takeScreenshot(`visual_regression_${page.name}`, `Visual regression test - ${page.name}`);
            });
        }
        
        const report = testRunner.generateReport();
        console.log('✅ Visual regression test completed!');
        
    } catch (error) {
        console.error('❌ Visual regression test failed:', error.message);
    } finally {
        await testRunner.close();
    }
}

// Export functions for individual use
module.exports = {
    runAllTests,
    runQuickSmokeTest,
    runVisualRegressionTest,
    runAuthFlowTests,
    runAdminFeaturesTests
};

// Run based on command line arguments
if (require.main === module) {
    const args = process.argv.slice(2);
    
    if (args.includes('--smoke')) {
        runQuickSmokeTest();
    } else if (args.includes('--visual')) {
        runVisualRegressionTest();
    } else if (args.includes('--auth')) {
        runAuthFlowTests();
    } else if (args.includes('--admin')) {
        runAdminFeaturesTests();
    } else {
        runAllTests();
    }
}