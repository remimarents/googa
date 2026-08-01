<?php
declare(strict_types=1);

session_name('googa');
session_start();

require_once __DIR__ . '/lib/store.php';
require_once __DIR__ . '/lib/version.php';

$context = googa_session_context();
if (($context['email'] ?? '') === '') {
    header('Location: ./');
    exit;
}

$user = is_array($context['user'] ?? null) ? $context['user'] : [];
$discount = (int)($user['discount_percent'] ?? 0);
$access = $context['access'] ?? ['label' => 'No active access'];
?>
<!doctype html>
<html lang="so">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Googa – marin la sugayo</title>
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
<style>
.gate{max-width:760px;margin:7vh auto;background:#fffdf7;border-radius:32px;padding:30px;box-shadow:0 18px 45px #10365422}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin:24px 0}
.card{padding:18px;border-radius:20px;background:#e5f7f7;font-weight:800}
.card b{display:block;font:800 31px 'Baloo 2'}
.cta{display:inline-block;border:0;border-radius:16px;background:#0b8691;color:#fff;padding:15px 22px;font-weight:800;font-size:17px;text-decoration:none}
.sub{color:#5b7185}
.meta{margin-top:16px;padding:12px 14px;border-radius:16px;background:#fff5cb;font-weight:800}
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}
.ghost{display:inline-block;border-radius:16px;background:#103654;color:#fff;padding:15px 22px;font-weight:800;text-decoration:none}
@media (max-width:700px){.grid{grid-template-columns:1fr}}
</style>
<main class="gate">
  <img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa" width="150">
  <p class="eyebrow">GOOGA</p>
  <h1>Akoonkaaga waa la aqoonsaday</h1>
  <p class="sub"><?= htmlspecialchars((string)$context['email'], ENT_QUOTES, 'UTF-8') ?></p>
  <p>
    Waad soo gashay, laakiin koontadan weli ma leh marin firfircoon gudaha Googa.
    Tani waa tallaabada saxda ah ee ku xigta marka QR-gelitaanku shaqeeyo, laakiin tijaabada ama rukhsadda bishii aan weli laga hawlgelin koontadan.
  </p>
  <div class="grid">
    <div class="card"><span>Tijaabo</span><b>kr 5</b><small>2 maalmood</small></div>
    <div class="card"><span>Rukhsad</span><b>kr 50</b><small>bishii</small></div>
  </div>
  <p>
    Marka tijaabo ama rukhsad lagu furo koontadaada, waxaad si toos ah u geli doontaa app-ka.
    Bare Af-Soomaali ah ayaa si joogto ah ugu dari doona halxiraalo cusub, ciyaaro cusub iyo waxyaabo kale si app-ku bil kasta u yeesho wax cusub.
  </p>
  <p class="meta">
    Xaaladda hadda: <?= htmlspecialchars((string)($access['label'] ?? 'No active access'), ENT_QUOTES, 'UTF-8') ?>
    <?php if ($discount > 0): ?> · Qiimo-dhimis: <?= $discount ?>%<?php endif; ?>
  </p>
  <div class="actions">
    <a class="cta" href="./">Dib u hubi marin</a>
    <a class="ghost" href="./?logout=1">Ka bax</a>
  </div>
</main>
</html>
