const { chromium } = require('playwright');

const base = process.env.GOOGA_BASE || 'http://127.0.0.1:8765/';
const sessionId = process.env.GOOGA_SESSION_ID || 'codexculturetest';

async function inspect(page, label) {
  const metrics = await page.evaluate(() => ({
    width: document.documentElement.clientWidth,
    scrollWidth: document.documentElement.scrollWidth,
    speakers: document.querySelectorAll('.culture-speak').length,
    nestedButtons: document.querySelectorAll('button button').length
  }));
  if (metrics.scrollWidth > metrics.width + 1) throw new Error(`${label}: horizontal overflow`);
  if (metrics.nestedButtons) throw new Error(`${label}: nested buttons`);
  return metrics;
}

(async () => {
  const browser = await chromium.launch();
  for (const viewport of [{ width:390,height:844,name:'mobile' },{ width:1440,height:900,name:'desktop' }]) {
    const context = await browser.newContext({ viewport, deviceScaleFactor: viewport.name === 'mobile' ? 2 : 1 });
    await context.addCookies([{ name:'googa', value:sessionId, url:base }]);
    const page = await context.newPage();
    const errors = [];
    page.on('console', msg => { if (msg.type() === 'error') errors.push(msg.text()); });
    page.on('pageerror', error => errors.push(error.message));
    await page.goto(`${base}culture-test.php`, { waitUntil:'networkidle' });
    if (!await page.getByRole('heading', { name:'Laba dal, hal sheeko' }).isVisible()) throw new Error('Intro missing');
    const intro = await inspect(page, `${viewport.name}-intro`);
    if (intro.speakers < 2) throw new Error('Intro audio controls missing');
    const introAudio = page.waitForResponse(response => response.url().includes('/audio/culture-test/intro.mp3'));
    await page.locator('.culture-thesis .culture-speak').click();
    if (!(await introAudio).ok()) throw new Error('Intro MP3 failed');
    await page.screenshot({ path:`/tmp/googa-culture-${viewport.name}-intro.png`, fullPage:true });
    await page.getByRole('button', { name:/Bilow tijaabada/ }).click();
    await inspect(page, `${viewport.name}-question`);
    const questionSpeakers = await page.locator('#cultureQuestion .culture-speak').count();
    if (questionSpeakers !== 6) throw new Error(`Expected six question/scale speakers, got ${questionSpeakers}`);
    const qAudio = page.waitForResponse(response => response.url().includes('/audio/culture-test/question-01.mp3'));
    await page.locator('#cultureQuestionSpeak').click();
    if (!(await qAudio).ok()) throw new Error('Question MP3 failed');
    await page.locator('#cultureTranslate').click();
    if (!await page.locator('#cultureQuestionNo').isVisible()) throw new Error('Norwegian support did not open');
    await page.screenshot({ path:`/tmp/googa-culture-${viewport.name}-question.png`, fullPage:true });
    for (let i = 0; i < 24; i += 1) await page.locator('[data-value="4"]').click();
    if (!await page.getByRole('heading', { name:'Isku-xiraha dhaqamada' }).isVisible()) throw new Error('High result title wrong');
    if (await page.locator('.culture-axes article').count() !== 3) throw new Error('Three axes missing');
    if (await page.locator('#cultureActions article').count() !== 2) throw new Error('Two next steps missing');
    const result = await inspect(page, `${viewport.name}-result`);
    if (result.speakers < 6) throw new Error('Result audio controls missing');
    const resultAudio = page.waitForResponse(response => response.url().includes('/audio/culture-test/result-connector.mp3'));
    await page.locator('#cultureResultSpeak').click();
    if (!(await resultAudio).ok()) throw new Error('Result MP3 failed');
    await page.screenshot({ path:`/tmp/googa-culture-${viewport.name}-result.png`, fullPage:true });
    if (errors.length) throw new Error(`${viewport.name}: ${errors.join(' | ')}`);
    await context.close();
  }
  const guest = await browser.newPage();
  const response = await guest.goto(`${base}culture-test.php`, { waitUntil:'domcontentloaded' });
  if (response.status() !== 200 || !guest.url().endsWith('/')) throw new Error('Guest access gate failed');
  await browser.close();
  console.log('OK: owner gate, mobile/desktop layout, 24 questions, symbols, Norwegian support and prerecorded Ubax audio');
})().catch(error => { console.error(error); process.exit(1); });
