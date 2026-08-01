<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/store.php';

$context = googa_session_context();
googa_require_owner($context);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Googa – velg modus</title>
<link rel="stylesheet" href="styles.css">
<style>
.owner-mode{max-width:780px;margin:8vh auto;background:#fffdf7;border-radius:32px;padding:30px;box-shadow:0 18px 45px #10365422}
.owner-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:22px}
.owner-card{border:0;border-radius:22px;padding:22px;text-align:left;box-shadow:0 9px 0 #10365418;background:#e5f7f7;color:#103654}
.owner-card strong{display:block;font:800 28px 'Baloo 2'}
.owner-card span{display:block;font-weight:800;margin-top:8px}
.owner-card small{display:block;color:#5b7185;margin-top:6px;min-height:44px}
.owner-card.demo{background:#ffe69a}
.owner-card.paid{background:#c7ddff}
.owner-card.dashboard{background:#f6c5db}
@media (max-width:700px){.owner-grid{grid-template-columns:1fr}}
</style>
<main class="owner-mode">
  <p class="eyebrow">OWNER</p>
  <h1>Velg hvordan du vil inn i Googa</h1>
  <p class="muted"><?= htmlspecialchars($context['email'], ENT_QUOTES, 'UTF-8') ?></p>
  <div class="owner-grid">
    <form method="post">
      <input type="hidden" name="mode" value="demo">
      <button class="owner-card demo" type="submit">
        <strong>Demobruker</strong>
        <span>Test onboarding og lett tilgang</span>
        <small>Bruk denne når du vil se demo-opplevelsen slik en prøvebruker møter den.</small>
      </button>
    </form>
    <form method="post">
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

