<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('googa');
    session_start();
}

require_once __DIR__ . '/lib/store.php';
require_once __DIR__ . '/lib/version.php';

$context = googa_session_context();
googa_require_owner($context);

if (empty($_SESSION['googa_owner_mode_csrf'])) {
    $_SESSION['googa_owner_mode_csrf'] = bin2hex(random_bytes(24));
}
$csrf = (string)$_SESSION['googa_owner_mode_csrf'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Ugyldig forespørsel.');
    }
    $mode = (string)($_POST['mode'] ?? '');
    if (in_array($mode, ['demo', 'paid'], true)) {
        $_SESSION['googa_mode'] = $mode;
        header('Location: ./');
        exit;
    }
}
?><!doctype html>
<html lang="no">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Googa – velg modus</title>
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
<style>
.owner-mode{width:min(100% - 28px,780px);margin:clamp(14px,5vh,46px) auto;background:#fffdf7;border-radius:30px;padding:clamp(20px,4vw,30px);box-shadow:var(--shadow)}
.owner-mode h1{margin:2px 0 5px;font:800 clamp(34px,7vw,48px)/1.02 'Baloo 2',Nunito,system-ui,sans-serif;letter-spacing:-.025em}.owner-mode>.muted{margin:0}
.owner-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:22px}
.owner-grid form{margin:0}.owner-card{display:block;width:100%;height:100%;border:0;border-radius:22px;padding:20px;text-align:left;text-decoration:none;box-shadow:0 7px 0 #10365416;background:#e5f7f7;color:#103654}
.owner-card strong{display:block;font:800 27px 'Baloo 2',Nunito,system-ui,sans-serif}
.owner-card span{display:block;font-weight:800;margin-top:8px}
.owner-card small{display:block;color:#5b7185;margin-top:6px;min-height:44px}
.owner-card.demo{background:#ffe69a}
.owner-card.paid{background:#c7ddff}
.owner-card.dashboard{background:#f6c5db}
@media (max-width:700px){.owner-mode{width:min(100% - 20px,780px);margin:6px auto;border-radius:24px;padding:18px 17px}.owner-mode h1{font-size:clamp(32px,9vw,39px)}.owner-grid{grid-template-columns:1fr;gap:9px;margin-top:14px}.owner-card{min-height:0;padding:14px 17px;border-radius:18px}.owner-card strong{font-size:25px}.owner-card span{margin-top:3px}.owner-card small{min-height:0;margin-top:3px;line-height:1.35}}
</style>
<style>
body{padding:max(10px,env(safe-area-inset-top)) 0 max(10px,env(safe-area-inset-bottom))}.owner-mode{position:relative;isolation:isolate;overflow:hidden;border:1px solid #ffffffcf;background:linear-gradient(145deg,#fffef9,#fff9e7 58%,#daf4f4 130%);box-shadow:0 28px 70px #10365420}.owner-mode:before{content:"";position:absolute;z-index:-1;width:280px;height:280px;right:-170px;top:-170px;border:45px solid #07858d10;border-radius:50%}.owner-mode>.eyebrow{display:inline-flex;padding:5px 10px;border-radius:999px;background:#103654;color:#fff;font-size:11px;letter-spacing:.13em}.owner-grid{gap:15px}.owner-card{position:relative;overflow:hidden;min-height:245px;border:1px solid #ffffffd5;border-radius:24px;padding:22px;box-shadow:0 13px 28px #10365416,inset 0 1px 0 #fff;transition:transform .17s,box-shadow .17s}.owner-card:after{content:"→";position:absolute;right:17px;bottom:15px;display:grid;place-items:center;width:34px;height:34px;border-radius:50%;background:#103654;color:#fff;font-size:18px}.owner-card:hover{transform:translateY(-4px);box-shadow:0 19px 36px #10365420}.owner-card strong{font-size:29px;letter-spacing:-.02em}.owner-card small{line-height:1.45;padding-right:20px}.owner-card.demo{background:linear-gradient(145deg,#fff7d1,#ffe081)}.owner-card.paid{background:linear-gradient(145deg,#e6efff,#bdd5ff)}.owner-card.dashboard{background:linear-gradient(145deg,#ffe7f1,#efb4d0)}
@media(max-width:700px){body{padding-top:4px}.owner-mode{min-height:calc(100dvh - 8px);margin:0 auto;border-radius:25px}.owner-grid{gap:10px}.owner-card{min-height:145px;padding:16px 18px}.owner-card:after{right:14px;bottom:13px;width:30px;height:30px}.owner-card small{padding-right:34px}}
</style>
<main class="owner-mode">
  <p class="eyebrow">OWNER</p>
  <h1>Velg hvordan du vil inn i Googa</h1>
  <p class="muted"><?= htmlspecialchars($context['email'], ENT_QUOTES, 'UTF-8') ?></p>
  <div class="owner-grid">
    <form method="post">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="mode" value="demo">
      <button class="owner-card demo" type="submit">
        <strong>Demobruker</strong>
        <span>Test onboarding og lett tilgang</span>
        <small>Bruk denne når du vil se demo-opplevelsen slik en prøvebruker møter den.</small>
      </button>
    </form>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="mode" value="paid">
      <button class="owner-card paid" type="submit">
        <strong>Månedlig bruker</strong>
        <span>Gå rett inn i betalt modus</span>
        <small>Bruk denne når du vil se opplevelsen som betalende bruker.</small>
      </button>
    </form>
    <a class="owner-card dashboard" href="owner.php">
      <strong>Eierdashbord</strong>
      <span>Rabatter, tilgang og brukere</span>
      <small>Åpne administrasjonspanelet for å styre brukere og rabattnivåer.</small>
    </a>
  </div>
</main>
</html>
