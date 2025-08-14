/**
 * Admin Features Tests
 * Tests admin dashboard, user management, and request management
 */

const StockAnalyticsTestRunner = require('./test-runner');

async function runAdminFeaturesTests() {
    const testRunner = new StockAnalyticsTestRunner();
    
    try {
        await testRunner.init();
        
        // Login as admin first
        await testRunner.test('Admin login for feature testing', async () => {
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics`);
            
            await testRunner.fillForm({
                'input[name="email"]': 'admin@gihon7.com',
                'input[name="password"]': 'admin123'
            });
            
            await testRunner.clickAndWait('form[action*="signin"] button[type="submit"]', 3000);
            
            const currentUrl = testRunner.page.url();
            if (!currentUrl.includes('/admin')) {
                throw new Error(`Login failed, current URL: ${currentUrl}`);
            }
            
            await testRunner.takeScreenshot('admin_logged_in', 'Admin successfully logged in');
        });

        // Test 1: Dashboard metrics display
        await testRunner.test('Dashboard displays metrics correctly', async () => {
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics/admin/dashboard`);
            
            // Wait for dashboard to load
            await testRunner.waitForText('Dashboard');
            
            // Check for metric boxes (5 in first row, 3 in second row)
            const metricBoxes = await testRunner.page.$$('div[style*="grid-template-columns"]');
            if (metricBoxes.length < 2) {
                throw new Error('Expected at least 2 metric rows on dashboard');
            }
            
            // Check for specific metrics
            const expectedMetrics = [
                'Total Requests',
                'Total Stocks', 
                'Total Wins',
                'Total Loss',
                'Total Timeout',
                'Total Users',
                'Total Active Users',
                'Total In-Active Users'
            ];
            
            for (const metric of expectedMetrics) {
                const exists = await testRunner.checkTextExists(metric);
                if (!exists) {
                    throw new Error(`Metric not found: ${metric}`);
                }
            }
            
            await testRunner.takeScreenshot('dashboard_metrics', 'Dashboard with all metrics displayed');
        });

        // Test 2: User management functionality
        await testRunner.test('User management page works correctly', async () => {
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics/admin/users`);
            
            await testRunner.waitForText('Users');
            await testRunner.waitForElement('.table');
            
            // Check table headers
            const expectedHeaders = ['Date', 'Full Name', 'Email Address', 'Mobile Number', 'Total Request', 'Action'];
            for (const header of expectedHeaders) {
                const exists = await testRunner.checkTextExists(header);
                if (!exists) {
                    console.log(`⚠️  Header might be different: ${header}`);
                }
            }
            
            // Check New User button
            const newUserButtonExists = await testRunner.checkElementExists('button[onclick*="showNewUserModal"]');
            if (!newUserButtonExists) {
                throw new Error('New User button not found');
            }
            
            await testRunner.takeScreenshot('users_management', 'Users management page');
        });

        // Test 3: New User Modal
        await testRunner.test('New User modal opens and displays correctly', async () => {
            // Click New User button
            await testRunner.clickAndWait('button[onclick*="showNewUserModal"]', 1000);
            
            // Wait for modal to appear
            await testRunner.waitForElement('#new-user-popup');
            
            // Check modal visibility
            const modalVisible = await testRunner.page.evaluate(() => {
                const modal = document.getElementById('new-user-popup');
                return modal && window.getComputedStyle(modal).display !== 'none';
            });
            
            if (!modalVisible) {
                throw new Error('New User modal not visible');
            }
            
            // Check form fields
            const expectedFields = ['new_full_name', 'new_email', 'new_mobile_number', 'new_password'];
            for (const fieldId of expectedFields) {
                const exists = await testRunner.checkElementExists(`#${fieldId}`);
                if (!exists) {
                    throw new Error(`Form field not found: ${fieldId}`);
                }
            }
            
            await testRunner.takeScreenshot('new_user_modal', 'New User modal opened with form fields');
            
            // Close modal
            await testRunner.clickAndWait('button[onclick*="closeUserPopup"]', 500);
        });

        // Test 4: Requests page functionality
        await testRunner.test('Requests page displays correctly', async () => {
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics/admin/requests`);
            
            await testRunner.waitForElement('.admin-content-container');
            
            // Check for search functionality
            const searchExists = await testRunner.checkElementExists('input[name="search"]');
            if (!searchExists) {
                throw new Error('Search input not found');
            }
            
            // Check for filter options
            const filterExists = await testRunner.checkElementExists('select[name="timeframe"]');
            if (filterExists) {
                console.log('✅ Timeframe filter found');
            }
            
            await testRunner.takeScreenshot('requests_page', 'Requests management page');
        });

        // Test 5: Profile settings page
        await testRunner.test('Profile settings page accessible', async () => {
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics/setting/profile`);
            
            // Should show profile form
            await testRunner.waitForText('Profile');
            
            const profileFormExists = await testRunner.checkElementExists('form[action*="profile"]');
            if (!profileFormExists) {
                throw new Error('Profile form not found');
            }
            
            await testRunner.takeScreenshot('profile_settings', 'Profile settings page');
        });

        // Test 6: Mobile responsiveness for admin panel
        await testRunner.test('Admin panel is mobile responsive', async () => {
            await testRunner.page.setViewport({ width: 768, height: 1024 }); // Tablet size
            await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics/admin/dashboard`);
            
            await testRunner.waitForText('Dashboard');
            
            await testRunner.takeScreenshot('admin_tablet_responsive', 'Admin dashboard on tablet view');
            
            // Test mobile size
            await testRunner.page.setViewport({ width: 375, height: 667 }); // Mobile size
            await testRunner.page.reload();
            await testRunner.waitForText('Dashboard');
            
            await testRunner.takeScreenshot('admin_mobile_responsive', 'Admin dashboard on mobile view');
            
            // Reset to desktop
            await testRunner.page.setViewport({ width: 1920, height: 1080 });
        });

        // Test 7: Navigation consistency
        await testRunner.test('Navigation works across all admin pages', async () => {
            const adminPages = [
                { url: '/admin/dashboard', expectedText: 'Dashboard' },
                { url: '/admin/requests', expectedText: 'admin-content-container' },
                { url: '/admin/users', expectedText: 'Users' }
            ];
            
            for (const page of adminPages) {
                await testRunner.page.goto(`${testRunner.baseUrl}/stock-analytics${page.url}`);
                
                if (page.expectedText === 'admin-content-container') {
                    await testRunner.waitForElement('.admin-content-container');
                } else {
                    await testRunner.waitForText(page.expectedText);
                }
                
                // Check sidebar navigation is present
                const sidebarExists = await testRunner.checkElementExists('.sidebar');
                if (!sidebarExists) {
                    throw new Error(`Sidebar not found on ${page.url}`);
                }
                
                console.log(`✅ Navigation verified for ${page.url}`);
            }
            
            await testRunner.takeScreenshot('navigation_verified', 'Admin navigation verified across all pages');
        });

        // Generate final report
        const report = testRunner.generateReport();
        
        console.log('\n🎉 Admin Features Tests Completed!');
        console.log(`Check screenshots in: ${testRunner.screenshotDir}`);
        
    } catch (error) {
        console.error('❌ Test runner failed:', error.message);
    } finally {
        await testRunner.close();
    }
}

module.exports = { runAdminFeaturesTests };

// Run tests if this file is executed directly
if (require.main === module) {
    runAdminFeaturesTests();
}