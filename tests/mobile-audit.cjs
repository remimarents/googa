const { chromium } = require('playwright');

const base = process.env.GOOGA_BASE || 'https://ferdighet.no/googa/';
const output = process.env.GOOGA_AUDIT_OUTPUT || '/tmp/googa-mobile-audit';

async function inspect(page, name) {
  await page.waitForLoadState('networkidle');
  const metrics = await page.evaluate(() => ({
    width: document.documentElement.clientWidth,
    scrollWidth: document.documentElement.scrollWidth,
    readAloud: document.querySelectorAll('.read-aloud').length,
    nestedButtons: document.querySelectorAll('button button').length,
  }));
  await page.screenshot({ path: `${output}-${name}.png`, fullPage: true });
  if (metrics.scrollWidth > metrics.width + 1) throw new Error(`${name}: horizontal overflow`);
  if (metrics.nestedButtons) throw new Error(`${name}: nested interactive buttons`);
  console.log(JSON.stringify({ name, ...metrics }));
  return metrics;
}

(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true });
  const page = await context.newPage();
  await page.goto(`${base}?logout=1`);
  const login = await inspect(page, 'login');
  if (login.readAloud < 5) throw new Error('Login is missing expected Somali read-aloud controls');
  if (await page.locator('form[action="checkout.php"] .plan').count() !== 1) throw new Error('The kr 5 Stripe offer is missing');
  const audioRequest = page.waitForResponse((response) => response.url().includes('/audio/ui/login-hero.mp3'));
  await page.locator('.lead .read-aloud').click();
  if (!(await audioRequest).ok()) throw new Error('Ubax login audio failed');
  await page.locator('#lang').click();
  if (await page.locator('.read-aloud').count() !== login.readAloud) throw new Error('Language switch removed read-aloud controls');

  await page.goto(`${base}reset-password.php?t=invalid`);
  if ((await inspect(page, 'reset-expired')).readAloud < 1) throw new Error('Password page is missing read-aloud');

  await page.goto(`${base}family-pending.php`);
  if ((await inspect(page, 'family-pending')).readAloud < 1) throw new Error('Family pending page is missing read-aloud');

  const email = process.env.GOOGA_TEST_EMAIL;
  const password = process.env.GOOGA_TEST_PASSWORD;
  if (email && password) {
    await page.goto(base);
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(password);
    await page.locator('.login-grid .primary').click();
    await page.waitForLoadState('networkidle');
    if (await page.getByRole('button', { name: 'Månedlig bruker' }).count()) await page.getByRole('button', { name: 'Månedlig bruker' }).click();
    const home = await inspect(page, 'home');
    if (home.readAloud < 5) throw new Error('Home is missing age and welcome read-aloud controls');
    await page.locator('.age-card').first().click();
    const riddle = await inspect(page, 'riddle');
    if (riddle.readAloud < 3) throw new Error('Riddle answers are missing read-aloud controls');
    await page.goto(`${base}family.php`);
    if ((await inspect(page, 'family')).readAloud < 3) throw new Error('Family page is missing read-aloud controls');
  }

  await browser.close();
})().catch((error) => {
  console.error(error);
  process.exit(1);
});
