<?php
declare(strict_types=1);

session_name('googa');
session_start();

require_once __DIR__ . '/lib/store.php';
require_once __DIR__ . '/lib/version.php';

$context = googa_session_context();
if (empty($context['authenticated']) || empty($context['access']['allowed'])) {
    header('Location: ./');
    exit;
}
if (($context['role'] ?? '') !== 'owner') {
    http_response_code(404);
    header('Location: ./');
    exit;
}
$assetVersion = googa_app_version();
?><!doctype html>
<html lang="so">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <meta name="theme-color" content="#123b59">
  <title>Googa – Laba dal, hal sheeko</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('culture-test.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="culture-test-page">
  <main class="culture-shell">
    <header class="culture-topbar">
      <a class="culture-brand" href="./"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>GOOGA</span></a>
      <div><span class="culture-preview">EIERFORHÅNDSVISNING</span><button class="culture-language" id="cultureLanguage" type="button" aria-label="Vis norsk støtte">🇳🇴 <span>Norsk</span></button></div>
    </header>

    <section class="culture-intro" id="cultureIntro">
      <div class="culture-hero">
        <div class="culture-hero-copy">
          <p class="culture-kicker">LABA DAL · HAL SHEEKO</p>
          <h1 id="cultureTitle">Laba dal, hal sheeko</h1>
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
      <div class="culture-result-buttons"><button class="culture-primary" id="cultureRestart" type="button">Mar kale samee</button><a class="culture-secondary" href="./">Ku noqo Googa</a></div>
      <p class="culture-review">Utkast · Ikke normert eller psykometrisk validert.</p>
    </section>
  </main>
  <script src="<?= htmlspecialchars(googa_asset_url('assets/read-aloud.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('culture-test-bank.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('culture-test.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
