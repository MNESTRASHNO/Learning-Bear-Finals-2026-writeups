const puppeteer = require('puppeteer-core');

const WEB_URL = process.env.WEB_URL || 'http://nginx';
const FLAG = process.env.FLAG || 'flag{redacted}';

async function visit(noteId) {
  const browser = await puppeteer.launch({
    executablePath: process.env.PUPPETEER_EXECUTABLE_PATH || '/usr/bin/chromium',
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu'],
    headless: 'new',
  });

  try {
    const page = await browser.newPage();
    page.setDefaultTimeout(30_000);

    await page.goto(WEB_URL);

    await page.setCookie({
      name: 'flag',
      value: FLAG,
      domain: new URL(WEB_URL).hostname,
      path: '/',
      httpOnly: false,
    });

    await page.goto(`${WEB_URL}/note/${noteId}`);
    await new Promise(r => setTimeout(r, 10_000));
  } finally {
    await browser.close();
  }
}

module.exports = { visit };
