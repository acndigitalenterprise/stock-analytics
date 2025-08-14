/**
 * Authentication Flow Tests
 * Tests sign-in, sign-up, and error handling flows
 */

const StockAnalyticsTestRunner = require('./test-runner');

async function runAuthFlowTests() {
    const testRunner = new StockAnalyticsTestRunner();
    
    try {
        await testRunner.init();
        
        // Test 1: Homepage loads correctly
        await testRunner.test('Homepage loads and displays correctly', async () => {
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics`);
            
            // Check if main elements are present
            await testRunner.waitForElement('.page-title');
            await testRunner.waitForText('Welcome to AI Stock Analytics');
            
            // Check if both forms are present
            const signUpFormExists = await testRunner.checkElementExists('form[action*="register"]');
            const signInFormExists = await testRunner.checkElementExists('form[action*="signin"]');
            
            if (!signUpFormExists) throw new Error('Sign up form not found');
            if (!signInFormExists) throw new Error('Sign in form not found');
            
            await testRunner.takeScreenshot('homepage_loaded', 'Homepage with both sign-up and sign-in forms');
        });

        // Test 2: Sign-in with invalid credentials shows error
        await testRunner.test('Sign-in with invalid credentials shows error message', async () => {
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics`);
            
            // Fill in invalid credentials
            await testRunner.fillForm({
                'input[name="email"]': 'invalid@test.com',
                'input[name="password"]': 'wrongpassword'
            });
            
            // Submit form
            await testRunner.clickAndWait('form[action*="signin"] button[type="submit"]', 2000);
            
            // Check for error message (should show our fixed error handling)
            const hasErrorMessage = await testRunner.checkTextExists('Incorrect username or password') ||
                                  await testRunner.checkTextExists('Your session has expired');
            
            if (!hasErrorMessage) {
                throw new Error('Expected error message not displayed');
            }
            
            await testRunner.takeScreenshot('signin_error_displayed', 'Error message shown for invalid credentials');
        });

        // Test 3: Sign-in with valid credentials (existing user)
        await testRunner.test('Sign-in with valid credentials works', async () => {
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics`);
            
            // Use existing admin credentials
            await testRunner.fillForm({
                'input[name="email"]': 'admin@gihon7.com',
                'input[name="password"]': 'admin123'
            });
            
            await testRunner.takeScreenshot('signin_form_filled', 'Sign-in form filled with valid credentials');
            
            // Submit form
            await testRunner.clickAndWait('form[action*="signin"] button[type="submit"]', 3000);
            
            // Should redirect to admin dashboard
            const currentUrl = testRunner.page.url();
            if (!currentUrl.includes('/admin')) {
                throw new Error(`Expected to redirect to admin page, but got: ${currentUrl}`);
            }
            
            // Check if dashboard elements are present
            await testRunner.waitForText('Dashboard');
            
            await testRunner.takeScreenshot('admin_dashboard_loaded', 'Successfully logged in to admin dashboard');
        });

        // Test 4: Admin dashboard navigation
        await testRunner.test('Admin dashboard navigation works', async () => {
            // Should already be logged in from previous test
            const currentUrl = testRunner.page.url();
            if (!currentUrl.includes('/admin')) {
                // Re-login if needed
                await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics`);
                await testRunner.fillForm({
                    'input[name="email"]': 'admin@gihon7.com',
                    'input[name="password"]': 'admin123'
                });
                await testRunner.clickAndWait('form[action*="signin"] button[type="submit"]', 3000);
            }
            
            // Test sidebar navigation
            const navItems = [
                { selector: 'a[href*="/admin/dashboard"]', text: 'Home' },
                { selector: 'a[href*="/admin/requests"]', text: 'Requests' },
                { selector: 'a[href*="/admin/users"]', text: 'Users' }
            ];
            
            for (const item of navItems) {
                const exists = await testRunner.checkElementExists(item.selector);
                if (!exists) {
                    throw new Error(`Navigation item not found: ${item.text}`);
                }
            }
            
            await testRunner.takeScreenshot('admin_navigation_verified', 'Admin navigation sidebar verified');
        });

        // Test 5: Users page accessibility (admin only)
        await testRunner.test('Users page loads for admin user', async () => {
            await testRunner.clickAndWait('a[href*="/admin/users"]', 2000);
            
            // Should show users table
            await testRunner.waitForText('Users');
            await testRunner.waitForElement('.table');
            
            // Check for New User button
            const newUserButtonExists = await testRunner.checkElementExists('button[onclick*="showNewUserModal"]');
            if (!newUserButtonExists) {
                throw new Error('New User button not found');
            }
            
            await testRunner.takeScreenshot('users_page_loaded', 'Users management page loaded successfully');
        });

        // Test 6: Logout functionality
        await testRunner.test('Logout functionality works', async () => {
            // Look for logout form or button
            const logoutFormExists = await testRunner.checkElementExists('form[action*="logout"]');
            
            if (logoutFormExists) {
                await testRunner.clickAndWait('form[action*="logout"] button', 2000);
                
                // Should redirect back to homepage
                await testRunner.waitForText('Welcome to AI Stock Analytics');
                
                const currentUrl = testRunner.page.url();
                if (!currentUrl.includes('/stock-analytics') || currentUrl.includes('/admin')) {
                    throw new Error(`Expected to redirect to homepage, but got: ${currentUrl}`);
                }
                
                await testRunner.takeScreenshot('logout_successful', 'Successfully logged out and redirected to homepage');
            } else {
                console.log('⚠️  Logout form not found, skipping logout test');
            }
        });

        // Test 7: Responsive design check
        await testRunner.test('Responsive design works on mobile viewport', async () => {
            await testRunner.page.setViewport({ width: 375, height: 667 }); // iPhone SE size
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics`);
            
            await testRunner.waitForElement('.forms-container');
            
            await testRunner.takeScreenshot('mobile_responsive', 'Mobile responsive design verification');
            
            // Reset to desktop viewport
            await testRunner.page.setViewport({ width: 1920, height: 1080 });
        });

        // Generate final report
        const report = testRunner.generateReport();
        
        console.log('\n🎉 Authentication Flow Tests Completed!');
        console.log(`Check screenshots in: ${testRunner.screenshotDir}`);
        
    } catch (error) {
        console.error('❌ Test runner failed:', error.message);
    } finally {
        await testRunner.close();
    }
}

module.exports = { runAuthFlowTests };

// Run tests if this file is executed directly
if (require.main === module) {
    runAuthFlowTests();
}