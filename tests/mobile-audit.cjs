const { chromium } = require('playwright');

const base = process.env.GOOGA_BASE || 'https://ferdighet.no/googa/';
const output = process.env.GOOGA_AUDIT_OUTPUT || '/tmp/googa-mobile-audit';
const viewportWidth = Number(process.env.GOOGA_VIEWPORT_WIDTH || 390);
const viewportHeight = Number(process.env.GOOGA_VIEWPORT_HEIGHT || 844);

async function inspect(page, name) {
  await page.waitForLoadState('networkidle');
  const metrics = await page.evaluate(() => ({
    width: document.documentElement.clientWidth,
    scrollWidth: document.documentElement.scrollWidth,
    height: document.documentElement.clientHeight,
    scrollHeight: document.documentElement.scrollHeight,
    smallTargets: [...document.querySelectorAll('button,a,input,select,textarea')]
      .filter((node) => {
        const box = node.getBoundingClientRect();
        return box.width > 0 && box.height > 0 && (box.width < 44 || box.height < 44);
      })
      .map((node) => ({
        text: (node.textContent || node.getAttribute('aria-label') || node.tagName).trim().slice(0, 50),
        width: Math.round(node.getBoundingClientRect().width),
        height: Math.round(node.getBoundingClientRect().height),
      })),
  }));
  await page.screenshot({ path: `${output}-${name}.png`, fullPage: true });
  if (metrics.scrollWidth > metrics.width + 1) {
    throw new Error(`${name}: horizontal overflow ${metrics.scrollWidth} > ${metrics.width}`);
  }
  console.log(JSON.stringify({ name, ...metrics }));
}

(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext({
    viewport: { width: viewportWidth, height: viewportHeight },
    deviceScaleFactor: 2,
    isMobile: true,
    hasTouch: true,
  });
  const page = await context.newPage();
  if (process.env.GOOGA_MOCK_QR === '1') {
    await page.route('**/api/auth.php?**', async (route) => {
      const url = new URL(route.request().url());
      const action = url.searchParams.get('action');
      await route.fulfill({
        contentType: 'application/json',
        body: JSON.stringify(action === 'create'
          ? { token: 'test-token', scanUrl: 'https://example.com/googa-login', state: 'pending' }
          : { state: 'pending' }),
      });
    });
  }

  await page.goto(`${base}?logout=1`);
  await inspect(page, 'login');
  const selectedPlanPoll = page.waitForRequest((request) => {
    const url = new URL(request.url());
    return url.pathname.endsWith('/api/auth.php')
      && url.searchParams.get('action') === 'poll'
      && url.searchParams.get('plan') === 'trial';
  });
  await page.locator('.price-choice[data-kind="trial"]').click();
  await page.locator('#code canvas').waitFor();
  await selectedPlanPoll;
  const qrUrl = await page.locator('#manualLink').getAttribute('href');
  await page.waitForTimeout(3200);
  if (await page.locator('#manualLink').getAttribute('href') !== qrUrl) {
    throw new Error('QR token changed while polling');
  }

  await page.goto(`${base}?quick=ahab`);
  await inspect(page, 'owner-mode');

  await page.getByRole('button', { name: 'Månedlig bruker' }).click();
  await inspect(page, 'home');

  await page.locator('.age-card').first().click();
  await inspect(page, 'riddle-youngest');

  await page.getByRole('button', { name: 'Vis tekst på norsk' }).click();
  await inspect(page, 'riddle-norwegian');

  await page.goto(`${base}family.php`);
  await inspect(page, 'family');

  await page.goto(`${base}owner.php`);
  await inspect(page, 'owner-dashboard');

  await page.goto(base);
  page.once('dialog', (dialog) => dialog.accept());
  await page.locator('#logoutDot').click({ clickCount: 3, delay: 100 });
  await page.getByRole('button', { name: 'Ama ku gal QR-koodh' }).waitFor();
  console.log(JSON.stringify({ name: 'logout-flow', ok: true }));

  await browser.close();
})().catch((error) => {
  console.error(error);
  process.exit(1);
});
