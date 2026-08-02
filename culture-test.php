<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/version.php';
$assetVersion = googa_app_version();
?><!doctype html>
<html lang="so">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <meta name="theme-color" content="#123b59">
  <title>Googa – Bariis på Grandis</title>
  <meta name="description" content="Gratis kulturkompass for voksne mellom Norge og Somalia. Ingen innlogging – svarene blir på enheten din.">
  <meta property="og:type" content="website"><meta property="og:site_name" content="Googa"><meta property="og:title" content="Bariis på Grandis – gratis kulturkompass"><meta property="og:description" content="Ta Googas gratis kulturkompass for voksne. Ingen innlogging, og svarene blir på enheten din."><meta property="og:url" content="https://ferdighet.no/googa/culture-test.php"><meta property="og:image" content="https://ferdighet.no/googa/assets/culture-test-share.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('culture-test.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('culture-commerce.css'), ENT_QUOTES, 'UTF-8') ?>">
  <style>.culture-more-offers{grid-template-columns:repeat(2,1fr)}</style>
</head>
<body class="culture-test-page">
  <main class="culture-shell">
    <header class="culture-topbar">
      <a class="culture-brand" href="./"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>GOOGA</span></a>
      <div class="culture-toolbar">
        <span class="culture-preview">BILAASH · AKOON LOOMA BAAHNA</span>
        <div class="culture-voice-picker" role="radiogroup" aria-label="Dooro codka Soomaaliga">
          <span class="culture-voice-label">CODKA</span>
          <button class="culture-voice active" id="voiceUbax" type="button" role="radio" aria-checked="true" data-voice="ubax" aria-label="Dooro codka Ubax"><img src="<?= htmlspecialchars(googa_asset_url('assets/voices/narrator-ubax.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>Ubax</span></button>
          <button class="culture-voice" id="voiceMuuse" type="button" role="radio" aria-checked="false" data-voice="muuse" aria-label="Dooro codka Muuse"><img src="<?= htmlspecialchars(googa_asset_url('assets/voices/narrator-muuse.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>Muuse</span></button>
        </div>
        <button class="culture-language" id="cultureLanguage" type="button" aria-label="Vis norsk støtte">🇳🇴 <span>Norsk</span></button>
      </div>
    </header>

    <section class="culture-intro" id="cultureIntro">
      <div class="culture-hero">
        <div class="culture-hero-copy">
          <p class="culture-kicker">GRATIS KULTURKOMPASS</p>
          <h1 id="cultureTitle">Bariis på Grandis</h1>
          <p class="culture-subtitle" id="cultureSubtitle">Tilmaamahaaga dhaqan ee Soomaali iyo Noorweey</p>
          <div class="culture-thesis"><span aria-hidden="true">↔</span><p id="cultureThesis">Uma baahnid inaad kala doorato labadaada hoy. Aqoonta aad ka hesho midkood waxay kaa caawin kartaa inaad si fiican u fahanto kan kale.</p><button class="culture-speak" type="button" data-speak-button data-speak-so="Uma baahnid inaad kala doorato labadaada hoy. Aqoonta aad ka hesho midkood waxay kaa caawin kartaa inaad si fiican u fahanto kan kale." data-speak-audio="audio/culture-test/intro.mp3" aria-label="Dhegeyso hordhaca"></button></div>
          <div class="culture-facts"><span>◷ <b>6–8</b> daqiiqo</span><span>✓ <b>24</b> su’aalood</span><span>⌁ qalabkaaga oo keliya</span></div>
          <button class="culture-primary" id="cultureStart" type="button"><span>Bilow tijaabada</span><b>→</b></button>
        </div>
        <div class="culture-visual" aria-hidden="true"><div class="culture-country norway"><span>🇳🇴</span><small>NORWAY</small></div><div class="culture-person"><span>👩🏾</span><i></i></div><div class="culture-country somalia"><span>🇸🇴</span><small>SOOMAALIYA</small></div><div class="culture-bridge-line"></div></div>
      </div>
      <aside class="culture-note"><span>i</span><p id="cultureDisclaimer">Kani waa is-milicsi loogu talagalay dadka waaweyn. Ma cabbirayo inta aad Soomaali ama Noorwiiji dhab ahaan u tahay, mana qiimeynayo fikirkaaga siyaasadeed. Jawaabahaagu qalabkaaga ayey ku sii jiraan.</p><button class="culture-speak" type="button" data-speak-button data-speak-so="Kani waa is-milicsi loogu talagalay dadka waaweyn. Ma cabbirayo inta aad Soomaali ama Noorwiiji dhab ahaan u tahay, mana qiimeynayo fikirkaaga siyaasadeed. Jawaabahaagu qalabkaaga ayey ku sii jiraan." data-speak-audio="audio/culture-test/disclaimer.mp3" aria-label="Dhegeyso sharaxaadda"></button></aside>
      <p class="culture-review">Utkast · Somalisk tekst skal kvalitetssikres av pedagog før publisering.</p>
    </section>

    <section class="culture-question hidden" id="cultureQuestion">
      <div class="culture-progress-row"><button class="culture-back" id="cultureBack" type="button" aria-label="Tilbake">←</button><div><span id="cultureProgressLabel">1 / 24</span><div class="culture-progress"><i id="cultureProgressFill"></i></div></div><span class="culture-domain-icon" id="cultureDomainIcon" aria-hidden="true">🧭</span></div>
      <article class="culture-question-card">
        <div class="culture-domain"><span id="cultureDomainName">Aqoonta bulshada Norway</span><small id="cultureDomainNo">Norsk samfunnskompetanse</small></div>
        <div class="culture-question-heading"><h1 id="cultureQuestionSo"></h1><button class="culture-speak culture-question-speak" id="cultureQuestionSpeak" type="button" aria-label="Dhegeyso su’aasha"></button></div>
        <p class="culture-question-no hidden" id="cultureQuestionNo"></p>
        <button class="culture-translate" id="cultureTranslate" type="button">🇳🇴 <span>Eeg af-Noorwiiji</span></button>
        <div class="culture-scale" id="cultureScale"></div>
      </article>
    </section>

    <section class="culture-result hidden" id="cultureResult">
      <div class="culture-result-hero"><span id="cultureResultIcon">✨</span><p>JAWAABTAADA</p><h1 id="cultureResultTitle"></h1><p id="cultureResultTitleNo"></p><button class="culture-speak" id="cultureResultSpeak" type="button" aria-label="Dhegeyso natiijada"></button></div>
      <p class="culture-result-intro" id="cultureResultIntro"></p>
      <div class="culture-axes" id="cultureAxes"></div>
      <section class="culture-actions"><p class="culture-kicker">TALLAABOOYINKA XIGA</p><h2>Labadaada buundo ee xiga</h2><div id="cultureActions"></div></section>
      <section class="culture-share" id="cultureShareCard"><div class="share-brand"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>GOOGA · LABA DAL, HAL SHEEKO</span></div><p>Tilmaamaha dhaqankayga</p><h2 id="cultureShareTitle"></h2><div id="cultureShareAxes"></div><footer><span>🇳🇴</span><b>Aqoonta hal hoy waxay naga caawisaa inaan fahanno kan kale.</b><span>🇸🇴</span></footer></section>
      <div class="culture-share-buttons"><button class="culture-primary" id="cultureShare" type="button">La wadaag natiijada</button><button class="culture-secondary" id="cultureDownload" type="button">Kaydi kaarka</button></div>
      <section class="culture-commerce"><p class="culture-kicker">QAADO BUUNDADA QOYSKA</p><h2>Googa la tijaabi qoyskaaga</h2><p>Halxiraalo, cod Af-Soomaali ah iyo ciyaar qoyska oo dhan ah.</p><a class="culture-buy" href="./?buy=trial"><span><small>2 maalmood oo tijaabo ah</small><b>Ku bilow kr 5</b><em>Kadib kr 50 bishii · jooji wakhti kasta</em></span><strong>→</strong></a><div class="culture-more-offers"><a href="gift.php">🎁 Hadiyad sii qoys</a><a href="./#annual">★ Kr 499 sanadkii</a></div></section>
      <div class="culture-result-buttons"><button class="culture-primary" id="cultureRestart" type="button">Mar kale samee</button><a class="culture-secondary" href="./">Ku noqo Googa</a></div>
      <p class="culture-review">Utkast · Ikke normert eller psykometrisk validert.</p>
    </section>
  </main>
  <script src="<?= htmlspecialchars(googa_asset_url('assets/read-aloud.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('culture-test-bank.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('culture-test.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
