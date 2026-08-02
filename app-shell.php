<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/version.php';

$assetVersion = googa_app_version();
$portalAvailable = !empty($context['user']['stripe']['customer_id']);
$familyAvailable = empty($context['family_device']) && !empty($context['access']['allowed']);
$parentAvailable = empty($context['family_device']) && ($context['email'] ?? '') !== '';
$showLogout = googa_normalize_email((string)($context['email'] ?? '')) === GOOGA_PRIVATE_LOGOUT_EMAIL;
$storiesAvailable = GOOGA_STORIES_PUBLIC || ($context['role'] ?? '') === 'owner';
$cultureTestAvailable = ($context['role'] ?? '') === 'owner';
?>
<!doctype html>
<html lang="so">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#103654" />
    <meta name="description" content="Googa – somaliske gåter for nysgjerrige hoder." />
    <link rel="manifest" href="<?= htmlspecialchars(googa_asset_url('manifest.php'), ENT_QUOTES, 'UTF-8') ?>" />
    <link rel="icon" href="<?= htmlspecialchars(googa_asset_url('assets/icon-192.png'), ENT_QUOTES, 'UTF-8') ?>" />
    <link rel="apple-touch-icon" href="<?= htmlspecialchars(googa_asset_url('assets/icon-512.png'), ENT_QUOTES, 'UTF-8') ?>" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet" />
    <title>Googa — ciyaar iyo halxiraale</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>" />
  </head>
  <body>
    <main class="app-shell" aria-live="polite">
      <header class="topbar">
        <button class="brand" id="homeButton" aria-label="Googa home"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa detective" /><span>Googa</span></button>
        <div class="top-actions"><a class="owner-link" href="ordreise/">Erayo</a><button class="update-link hidden" id="updateButton" type="button">Cusboonaysii</button><a class="owner-link hidden" id="ownerLink" href="owner.php">Eier</a><?php if ($familyAvailable): ?><a class="owner-link" href="family.php">Qoyska</a><?php endif; ?><?php if ($parentAvailable): ?><a class="owner-link" href="help.php">Caawimo</a><?php endif; ?><?php if ($portalAvailable && empty($context['family_device'])): ?><a class="owner-link" href="portal.php">Rukhsad</a><?php endif; ?><span class="stars" id="stars">✦ 0</span><button class="language" id="languageToggle" title="Vis tekst på norsk" aria-label="Vis tekst på norsk">🇳🇴</button></div>
      </header>

      <section id="welcome" class="welcome">
        <div class="hero-card">
          <div class="welcome-copy">
            <p class="eyebrow" data-so="Ciyaar · Ogaansho · Af-Soomaali" data-no="Lek · Oppdagelse · Somali">Ciyaar · Ogaansho · Af-Soomaali</p>
            <h1 data-so="Maskaxda yar, sir weyn!" data-no="Lite hode, store mysterier!">Maskaxda yar, sir weyn!</h1>
            <p class="intro" data-so="Dooro da'daada. Googa wuxuu kuu hayaa halxiraale maanta." data-no="Velg alderen din. Googa har en gåte klar til deg i dag." data-speak-so="Ciyaar. Ogaansho. Af-Soomaali. Maskaxda yar, sir weyn! Dooro da'daada. Googa wuxuu kuu hayaa halxiraale maanta." data-speak-audio="audio/ui/welcome.mp3">Dooro da'daada. Googa wuxuu kuu hayaa halxiraale maanta.</p>
            <p class="build-note" id="buildNote">v<?= htmlspecialchars($assetVersion, ENT_QUOTES, 'UTF-8') ?></p>
          </div>
          <div class="hero-art" aria-hidden="true"><span class="hero-orbit"></span><span class="clue clue-one">?</span><span class="clue clue-two">✦</span><img class="hero-mascot" src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="" /></div>
        </div>
        <div class="age-section-head"><div><p class="section-kicker" data-so="DOORO KOOXDA" data-no="VELG GRUPPE">DOORO KOOXDA</p><h2 data-so="Yaa ciyaaraya?" data-no="Hvem skal spille?">Yaa ciyaaraya?</h2></div><span class="daily-pill"><i>✦</i> <span data-so="4 heer" data-no="4 nivåer">4 heer</span></span></div>
        <div class="age-grid" id="ageGrid"></div>
        <?php if ($storiesAvailable): ?>
        <a class="story-launch" href="stories.php">
          <span class="story-launch-art" aria-hidden="true"><img src="<?= htmlspecialchars(googa_asset_url('assets/stories/diin-dawaco.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><img src="<?= htmlspecialchars(googa_asset_url('assets/stories/wiil-waal.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""></span>
          <span class="story-launch-copy"><small><?= GOOGA_STORIES_PUBLIC ? 'SHEEKOOYIN' : 'EIERFORHÅNDSVISNING' ?></small><strong data-so="Sheeko ku baro Af-Soomaaliga" data-no="Lær somali gjennom historier">Sheeko ku baro Af-Soomaaliga</strong><span data-so="Dhegeyso, taabo oo faham — afar sheeko oo da' kasta loo habeeyey." data-no="Lytt, trykk og forstå – fire historier tilpasset hver aldersgruppe.">Dhegeyso, taabo oo faham — afar sheeko oo da' kasta loo habeeyey.</span><b><i data-so="Fur sheekooyinka" data-no="Åpne historiene">Fur sheekooyinka</i> →</b></span>
        </a>
        <?php endif; ?>
        <?php if ($cultureTestAvailable): ?>
        <a class="culture-test-launch" href="culture-test.php">
          <span class="culture-test-launch-art" aria-hidden="true"><i>🇳🇴</i><b>↔</b><i>🇸🇴</i></span>
          <span><small>EIERFORHÅNDSVISNING · VOKSNE</small><strong>Laba dal, hal sheeko</strong><em>Mellom to hjem – ditt norsk-somaliske kulturkompass</em><b>Åpne testen →</b></span>
        </a>
        <?php endif; ?>
      </section>

      <section id="game" class="game hidden">
        <div class="game-toolbar">
          <button class="back" id="backButton"><span aria-hidden="true">←</span> <span data-so="Kooxaha da'da" data-no="Aldersgrupper">Kooxaha da'da</span></button>
          <div class="progress-shell">
            <div class="progress-row"><span id="ageLabel"></span><span id="progress"></span></div>
            <div class="progress-track"><i id="progressFill"></i></div>
          </div>
        </div>
        <article class="riddle-card">
          <div class="riddle-top"><span class="case-tag" id="caseTag"></span><button class="speak" id="speakButton" aria-label="Les gåten høyt"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9v6h4l5 4V5L8 9H4Z"/><path d="M16 9.2a4 4 0 0 1 0 5.6M18.5 6.7a7.5 7.5 0 0 1 0 10.6"/></svg></button></div>
          <div id="riddleVisual" class="riddle-visual" aria-hidden="true"></div>
          <p class="listen-hint" data-so="Dhegeyso si fiican" data-no="Lytt godt">Dhegeyso si fiican</p>
          <h2 id="question"></h2>
          <p id="norwegianQuestion" class="norwegian-question hidden"></p>
          <div id="options" class="options"></div>
          <div id="feedback" class="feedback hidden"></div>
        </article>
      </section>
    </main>
    <?php if ($showLogout): ?><button class="logout-control" id="logoutDot" type="button" data-so="Ka bax" data-no="Logg ut" aria-label="Logg ut" title="Trykk tre ganger for å logge ut">Ka bax</button><?php endif; ?>
    <style>.logout-control{position:fixed;left:50%;bottom:max(8px,env(safe-area-inset-bottom));z-index:8;transform:translateX(-50%);min-width:76px;min-height:38px;border:1px solid #10365430;border-radius:999px;background:#fffdf7;color:#103654;padding:5px 12px;font:800 12px system-ui;box-shadow:0 2px 8px #10365418;cursor:pointer;opacity:.88}.logout-control:hover,.logout-control:focus-visible{opacity:1;outline:3px solid #087f89;outline-offset:2px}</style>
    <script src="<?= htmlspecialchars(googa_asset_url('session.php'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(googa_asset_url('bank.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(googa_asset_url('assets/read-aloud.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(googa_asset_url('app.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  </body>
</html>
