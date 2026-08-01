<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/../lib/store.php';
require_once __DIR__ . '/../lib/version.php';

$context = googa_session_context();
if (empty($context['authenticated'])) {
    header('Location: ../');
    exit;
}
$user = is_array($context['user'] ?? null) ? $context['user'] : [];
$premium = googa_has_ordreise_full_access($user);
$subscriptionActive = googa_has_active_googa_subscription($user);
$freeLive = googa_ordreise_free_is_live();
$monthsSincePurchase = googa_ordreise_months_since_purchase($user);
if (empty($_SESSION['googa_payment_csrf'])) {
    $_SESSION['googa_payment_csrf'] = bin2hex(random_bytes(24));
}
?>
<!doctype html>
<html lang="no"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><title>Ordreise på af-soomaali</title>
<link rel="stylesheet" href="../styles.css?v=<?= rawurlencode(googa_app_version()) ?>">
<style>
body{min-height:100vh;padding:18px;background:linear-gradient(145deg,#e9f6ff,#fff9df);color:#103654;font-family:Nunito,system-ui,sans-serif}.help{width:min(100%,650px);margin:auto;padding:28px;border:1px solid #fff;border-radius:28px;background:#fffdf8;box-shadow:0 24px 70px #10365420}.eyebrow{margin:0;color:#1767ad;font-size:12px;font-weight:900;letter-spacing:.12em}.help h1{margin:7px 0 12px;font:800 clamp(31px,8vw,46px)/1 'Baloo 2',Nunito,sans-serif}.lead{font-size:17px;line-height:1.5}.card{margin:16px 0;padding:18px;border:1px solid #b9d8f5;border-radius:20px;background:linear-gradient(135deg,#edf7ff,#d9edff)}.card h2{margin:0 0 8px;font:800 23px 'Baloo 2',Nunito,sans-serif;color:#145ca6}.card p{margin:8px 0;line-height:1.45}.price{font:900 32px 'Baloo 2',Nunito,sans-serif;color:#145ca6}.cta,.back{display:flex;align-items:center;justify-content:center;width:100%;min-height:52px;margin-top:14px;border:0;border-radius:15px;background:#4189dd;color:#fff;font:900 16px Nunito,system-ui,sans-serif;text-decoration:none;cursor:pointer}.back{background:#103654}.note{padding:13px 15px;border-radius:15px;background:#fff1b9;font-size:14px;font-weight:800;line-height:1.45}.status{padding:12px 14px;border-radius:14px;background:#dff7e7;color:#155b36;font-weight:900}.small{color:#536f84;font-size:13px;line-height:1.45}
</style>
<main class="help"><p class="eyebrow">GOOGA · ORDREISE</p><h1>Ordreise på af-soomaali</h1>
<?php if ($freeLive): ?><p class="lead">De første 10 brettene er gratis. Du kan starte reisen nå.</p><?php else: ?><p class="lead">Fra neste måned kan du spille de første 10 brettene helt gratis – uten kjøp.</p><?php endif; ?>
<section class="card"><h2>Få hele Ordreise nå</h2><p>Med et aktivt Googa-abonnement kan du låse opp alle 1&nbsp;001 brettene allerede nå, uten å vente på gratisåpningen.</p><div class="price">59 kr <small>én gang</small></div><p class="small">Kjøpet er knyttet til Googa-kontoen din og fungerer mens Googa-abonnementet er aktivt. Sier du opp, blir Ordreise-pakken låst igjen; framgangen din blir liggende til abonnementet aktiveres på nytt.</p><?php if ($premium): ?><p class="status">Full tilgang er aktiv. Du er på måned <?= $monthsSincePurchase + 1 ?> av din Ordreise-reise.</p><a class="cta" href="./">Åpne Ordreise</a><?php elseif ($subscriptionActive): ?><form method="post" action="../checkout.php"><input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_payment_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="kind" value="ordreise_lifetime"><button class="cta" type="submit">Lås opp alle brett · 59 kr</button></form><?php else: ?><p class="note">Full tilgang kan kjøpes når Googa-abonnementet er aktivt.</p><a class="cta" href="../">Start eller aktiver Googa-abonnement</a><?php endif; ?></section>
<section class="card"><h2>Nytt innhold følger deg</h2><p>Alle de 1&nbsp;001 brettene er tilgjengelige med én gang. Når vi legger til nytt månedsinnhold, regnes det fra dagen du kjøpte Ordreise – ikke fra en fast kalenderdato.</p></section><a class="back" href="../">Tilbake til Googa</a></main>
</html>
