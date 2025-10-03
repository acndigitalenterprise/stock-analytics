const puppeteer = require('puppeteer');

(async () => {
  console.log('🚨 EMERGENCY CSS INJECTION - DIRECT DOM MANIPULATION');

  const browser = await puppeteer.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  const page = await browser.newPage();

  try {
    // Login
    await page.goto('https://tickerai.app/signin', { waitUntil: 'networkidle2' });
    await page.type('input[name="email"]', 'coretechlead@gmail.com');
    await page.type('input[name="password"]', 'PassAman@2025');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle2' }),
      page.click('button[type="submit"]')
    ]);

    // Go to signals
    await page.goto('https://tickerai.app/signals', { waitUntil: 'networkidle2' });

    // INJECT CSS DIRECTLY INTO DOM
    await page.addStyleTag({
      content: `
        /* EMERGENCY CSS OVERRIDE */
        html, html body, html body.admin-layout {
          background: #f8fafc !important;
          background-image: none !important;
        }

        html body.admin-layout::before,
        html body.admin-layout::after {
          display: none !important;
          content: none !important;
          opacity: 0 !important;
          visibility: hidden !important;
        }

        .admin-content-container {
          background: transparent !important;
          backdrop-filter: none !important;
          border-radius: 0 !important;
        }

        .signals-header {
          background: white !important;
          border: 1px solid #e2e8f0 !important;
          border-radius: 12px !important;
          padding: 2rem !important;
          box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
          margin-bottom: 2rem !important;
        }

        .signals-stats-container {
          display: grid !important;
          grid-template-columns: repeat(3, 1fr) !important;
          gap: 1rem !important;
        }

        .signals-stat-card {
          background: white !important;
          border: 2px solid #e2e8f0 !important;
          border-radius: 12px !important;
          padding: 1.5rem !important;
          text-align: center !important;
        }

        .signals-stat-value {
          font-size: 2rem !important;
          font-weight: bold !important;
          color: #667eea !important;
        }

        .signals-filter-bar {
          background: white !important;
          border: 1px solid #e2e8f0 !important;
          border-radius: 12px !important;
          padding: 1.5rem !important;
          margin-bottom: 2rem !important;
        }

        .signals-no-data {
          background: white !important;
          border: 1px solid #e2e8f0 !important;
          border-radius: 12px !important;
          padding: 3rem !important;
          text-align: center !important;
        }
      `
    });

    console.log('✅ Emergency CSS injected directly into DOM');

    // Take screenshot
    await page.screenshot({ path: 'emergency-fixed-screenshot.png', fullPage: true });
    console.log('📸 Emergency fix screenshot saved');

    // Verify the fix
    const bgCheck = await page.evaluate(() => {
      const body = document.body;
      const computed = window.getComputedStyle(body);
      return {
        backgroundColor: computed.backgroundColor,
        hasGradient: computed.background.includes('gradient')
      };
    });

    console.log('🎨 Emergency fix results:');
    console.log('- Background color:', bgCheck.backgroundColor);
    console.log('- Has gradient:', bgCheck.hasGradient);

  } catch (error) {
    console.error('❌ Emergency fix failed:', error.message);
  } finally {
    await browser.close();
  }
})();