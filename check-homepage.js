const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch();
  const page = await browser.newPage();
  
  try {
    await page.goto('http://localhost:8080/', { waitUntil: 'networkidle2' });
    await page.setViewport({ width: 1200, height: 800 });
    
    // Wait for tabs to load
    await page.waitForSelector('.tab-navigation', { timeout: 5000 });
    
    // Take screenshot
    await page.screenshot({ path: 'homepage-current.png', fullPage: true });
    
    // Check if tab navigation exists
    const tabNavigation = await page.$('.tab-btn');
    console.log('Tab navigation exists:', !!tabNavigation);
    
    // Get all tab buttons
    const tabButtons = await page.$$eval('.tab-btn', buttons => 
      buttons.map(btn => btn.textContent.trim())
    );
    console.log('Tab buttons found:', tabButtons);
    
    // Check what's actually on the page
    const pageContent = await page.evaluate(() => {
      const forms = document.querySelectorAll('form');
      const headers = document.querySelectorAll('h1, h2, h3');
      const inputs = document.querySelectorAll('input');
      
      return {
        formCount: forms.length,
        headers: Array.from(headers).map(h => h.textContent.trim()),
        inputLabels: Array.from(inputs).map(input => {
          const label = document.querySelector(`label[for="${input.id}"]`);
          return label ? label.textContent.trim() : input.placeholder || input.name;
        })
      };
    });
    console.log('Page analysis:', pageContent);
    
    // Check if forms exist
    const requestForm = await page.$('#tab-request');
    const signinForm = await page.$('#tab-signin');
    const signupForm = await page.$('#tab-signup');
    
    console.log('Request form exists:', !!requestForm);
    console.log('Signin form exists:', !!signinForm);
    console.log('Signup form exists:', !!signupForm);
    
    console.log('Screenshot saved as homepage-current.png');
    
  } catch (error) {
    console.error('Error:', error);
  }
  
  await browser.close();
})();