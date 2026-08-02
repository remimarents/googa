const { chromium } = require('playwright');
const scoringVectors = require('../config/culture-test-vectors.json');

const base = process.env.GOOGA_BASE || 'http://127.0.0.1:8765/';

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
    const context = await browser.newContext({ viewport, deviceScaleFactor:viewport.name === 'mobile' ? 2 : 1, isMobile:viewport.name === 'mobile', hasTouch:viewport.name === 'mobile' });
    const page = await context.newPage();
    const pageErrors = [];
    page.on('pageerror', error => pageErrors.push(error.message));
    await page.goto(`${base}culture-test.php`, { waitUntil:'networkidle' });
    if (!await page.getByRole('heading', { name:'Bariis på Grandis?!' }).isVisible()) throw new Error('Intro title missing');
    if (!await page.getByText('Intee ayay gaartay?').isVisible()) throw new Error('Somali subtitle missing');
    const intro = await inspect(page, `${viewport.name}-intro`);
    if (intro.speakers < 2) throw new Error('Intro audio controls missing');
    await page.locator('#cultureLanguage').click();
    if (!await page.getByText('Hvor langt har det gått?').isVisible()) throw new Error('Norwegian intro missing');
    const muuseSample = page.waitForResponse(response => response.url().includes('/audio/culture-test/muuse/voice-sample.mp3'));
    await page.locator('#voiceMuuse').click();
    if (!(await muuseSample).ok()) throw new Error('Muuse sample MP3 failed');
    await page.getByRole('button', { name:'Start testen' }).click();
    await inspect(page, `${viewport.name}-question`);
    if (await page.locator('[data-option]').count() !== 4) throw new Error('Expected four situational choices');
    if (await page.locator('#cultureQuestion .culture-speak').count() !== 5) throw new Error('Question and four answers need audio controls');
    await page.locator('#cultureTranslate').click();
    if (!await page.locator('#cultureQuestionNo').isVisible()) throw new Error('Somali support did not open');
    for (let index = 0; index < 24; index += 1) {
      await page.locator('[data-option="c"]').click();
    }
    if (!await page.locator('#cultureQuestion').isVisible()) throw new Error('Last answer must not auto-submit');
    if (!await page.locator('#cultureFinish').isVisible()) throw new Error('Explicit result button missing');
    await page.locator('#cultureFinish').click();
    if (!await page.locator('#cultureResult').isVisible()) throw new Error('Result did not render');
    if (await page.locator('.culture-axes article').count() !== 3) throw new Error('Three axes missing');
    if (await page.locator('#cultureActions article').count() !== 2) throw new Error('Two next steps missing');
    if (await page.locator('.culture-share-action').count() !== 6) throw new Error('Expected six result sharing actions');
    for (const id of ['cultureShare','cultureShareFacebook','cultureShareEmail','cultureShareMessage','cultureShareQr','cultureDownload']) {
      if (!await page.locator(`#${id}`).isVisible()) throw new Error(`${id} sharing action missing`);
    }
    const shareLinks = await page.evaluate(() => window.GOOGA_CULTURE_TEST_ENGINE.shareLinks());
    if (!shareLinks.facebook.startsWith('https://www.facebook.com/sharer/')) throw new Error('Facebook sharing URL missing');
    if (!shareLinks.email.startsWith('mailto:') || !decodeURIComponent(shareLinks.email).includes('Styrelederen for shaah og kaffe')) throw new Error('Email sharing copy missing result title');
    if (!shareLinks.message.startsWith('sms:') || !decodeURIComponent(shareLinks.message).includes('Styrelederen for shaah og kaffe')) throw new Error('iMessage sharing copy missing result title');
    await page.locator('#cultureShareQr').click();
    if (!await page.locator('#cultureQrModal').isVisible()) throw new Error('QR sharing dialog did not open');
    await page.locator('#cultureQrCode canvas').waitFor({ state:'visible' });
    const qrSize = await page.locator('#cultureQrCode canvas').evaluate(canvas => ({ width:canvas.width,height:canvas.height }));
    if (qrSize.width < 200 || qrSize.height < 200) throw new Error('QR sharing image is too small');
    await page.locator('.culture-qr-close').click();
    if (await page.locator('#cultureQrModal').isVisible()) throw new Error('QR sharing dialog did not close');
    const result = await inspect(page, `${viewport.name}-result`);
    if (result.speakers < 6) throw new Error('Result audio controls missing');
    const engine = await page.evaluate(() => window.GOOGA_CULTURE_TEST_ENGINE.scoreAnswers(window.GOOGA_CULTURE_TEST.questions.map(() => 'c')));
    if (!engine.profile || Object.keys(engine.values).length !== 3) throw new Error('Scoring engine unavailable');
    for (const vector of scoringVectors.vectors) {
      const actual = await page.evaluate(answers => window.GOOGA_CULTURE_TEST_ENGINE.scoreAnswers(answers), vector.answers);
      if (actual.profile !== vector.expected_profile || JSON.stringify(actual.values) !== JSON.stringify(vector.expected_values)) throw new Error(`${vector.id} browser scoring mismatch`);
    }
    await page.screenshot({ path:`/tmp/googa-bariis-grandis-${viewport.name}.png`, fullPage:true });
    if (pageErrors.length) throw new Error(`${viewport.name}: ${pageErrors.join(' | ')}`);
    await context.close();
  }
  const guest = await browser.newPage();
  const response = await guest.goto(`${base}culture-test.php`, { waitUntil:'domcontentloaded' });
  if (response.status() !== 200 || !guest.url().includes('culture-test.php')) throw new Error('Free public access failed');
  await browser.close();
  console.log('OK: public access, mobile/desktop layout, 24 scenarios, explicit submit, three-axis scoring, language and audio controls');
})().catch(error => { console.error(error); process.exit(1); });
