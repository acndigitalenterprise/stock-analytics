const puppeteer = require('puppeteer');

(async () => {
  console.log('🔍 DETAILED SIGNALS PAGE ANALYSIS');

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

    console.log('📄 Page loaded, analyzing elements...\n');

    // Detailed element analysis
    const analysis = await page.evaluate(() => {
      const results = {
        pageTitle: document.title,
        url: window.location.href,
        elements: {}
      };

      // Check header
      const header = document.querySelector('.signals-header');
      if (header) {
        const headerStyle = window.getComputedStyle(header);
        results.elements.header = {
          exists: true,
          background: headerStyle.background.substring(0, 100),
          borderRadius: headerStyle.borderRadius,
          padding: headerStyle.padding,
          backdropFilter: headerStyle.backdropFilter
        };
      } else {
        results.elements.header = { exists: false };
      }

      // Check stats cards
      const statsContainer = document.querySelector('.signals-stats-container');
      const statCards = document.querySelectorAll('.signals-stat-card');
      if (statsContainer && statCards.length > 0) {
        const containerStyle = window.getComputedStyle(statsContainer);
        const cardStyle = window.getComputedStyle(statCards[0]);
        results.elements.statsCards = {
          exists: true,
          count: statCards.length,
          containerDisplay: containerStyle.display,
          gridColumns: containerStyle.gridTemplateColumns,
          cardBackground: cardStyle.background.substring(0, 100),
          cardBorder: cardStyle.border,
          cardPadding: cardStyle.padding
        };
      } else {
        results.elements.statsCards = { exists: false };
      }

      // Check filter bar
      const filterBar = document.querySelector('.signals-filter-bar');
      if (filterBar) {
        const filterStyle = window.getComputedStyle(filterBar);
        results.elements.filterBar = {
          exists: true,
          display: filterStyle.display,
          background: filterStyle.background.substring(0, 100),
          padding: filterStyle.padding,
          gap: filterStyle.gap
        };
      } else {
        results.elements.filterBar = { exists: false };
      }

      // Check refresh button
      const refreshBtn = document.querySelector('.signals-refresh-btn');
      if (refreshBtn) {
        const btnStyle = window.getComputedStyle(refreshBtn);
        results.elements.refreshButton = {
          exists: true,
          background: btnStyle.backgroundColor,
          padding: btnStyle.padding,
          borderRadius: btnStyle.borderRadius,
          display: btnStyle.display
        };
      } else {
        results.elements.refreshButton = { exists: false };
      }

      // Check no signals section
      const noSignals = document.querySelector('.signals-no-data');
      if (noSignals) {
        const noSignalsStyle = window.getComputedStyle(noSignals);
        results.elements.noSignalsSection = {
          exists: true,
          background: noSignalsStyle.background.substring(0, 100),
          textAlign: noSignalsStyle.textAlign,
          padding: noSignalsStyle.padding
        };
      } else {
        results.elements.noSignalsSection = { exists: false };
      }

      return results;
    });

    console.log('🔍 ANALYSIS RESULTS:\n');
    console.log(`📄 Page: ${analysis.pageTitle}`);
    console.log(`🔗 URL: ${analysis.url}\n`);

    console.log('📦 HEADER SECTION:');
    if (analysis.elements.header.exists) {
      console.log('   ✅ Header exists');
      console.log(`   🎨 Background: ${analysis.elements.header.background}`);
      console.log(`   📐 Border Radius: ${analysis.elements.header.borderRadius}`);
      console.log(`   📏 Padding: ${analysis.elements.header.padding}`);
      console.log(`   🌀 Backdrop Filter: ${analysis.elements.header.backdropFilter}`);
    } else {
      console.log('   ❌ Header NOT FOUND');
    }

    console.log('\n📊 STATS CARDS:');
    if (analysis.elements.statsCards.exists) {
      console.log(`   ✅ Stats cards exist (${analysis.elements.statsCards.count} cards)`);
      console.log(`   📐 Container Display: ${analysis.elements.statsCards.containerDisplay}`);
      console.log(`   🎯 Grid Columns: ${analysis.elements.statsCards.gridColumns}`);
      console.log(`   🎨 Card Background: ${analysis.elements.statsCards.cardBackground}`);
      console.log(`   🖼️ Card Border: ${analysis.elements.statsCards.cardBorder}`);
      console.log(`   📏 Card Padding: ${analysis.elements.statsCards.cardPadding}`);
    } else {
      console.log('   ❌ Stats cards NOT FOUND');
    }

    console.log('\n🎛️ FILTER BAR:');
    if (analysis.elements.filterBar.exists) {
      console.log('   ✅ Filter bar exists');
      console.log(`   📐 Display: ${analysis.elements.filterBar.display}`);
      console.log(`   🎨 Background: ${analysis.elements.filterBar.background}`);
      console.log(`   📏 Padding: ${analysis.elements.filterBar.padding}`);
      console.log(`   📐 Gap: ${analysis.elements.filterBar.gap}`);
    } else {
      console.log('   ❌ Filter bar NOT FOUND');
    }

    console.log('\n🔄 REFRESH BUTTON:');
    if (analysis.elements.refreshButton.exists) {
      console.log('   ✅ Refresh button exists');
      console.log(`   🎨 Background: ${analysis.elements.refreshButton.background}`);
      console.log(`   📏 Padding: ${analysis.elements.refreshButton.padding}`);
      console.log(`   📐 Border Radius: ${analysis.elements.refreshButton.borderRadius}`);
      console.log(`   📐 Display: ${analysis.elements.refreshButton.display}`);
    } else {
      console.log('   ❌ Refresh button NOT FOUND');
    }

    console.log('\n📭 NO SIGNALS SECTION:');
    if (analysis.elements.noSignalsSection.exists) {
      console.log('   ✅ No signals section exists');
      console.log(`   🎨 Background: ${analysis.elements.noSignalsSection.background}`);
      console.log(`   📐 Text Align: ${analysis.elements.noSignalsSection.textAlign}`);
      console.log(`   📏 Padding: ${analysis.elements.noSignalsSection.padding}`);
    } else {
      console.log('   ❌ No signals section NOT FOUND');
    }

    // Take high quality screenshot
    await page.screenshot({
      path: 'signals-detailed-analysis.png',
      fullPage: true,
      quality: 100
    });
    console.log('\n📸 High-quality screenshot saved: signals-detailed-analysis.png');

  } catch (error) {
    console.error('❌ Analysis failed:', error.message);
  } finally {
    await browser.close();
    console.log('\n🏁 Detailed analysis completed');
  }
})();