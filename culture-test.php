<?php
declare(strict_types=1);

session_name('googa'); session_start();
require_once __DIR__ . '/lib/store.php';
require_once __DIR__ . '/lib/version.php';
$context = googa_session_context();
$assetVersion = googa_app_version();
?><!doctype html>
<html lang="so">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <meta name="theme-color" content="#123b59">
  <title>Googa – Bariis på Grandis?!</title>
  <meta name="description" content="Gratis kulturkompass for voksne mellom Norge og Somalia. Ingen innlogging – svarene blir på enheten din.">
  <meta property="og:type" content="website"><meta property="og:site_name" content="Googa"><meta property="og:title" content="Bariis på Grandis?! – hvor langt har det gått?"><meta property="og:description" content="Ta Googas gratis, humoristiske kulturkompass for voksne. Ingen innlogging, og svarene blir på enheten din."><meta property="og:url" content="https://ferdighet.no/googa/culture-test.php"><meta property="og:image" content="https://ferdighet.no/googa/assets/culture-test-share.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('culture-test.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('culture-commerce.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('culture-sharing.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('culture-coupons.css'), ENT_QUOTES, 'UTF-8') ?>">
  <style>.culture-more-offers{grid-template-columns:repeat(2,1fr)}</style>
</head>
<body class="culture-test-page">
  <main class="culture-shell">
    <header class="culture-topbar">
      <a class="culture-brand" href="./"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>GOOGA</span></a>
      <div class="culture-toolbar">
        <span class="culture-preview" data-so="BILAASH · AKOON LOOMA BAAHNA" data-no="GRATIS · UTEN INNLOGGING">BILAASH · AKOON LOOMA BAAHNA</span>
        <div class="culture-voice-picker" role="radiogroup" aria-label="Dooro codka Soomaaliga">
          <span class="culture-voice-label" data-so="CODKA" data-no="STEMME">CODKA</span>
          <button class="culture-voice active" id="voiceUbax" type="button" role="radio" aria-checked="true" data-voice="ubax" aria-label="Dooro codka Ubax"><img src="<?= htmlspecialchars(googa_asset_url('assets/voices/narrator-ubax.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>Ubax</span></button>
          <button class="culture-voice" id="voiceMuuse" type="button" role="radio" aria-checked="false" data-voice="muuse" aria-label="Dooro codka Muuse"><img src="<?= htmlspecialchars(googa_asset_url('assets/voices/narrator-muuse.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>Muuse</span></button>
        </div>
        <button class="culture-language" id="cultureLanguage" type="button" aria-label="Vis norsk støtte">🇳🇴 <span>Norsk</span></button>
      </div>
    </header>

    <section class="culture-intro" id="cultureIntro">
      <div class="culture-hero">
        <div class="culture-hero-copy">
          <p class="culture-kicker" data-so="TIJAABO DHAQAN OO BILAASH AH" data-no="GRATIS KULTURKOMPASS">TIJAABO DHAQAN OO BILAASH AH</p>
          <h1 id="cultureTitle">Bariis på Grandis?!</h1>
          <p class="culture-subtitle" id="cultureSubtitle">Intee ayay gaartay?</p>
          <div class="culture-thesis"><span aria-hidden="true">↔</span><p id="cultureThesis">Kaftan yar, laba dhaqan iyo nolol dhab ah. Ma aha inaad mid doorato.</p><button class="culture-speak" type="button" data-speak-button data-speak-so="Kaftan yar, laba dhaqan iyo nolol dhab ah. Ma aha inaad mid doorato. Aqoonta midkood waxay kaa caawin kartaa inaad kan kale si fiican u fahanto." aria-label="Dhegeyso hordhaca"></button></div>
          <div class="culture-facts"><span>◷ <b>6–8</b> <i data-so="daqiiqo" data-no="minutter">daqiiqo</i></span><span>✓ <b>24</b> <i data-so="su’aalood" data-no="spørsmål">su’aalood</i></span><span data-so="⌁ qalabkaaga oo keliya" data-no="⌁ bare på enheten din">⌁ qalabkaaga oo keliya</span></div>
          <button class="culture-primary" id="cultureStart" type="button"><span>Bilow tijaabada</span><b>→</b></button>
        </div>
        <div class="culture-visual" aria-hidden="true"><div class="culture-country norway"><span>🇳🇴</span><small>NORWAY</small></div><div class="culture-person"><span>👩🏾</span><i></i></div><div class="culture-country somalia"><span>🇸🇴</span><small>SOOMAALIYA</small></div><div class="culture-bridge-line"></div></div>
      </div>
      <aside class="culture-note"><span>i</span><p id="cultureDisclaimer">Tani waa tijaabo madadaalo iyo is-milicsi ah. Ma cabbirayso inta aad Soomaali ama Noorwiiji dhab ahaan u tahay.</p><button class="culture-speak" type="button" data-speak-button data-speak-so="Tani waa tijaabo madadaalo iyo is-milicsi ah oo loogu talagalay dadka waaweyn. Ma cabbirayso inta aad Soomaali ama Noorwiiji dhab ahaan u tahay. Jawaabaha iyo natiijadu qalabkaaga ayey ku sii jiraan." aria-label="Dhegeyso sharaxaadda"></button></aside>
      <p class="culture-review"><a href="contact.php?topic=Bariis%20p%C3%A5%20Grandis" data-so="💬 Farriin u dir Arab →" data-no="💬 Send melding til Arab →">💬 Farriin u dir Arab →</a></p>
    </section>

    <section class="culture-question hidden" id="cultureQuestion">
      <div class="culture-progress-row"><button class="culture-back" id="cultureBack" type="button" aria-label="Tilbake">←</button><div><span id="cultureProgressLabel">1 / 24</span><div class="culture-progress"><i id="cultureProgressFill"></i></div></div><span class="culture-domain-icon" id="cultureDomainIcon" aria-hidden="true">🧭</span></div>
      <article class="culture-question-card">
        <div class="culture-domain"><span id="cultureDomainName">Aqoonta bulshada Norway</span><small id="cultureDomainNo">Norsk samfunnskompetanse</small></div>
        <div class="culture-question-heading"><h1 id="cultureQuestionSo"></h1><button class="culture-speak culture-question-speak" id="cultureQuestionSpeak" type="button" aria-label="Dhegeyso su’aasha"></button></div>
        <p class="culture-question-no hidden" id="cultureQuestionNo"></p>
        <button class="culture-translate" id="cultureTranslate" type="button">🇳🇴 <span>Eeg Af-Noorwiiji</span></button>
        <div class="culture-scale" id="cultureScale"></div>
        <button class="culture-primary culture-finish hidden" id="cultureFinish" type="button" style="width:100%;margin-top:16px">Arag natiijada</button>
      </article>
    </section>

    <section class="culture-result hidden" id="cultureResult">
      <div class="culture-result-hero"><span id="cultureResultIcon">✨</span><p>JAWAABTAADA</p><h1 id="cultureResultTitle"></h1><p id="cultureResultTitleNo"></p><button class="culture-speak" id="cultureResultSpeak" type="button" aria-label="Dhegeyso natiijada"></button></div>
      <p class="culture-result-intro" id="cultureResultIntro"></p>
      <div class="culture-axes" id="cultureAxes"></div>
      <section class="culture-actions"><p class="culture-kicker" data-so="TALLAABOOYINKA XIGA" data-no="NESTE STEG">TALLAABOOYINKA XIGA</p><h2 data-so="Labadaada buundo ee xiga" data-no="Dine to neste broer">Labadaada buundo ee xiga</h2><div id="cultureActions"></div></section>
      <section class="culture-share" id="cultureShareCard"><div class="share-brand"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>GOOGA · BARIIS PÅ GRANDIS?!</span></div><p id="cultureAwardLabel">BILADDAADA MAANTA</p><h2 id="cultureShareTitle"></h2><div id="cultureShareAxes"></div><footer><span>🍚</span><b>Laba dhaqan. Hal nolol. Isku-darkaaga gaarka ah.</b><span>🍕</span></footer></section>
      <section class="culture-share-tools" aria-labelledby="cultureShareHeading">
        <div><p class="culture-kicker">U DIR QOF AAD TAQAAN</p><h2 id="cultureShareHeading">Yaa ku xiga?</h2><p id="cultureShareHelp">La wadaag biladdaada, kadibna arag qofka qoyska ama saaxiibbada kaa duwan.</p></div>
        <div class="culture-share-buttons">
          <button class="culture-share-action share-native" id="cultureShare" type="button"><span>↗</span><b>La wadaag</b></button>
          <button class="culture-share-action share-facebook" id="cultureShareFacebook" type="button"><span>f</span><b>Facebook</b></button>
          <button class="culture-share-action share-email" id="cultureShareEmail" type="button"><span>✉</span><b>E-mail</b></button>
          <button class="culture-share-action share-message" id="cultureShareMessage" type="button"><span>💬</span><b>iMessage</b></button>
          <button class="culture-share-action share-qr" id="cultureShareQr" type="button"><span>▦</span><b>QR-koodh</b></button>
          <button class="culture-share-action share-save" id="cultureDownload" type="button"><span>⇩</span><b>Kaydi kaarka</b></button>
        </div>
        <p class="culture-share-feedback" id="cultureShareFeedback" role="status" aria-live="polite"></p>
      </section>
      <section class="culture-coupon-demo" aria-labelledby="couponDemoTitle">
        <p class="culture-kicker" data-so="MEELAHA CUNTADA" data-no="SPISESTEDER">MEELAHA CUNTADA</p>
        <h2 id="couponDemoTitle" data-so="Cuntooyin aad tijaabin karto" data-no="Mat du kan prøve">Cuntooyin aad tijaabin karto</h2>
        <p class="coupon-lead" data-so="Laba fikradood oo ku saabsan dalabyo makhaayadeed." data-no="To ideer til restauranttilbud.">Laba fikradood oo ku saabsan dalabyo makhaayadeed.</p>
        <div class="coupon-grid">
          <button class="coupon-card" type="button" data-coupon-demo aria-label="Vis eksempeltilbud fra Banaadir Bord">
            <img src="<?= htmlspecialchars(googa_asset_url('assets/coupons/banaadir-bord-samosa.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Sprøstekte samosaer med te">
            <span class="coupon-example" data-so="FIKRAD" data-no="IDÉ">FIKRAD</span>
            <span class="coupon-copy"><small>Banaadir Bord · Oslo</small><strong data-so="2 sambuus qiimaha 1" data-no="2 samosa for prisen av 1">2 sambuus qiimaha 1</strong><em data-so="Eeg kuubanka →" data-no="Vis kupong →">Eeg kuubanka →</em></span>
          </button>
          <button class="coupon-card" type="button" data-coupon-demo aria-label="Vis eksempeltilbud fra Hodan Mat">
            <img src="<?= htmlspecialchars(googa_asset_url('assets/coupons/hodan-mat-rice.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Grillet kylling med krydret ris">
            <span class="coupon-example" data-so="FIKRAD" data-no="IDÉ">FIKRAD</span>
            <span class="coupon-copy"><small>Hodan Mat · Oslo</small><strong data-so="Cabitaan bilaash ah markaad cunto iibsato" data-no="Gratis drikke til maten">Cabitaan bilaash ah markaad cunto iibsato</strong><em data-so="Eeg kuubanka →" data-no="Vis kupong →">Eeg kuubanka →</em></span>
          </button>
        </div>
      </section>
      <section class="culture-commerce"><p class="culture-kicker" data-so="QAADO BUUNDADA QOYSKA" data-no="TA BROEN MED HJEM">QAADO BUUNDADA QOYSKA</p><h2 data-so="Googa la tijaabi qoyskaaga" data-no="Prøv Googa med familien">Googa la tijaabi qoyskaaga</h2><p data-so="Halxiraalo, cod Af-Soomaali ah iyo ciyaar qoyska oo dhan ah." data-no="Somaliske gåter, lyd og lek for hele familien.">Halxiraalo, cod Af-Soomaali ah iyo ciyaar qoyska oo dhan ah.</p><a class="culture-buy" href="./?buy=trial"><span><small data-so="2 maalmood oo tijaabo ah" data-no="2 dagers prøveperiode">2 maalmood oo tijaabo ah</small><b data-so="Ku bilow kr 5" data-no="Start for kr 5">Ku bilow kr 5</b><em data-so="Kadib kr 50 bishii · jooji wakhti kasta" data-no="Deretter kr 50 per måned · si opp når som helst">Kadib kr 50 bishii · jooji wakhti kasta</em></span><strong>→</strong></a><div class="culture-more-offers"><a href="gift.php" data-so="🎁 Hadiyad sii qoys" data-no="🎁 Gi Googa i gave">🎁 Hadiyad sii qoys</a><a href="./#annual" data-so="★ Kr 499 sanadkii" data-no="★ Kr 499 per år">★ Kr 499 sanadkii</a></div></section>
      <div class="culture-result-buttons"><button class="culture-primary" id="cultureRestart" type="button" data-so="Mar kale samee" data-no="Ta testen på nytt">Mar kale samee</button><a class="culture-secondary" href="./" data-so="Ku noqo Googa" data-no="Tilbake til Googa">Ku noqo Googa</a></div>
    </section>
  </main>
  <div class="culture-qr-modal hidden" id="cultureQrModal" role="dialog" aria-modal="true" aria-labelledby="cultureQrTitle">
    <div class="culture-qr-backdrop" data-qr-close></div>
    <section class="culture-qr-panel">
      <button class="culture-qr-close" type="button" data-qr-close aria-label="Xir">×</button>
      <img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="">
      <p class="culture-kicker">BARIIS PÅ GRANDIS?!</p>
      <h2 id="cultureQrTitle">Kaamirada ku akhri oo tijaabi</h2>
      <p id="cultureQrHelp">Saaxiibkaa ha ku akhriyo QR-koodhka kaamirada taleefankiisa si uu tijaabada u bilaabo.</p>
      <div class="culture-qr-code" id="cultureQrCode"></div>
      <button class="culture-secondary" id="cultureCopyLink" type="button">Koobi garee linkiga</button>
    </section>
  </div>
  <script src="<?= htmlspecialchars(googa_asset_url('assets/read-aloud.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('assets/vendor/qrcode.bundle.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('culture-test-bank.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('culture-test.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <dialog class="coupon-dialog" id="couponDialog" aria-labelledby="couponDialogTitle"><form method="dialog"><span class="coupon-dialog-icon" aria-hidden="true">🍽️</span><h2 id="couponDialogTitle">Fikrad ku saabsan dalab</h2><p>Googa weli heshiis lama laha makhaayadaha. Halkan waxaa lagu muujinayaa sida dalabku u shaqayn karo.</p><div class="coupon-prospects"><b>Meelaha aanu eegnay</b><ul><li><span>🍛</span><p><strong>Waaberi Restaurant</strong><small>Grønland · Oslo</small></p></li><li><span>🥘</span><p><strong>Safari Grill</strong><small>Grønland · Oslo</small></p></li></ul></div><p class="coupon-disclosure">Tani ma aha xayeysiis. Googa hadda heshiis lama laha meelahan.</p><button class="culture-primary" type="submit">Waan fahmay</button></form></dialog>
  <script src="<?= htmlspecialchars(googa_asset_url('culture-coupons.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <?php require __DIR__ . '/owner-corrections-init.php'; ?>
</body>
</html>
