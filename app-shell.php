<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/version.php';

$assetVersion = googa_app_version();
$portalAvailable = !empty($context['user']['stripe']['customer_id']);
$familyAvailable = empty($context['family_device']) && !empty($context['access']['allowed']);
?>
<!doctype html>
<html lang="so">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#103654" />
    <meta name="description" content="Googa – somaliske gåter for nysgjerrige hoder." />
    <link rel="manifest" href="<?= htmlspecialchars(googa_asset_url('manifest.php'), ENT_QUOTES, 'UTF-8') ?>" />
    <link rel="icon" href="<?= htmlspecialchars(googa_asset_url('assets/icon-192.png'), ENT_QUOTES, 'UTF-8') ?>" />
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
        <div class="top-actions"><button class="update-link hidden" id="updateButton" type="button">Cusboonaysii</button><a class="owner-link hidden" id="ownerLink" href="owner.php">Owner</a><?php if ($familyAvailable): ?><a class="owner-link" href="family.php">Qoyska</a><?php endif; ?><?php if ($portalAvailable && empty($context['family_device'])): ?><a class="owner-link" href="portal.php">Maaree rukhsad</a><?php endif; ?><span class="stars" id="stars">✦ 0</span><button class="language" id="languageToggle" title="Vis tekst på norsk" aria-label="Vis tekst på norsk">🇳🇴</button></div>
      </header>

      <section id="welcome" class="welcome">
        <div class="welcome-copy">
          <p class="eyebrow" data-so="Ciyaar. Ogaansho. Af-Soomaali." data-no="Lek. Oppdagelse. Somali.">Ciyaar. Ogaansho. Af-Soomaali.</p>
          <h1 data-so="Maskaxda yar, sir weyn!" data-no="Lite hode, store mysterier!">Maskaxda yar, sir weyn!</h1>
          <p class="intro" data-so="Dooro da'daada. Googa wuxuu kuu hayaa halxiraale maanta." data-no="Velg alderen din. Googa har en gåte klar til deg i dag.">Dooro da'daada. Googa wuxuu kuu hayaa halxiraale maanta.</p>
          <p class="build-note" id="buildNote">v<?= htmlspecialchars($assetVersion, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <img class="hero-mascot" src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa" />
        <div class="age-grid" id="ageGrid"></div>
      </section>

      <section id="game" class="game hidden">
        <button class="back" id="backButton">← <span data-so="Kooxaha da'da" data-no="Aldersgrupper">Kooxaha da'da</span></button>
        <div class="progress-row"><span id="ageLabel"></span><span id="progress"></span></div>
        <div class="progress-track"><i id="progressFill"></i></div>
        <article class="riddle-card">
          <div class="riddle-top"><span class="case-tag" id="caseTag"></span><button class="speak" id="speakButton" aria-label="Les gåten høyt">🔊</button></div>
          <div id="riddleVisual" class="riddle-visual" aria-hidden="true"></div>
          <p class="listen-hint" data-so="Dhegeyso si fiican" data-no="Lytt godt">Dhegeyso si fiican</p>
          <h2 id="question"></h2>
          <p id="norwegianQuestion" class="norwegian-question hidden"></p>
          <div id="options" class="options"></div>
          <div id="feedback" class="feedback hidden"></div>
        </article>
      </section>
    </main>
    <button class="logout-control" id="logoutDot" type="button" data-so="Ka bax" data-no="Logg ut" aria-label="Logg ut" title="Trykk tre ganger for å logge ut">Ka bax</button>
    <style>.logout-control{position:fixed;left:50%;bottom:8px;z-index:5;transform:translateX(-50%);border:1px solid #10365430;border-radius:999px;background:#fffdf7;color:#103654;padding:5px 12px;font:800 12px system-ui;box-shadow:0 2px 8px #10365418;cursor:pointer;opacity:.8}.logout-control:hover,.logout-control:focus-visible{opacity:1;outline:2px solid #0b8691;outline-offset:2px}</style>
    <script src="<?= htmlspecialchars(googa_asset_url('session.php'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(googa_asset_url('bank.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(googa_asset_url('app.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
  </body>
</html>
