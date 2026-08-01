<?php
declare(strict_types=1);
session_name('googa');
session_start();
require_once __DIR__ . '/lib/version.php';
if (empty($_SESSION['googa_payment_csrf'])) {
    $_SESSION['googa_payment_csrf'] = bin2hex(random_bytes(24));
}
$paymentState = (string)($_GET['payment'] ?? '');
?><!doctype html>
<html lang="so">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#103654">
  <title>Googa – hadiyad sii qoys</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@700;800&family=Nunito:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
  <style>
  body{margin:0;padding:16px;background:linear-gradient(145deg,#f2fbfa,#fff6d9);font-family:Nunito,sans-serif;color:#103654}.gift-shell{width:min(760px,100%);margin:auto}.gift-head,.gift-form{border:1px solid #fff;border-radius:28px;background:#fffdf7;box-shadow:0 22px 55px #10365418}.gift-head{position:relative;overflow:hidden;padding:28px;text-align:center}.gift-head img{width:92px}.gift-head h1{margin:4px 0;font:800 clamp(34px,7vw,52px)/1 'Baloo 2',sans-serif}.gift-head p{max-width:580px;margin:10px auto;color:#587085;line-height:1.5}.gift-form{margin-top:14px;padding:22px}.fields{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field{display:grid;gap:6px}.field label{font-weight:900}.field input{min-height:50px;border:1px solid #cfdee2;border-radius:14px;padding:10px 12px;font:inherit}.choices{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:17px 0}.choice{position:relative}.choice input{position:absolute;opacity:0}.choice span{display:grid;place-items:center;min-height:112px;border:2px solid #d9e8e8;border-radius:18px;background:#f2fbfa;font-weight:900;text-align:center}.choice b{font:800 28px 'Baloo 2'}.choice input:checked+span{border-color:#087f89;background:#dff5f3;box-shadow:0 8px 20px #087f8920}.buy{width:100%;min-height:56px;border:0;border-radius:16px;background:#087f89;color:#fff;font:900 17px Nunito}.fine{font-size:12px;color:#687c8c;text-align:center}.back{display:inline-flex;margin:14px;color:#103654;font-weight:900;text-decoration:none}.message{padding:12px;border-radius:14px;background:#ffe1df;font-weight:800}.lang{position:absolute;right:18px;top:18px;width:45px;height:45px;border:0;border-radius:14px;background:#eef8f7;font-size:20px}@media(max-width:600px){body{padding:8px}.gift-head,.gift-form{border-radius:22px;padding:17px}.fields,.choices{grid-template-columns:1fr}.choice span{min-height:76px;display:flex;justify-content:space-between;padding:13px 18px}}
  </style>
</head><body><main class="gift-shell">
  <section class="gift-head"><button class="lang" id="lang" type="button">🇳🇴</button><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt=""><p><b>GOOGA HADIYAD</b></p><h1 data-so="Hadiyad sii qoys" data-no="Gi Googa til en familie">Hadiyad sii qoys</h1><p data-so="Bixi hal mar. Qofka aad doorato wuxuu helayaa Googa muddada oo dhan, mana jiro rukhsad si toos ah u cusboonaanaysa." data-no="Betal én gang. Mottakeren får Googa i hele perioden, uten automatisk fornyelse.">Bixi hal mar. Qofka aad doorato wuxuu helayaa Googa muddada oo dhan, mana jiro rukhsad si toos ah u cusboonaanaysa.</p></section>
  <?php if ($paymentState === 'cancelled'): ?><p class="message">Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin.</p><?php endif; ?>
  <?php if ($paymentState === 'success'): ?><p class="message" style="background:#dff5e5">Hadiyadda waa la diray! Qaataha wuxuu helayaa e-mail uu ku sameeyo furaha Googa.</p><?php endif; ?>
  <form class="gift-form" method="post" action="checkout.php"><input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_payment_csrf'], ENT_QUOTES, 'UTF-8') ?>">
    <div class="fields"><div class="field"><label for="recipient_name" data-so="Magaca qaataha" data-no="Mottakerens navn">Magaca qaataha</label><input id="recipient_name" name="recipient_name" autocomplete="name" required></div><div class="field"><label for="recipient_email" data-so="E-mailka qaataha" data-no="Mottakerens e-post">E-mailka qaataha</label><input id="recipient_email" name="recipient_email" type="email" autocomplete="email" required></div></div>
    <div class="choices"><label class="choice"><input type="radio" name="kind" value="gift_3" required><span><i>3 bilood</i><b>kr 149</b></span></label><label class="choice"><input type="radio" name="kind" value="gift_6"><span><i>6 bilood</i><b>kr 279</b></span></label><label class="choice"><input type="radio" name="kind" value="gift_12" checked><span><i>12 bilood</i><b>kr 499</b></span></label></div>
    <button class="buy" type="submit" data-so="Sii hadiyadda →" data-no="Gå til sikker betaling →">Sii hadiyadda →</button><p class="fine" data-so="Stripe ayaa lacag-bixinta si ammaan ah u maamusha. Qaataha waxaa loo sameeyaa ama loo kordhiyaa gelitaanka Googa." data-no="Stripe håndterer betalingen sikkert. Mottakerens Googa-tilgang opprettes eller forlenges.">Stripe ayaa lacag-bixinta si ammaan ah u maamusha.</p>
  </form><a class="back" href="./">← Googa</a>
</main><script>let l='so';document.getElementById('lang').onclick=()=>{l=l==='so'?'no':'so';document.documentElement.lang=l;document.getElementById('lang').textContent=l==='so'?'🇳🇴':'🇸🇴';document.querySelectorAll('[data-so]').forEach(e=>e.textContent=e.dataset[l])}</script></body></html>
