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
if (empty($_SESSION['googa_payment_csrf'])) {
    $_SESSION['googa_payment_csrf'] = bin2hex(random_bytes(24));
}
$paymentCsrf = $_SESSION['googa_payment_csrf'];
$paymentState = (string)($_GET['payment'] ?? '');
$choice = (string)($_GET['choice'] ?? '');
if (!in_array($choice, ['trial', 'monthly'], true)) {
    $choice = '';
}
$autoCheckout = $choice !== '' && hash_equals((string)($_SESSION['googa_checkout_choice'] ?? ''), $choice);
$showLogout = googa_normalize_email((string)($context['email'] ?? '')) === GOOGA_PRIVATE_LOGOUT_EMAIL;
if ($autoCheckout) {
    unset($_SESSION['googa_checkout_choice']);
}
?>
<!doctype html>
<html lang="so">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Googa – marin la sugayo</title>
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
<style>
.gate{width:min(100% - 28px,760px);margin:clamp(14px,5vh,44px) auto;background:#fffdf7;border-radius:30px;padding:clamp(20px,4vw,30px);box-shadow:var(--shadow)}
.gate>img{display:block;width:112px;height:112px;object-fit:contain}.gate h1{margin:2px 0 4px;font:800 clamp(34px,7vw,48px)/1.02 'Baloo 2',Nunito,system-ui,sans-serif}.gate>p{line-height:1.45}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin:24px 0}
.card{padding:18px;border-radius:20px;background:#e5f7f7;font-weight:800}
.card b{display:block;font:800 31px 'Baloo 2',Nunito,system-ui,sans-serif}
.cta{display:inline-flex;align-items:center;justify-content:center;min-height:48px;border:0;border-radius:16px;background:#087f89;color:#fff;padding:13px 18px;font-weight:800;font-size:16px;text-decoration:none;cursor:pointer}
.sub{color:#5b7185}
.meta{margin-top:16px;padding:12px 14px;border-radius:16px;background:#fff5cb;font-weight:800}
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}
.actions form{margin:0}
.ghost{display:inline-flex;align-items:center;justify-content:center;min-height:48px;border-radius:16px;background:#103654;color:#fff;padding:13px 18px;font-weight:800;text-decoration:none}
@media (max-width:700px){.gate{width:min(100% - 20px,760px);margin:10px auto;border-radius:24px;padding:17px 16px}.gate>img{width:82px;height:82px;margin:auto}.gate .eyebrow{text-align:center}.gate h1{text-align:center;font-size:34px}.gate>.sub{text-align:center;margin-top:5px}.gate>p{font-size:14px}.grid{grid-template-columns:1fr 1fr;gap:9px;margin:15px 0}.card{padding:12px 10px}.card b{font-size:27px}.actions{display:grid;grid-template-columns:1fr;margin-top:14px}.actions form,.actions button,.actions a{width:100%}.meta{font-size:13px}}
</style>
<style>
body{padding:max(10px,env(safe-area-inset-top)) 0 max(10px,env(safe-area-inset-bottom))}.gate{position:relative;isolation:isolate;overflow:hidden;border:1px solid #ffffffcf;background:linear-gradient(145deg,#fffef9,#fff9e7 62%,#d7f3f3 135%);box-shadow:0 28px 70px #10365420}.gate:before{content:"";position:absolute;z-index:-1;width:270px;height:270px;right:-160px;top:-160px;border:48px solid #07858d10;border-radius:50%}.gate>img{margin:-6px 0 -9px;filter:drop-shadow(0 10px 10px #10365420)}.gate>.eyebrow{width:max-content;padding:5px 10px;border-radius:999px;background:#e5f7f7;letter-spacing:.12em}.gate h1{letter-spacing:-.035em;text-wrap:balance}.grid{gap:13px}.card{position:relative;overflow:hidden;min-height:150px;padding:19px;border:1px solid #fff;border-radius:23px;background:linear-gradient(145deg,#fff8d7,#ffe69a);box-shadow:0 12px 25px #10365414,inset 0 1px 0 #fff}.card+ .card{background:linear-gradient(145deg,#e9ffff,#c3eeec)}.card b{font-size:36px}.card small{display:block;line-height:1.35}.meta{border:1px solid #f0db86;background:linear-gradient(145deg,#fff9df,#fff0af)}.actions{display:grid;grid-template-columns:1fr 1fr}.actions form{display:flex}.actions form .cta{width:100%}.cta,.ghost{box-shadow:0 7px 18px #087f8924;transition:transform .16s,box-shadow .16s}.cta:hover,.ghost:hover{transform:translateY(-2px);box-shadow:0 11px 24px #10365427}.actions>a{grid-column:span 1}.actions .ghost{background:#103654}
@media(max-width:700px){body{padding-top:4px}.gate{min-height:calc(100dvh - 8px);margin:0 auto;border-radius:25px}.gate>img{margin:-6px auto -8px}.gate>.eyebrow{margin-inline:auto}.grid{grid-template-columns:1fr 1fr}.card{min-height:129px;padding:13px 11px;border-radius:19px}.actions{grid-template-columns:1fr}.actions>a{grid-column:auto}.gate>p{line-height:1.42}}
</style>
<main class="gate">
  <img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa" width="150">
  <p class="eyebrow">GOOGA</p>
  <h1 data-speak-so="Akoonkaaga waa la aqoonsaday." data-speak-audio="audio/ui/access-title.mp3">Akoonkaaga waa la aqoonsaday</h1>
  <p class="sub"><?= htmlspecialchars((string)$context['email'], ENT_QUOTES, 'UTF-8') ?></p>
  <p data-speak-so="Waad soo gashay, laakiin koontadan weli ma leh marin firfircoon gudaha Googa. Dooro tijaabada ama rukhsadda bishii si aad u bilowdo." data-speak-audio="audio/ui/access-intro.mp3">
    Waad soo gashay, laakiin koontadan weli ma leh marin firfircoon gudaha Googa.
    Dooro tijaabada ama rukhsadda bishii si aad u bilowdo.
  </p>
  <div class="grid">
    <div class="card" data-speak-so="Tijaabada Googa. Shan karoon laba maalmood, ka dib konton karoon bishii." data-speak-audio="audio/ui/plan-trial.mp3"><span>Tijaabo</span><b>kr 5</b><small>2 maalmood, ka dib kr 50 bishii</small></div>
    <div class="card" data-speak-so="Rukhsadda Googa. Konton karoon bishii." data-speak-audio="audio/ui/plan-monthly.mp3"><span>Rukhsad</span><b>kr 50</b><small>bishii</small></div>
  </div>
  <p data-speak-so="Tijaabadu waxay ku kacaysaa shan karoon hadda, ka dibna rukhsaddu waa konton karoon bishii ilaa aad joojiso. Marka lacag-bixinta la xaqiijiyo, waxaad si toos ah u geli doontaa app-ka." data-speak-audio="audio/ui/access-offer.mp3">
    Tijaabadu waxay ku kacaysaa kr 5 hadda, ka dibna rukhsaddu waa kr 50 bishii ilaa aad joojiso.
    Marka lacag-bixinta la xaqiijiyo, waxaad si toos ah u geli doontaa app-ka.
    Bare Af-Soomaali ah ayaa si joogto ah ugu dari doona halxiraalo cusub, ciyaaro cusub iyo waxyaabo kale si app-ku bil kasta u yeesho wax cusub.
  </p>
  <p class="meta">
    Xaaladda hadda: <?= htmlspecialchars((string)($access['label'] ?? 'No active access'), ENT_QUOTES, 'UTF-8') ?>
    <?php if ($discount > 0): ?> · Qiimo-dhimis: <?= $discount ?>%<?php endif; ?>
  </p>
  <div class="actions">
    <form method="post" action="checkout.php" id="trialCheckout">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($paymentCsrf, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="kind" value="trial">
      <button class="cta" type="submit">Ku bilow tijaabada · kr 5</button>
    </form>
    <form method="post" action="checkout.php" id="monthlyCheckout">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($paymentCsrf, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="kind" value="monthly">
      <button class="cta" type="submit">Ku bilow rukhsadda · kr 50/bishii</button>
    </form>
    <a class="cta" href="./">Dib u hubi marin</a>
    <?php if ($showLogout): ?><a class="ghost" href="./?logout=1">Ka bax</a><?php endif; ?>
  </div>
  <?php if ($paymentState === 'cancelled'): ?><p class="meta" data-speak-so="Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin." data-speak-audio="audio/ui/payment-cancelled.mp3">Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin.</p><?php endif; ?>
  <?php if ($paymentState === 'error'): ?><p class="meta" data-speak-so="Lacag-bixinta lama bilaabi karin hadda. Fadlan mar kale isku day." data-speak-audio="audio/ui/payment-error.mp3">Lacag-bixinta lama bilaabi karin hadda. Fadlan mar kale isku day.</p><?php endif; ?>
  <?php if ($paymentState === 'processing'): ?><p class="meta" data-speak-so="Lacag-bixinta waa la xaqiijinayaa. Dib u hubi marin daqiiqad yar gudahood." data-speak-audio="audio/ui/payment-processing.mp3">Lacag-bixinta waa la xaqiijinayaa. Dib u hubi marin daqiiqad yar gudahood.</p><?php endif; ?>
  <?php if ($paymentState === 'portal-error'): ?><p class="meta" data-speak-so="Maareynta rukhsadda lama furi karin hadda." data-speak-audio="audio/ui/portal-error.mp3">Maareynta rukhsadda lama furi karin hadda.</p><?php endif; ?>
</main>
<script src="<?= htmlspecialchars(googa_asset_url('assets/read-aloud.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php if ($autoCheckout): ?><script>document.getElementById(<?= json_encode($choice === 'trial' ? 'trialCheckout' : 'monthlyCheckout') ?>).requestSubmit();</script><?php endif; ?>
</html>
