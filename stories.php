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
$isOwner = ($context['role'] ?? '') === 'owner';
$isPublic = defined('GOOGA_STORIES_PUBLIC') && GOOGA_STORIES_PUBLIC;
if (!$isPublic && !$isOwner) {
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
  <meta name="theme-color" content="#103654">
  <title>Googa – Sheekooyin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('stories.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('stories-reader-nav.css'), ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('pwa-update.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="stories-page<?= !$isPublic ? ' is-preview' : '' ?>">
  <main class="stories-shell">
    <header class="stories-topbar">
      <a class="stories-brand" href="./"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><span>GOOGA</span></a>
      <div class="stories-top-actions">
        <?php if (!$isPublic): ?><span class="preview-badge">EIERFORHÅNDSVISNING</span><?php endif; ?>
        <button class="preview-badge" data-pwa-update data-update-label="Oppdater" data-updating-label="Oppdaterer …" type="button">Oppdater</button>
        <button class="story-language" id="storyLanguage" type="button" aria-label="Vis norsk tekst">🇳🇴</button>
      </div>
    </header>

    <section class="story-library" id="storyLibrary">
      <div class="library-hero">
        <div>
          <p class="story-kicker" data-so="SHEEKOOYIN CUSUB" data-no="NYE HISTORIER">SHEEKOOYIN CUSUB</p>
          <h1 data-so="Sheeko ku baro Af-Soomaaliga" data-no="Lær somali gjennom historier">Sheeko ku baro Af-Soomaaliga</h1>
          <p data-so="Dhegeyso, taabo qoraalka oo hel caawimo marka aad u baahan tahay." data-no="Lytt, trykk på teksten og få hjelp når du trenger det.">Dhegeyso, taabo qoraalka oo hel caawimo marka aad u baahan tahay.</p>
        </div>
        <div class="library-seal" aria-hidden="true"><span>4</span><small data-so="da'" data-no="nivåer">da'</small></div>
      </div>
      <div class="story-grid" id="storyGrid"></div>
      <?php if (!$isPublic): ?><aside class="release-note"><strong>Planlagt innholdsutvidelse</strong><span>Denne delen er bare synlig for eiere nå. Én funksjonsbryter åpner den senere for alle aktive abonnenter.</span></aside><?php endif; ?>
    </section>

    <section class="story-experience hidden" id="storyExperience">
      <div class="reader-toolbar">
        <button class="reader-back" id="readerBack" type="button">← <span data-so="Sheekooyinka" data-no="Historiene">Sheekooyinka</span></button>
        <div class="scene-progress"><span id="sceneLabel"></span><div><i id="sceneProgress"></i></div></div>
      </div>

      <article class="story-reader" id="storyReader">
        <div class="reader-cover"><img id="readerImage" src="" alt=""><span class="reader-age" id="readerAge"></span></div>
        <div class="reader-content">
          <p class="story-kicker" id="readerSupport"></p>
          <h1 id="readerTitle"></h1>
          <p class="reader-subtitle" id="readerSubtitle"></p>
          <div class="tap-hint"><span aria-hidden="true">☝🏾</span><span id="tapHint">Taabo qoraalka si aad u hesho caawimo norsk ah</span></div>
          <div class="scene-card" id="sceneCard"></div>
          <section class="story-activity hidden" id="storyActivity"><p class="activity-label" data-so="KA FIKIR" data-no="TENK ETTER">KA FIKIR</p><h2 id="activityPrompt"></h2><div id="activityOptions"></div><p class="activityFeedback hidden" id="activityFeedback"></p></section>
          <nav class="scene-nav"><button id="previousScene" class="scene-secondary" type="button">← <span data-so="Hore" data-no="Forrige">Hore</span></button><button id="nextScene" class="scene-primary" type="button"><span data-so="Sii wad" data-no="Fortsett">Sii wad</span> →</button></nav>
        </div>
      </article>
    </section>
  </main>

  <div class="support-overlay hidden" id="supportOverlay" role="dialog" aria-modal="true" aria-labelledby="supportTitle">
    <button class="support-dismiss" id="supportDismiss" type="button" aria-label="Lukk"></button>
    <section class="support-sheet">
      <div class="sheet-handle" aria-hidden="true"></div>
      <div class="sheet-head"><div><p class="story-kicker" id="supportLevel"></p><h2 id="supportTitle">Norsk støtte</h2></div><button class="sheet-close" id="sheetClose" type="button" aria-label="Lukk">×</button></div>
      <div class="support-original"><p id="supportOriginal"></p><button class="sheet-play" id="sheetPlay" type="button" aria-label="Dhegeyso"></button></div>
      <div class="support-translation"><small>NORSK</small><p id="supportNorwegian"></p></div>
      <div class="support-note hidden" id="supportNote"></div>
      <div class="word-section"><h3 id="wordHeading">Taabo eray</h3><div class="word-chips" id="wordChips"></div><p class="word-result hidden" id="wordResult"></p></div>
    </section>
  </div>

  <script>window.GOOGA_STORY_PREVIEW=<?= !$isPublic ? 'true' : 'false' ?>;window.GOOGA_PWA_UPDATE={version:<?= json_encode($assetVersion) ?>,versionUrl:'./version.php',reloadUrl:'./stories.php'};</script>
  <script src="<?= htmlspecialchars(googa_asset_url('assets/read-aloud.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('story-bank.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('stories.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="<?= htmlspecialchars(googa_asset_url('pwa-update.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
