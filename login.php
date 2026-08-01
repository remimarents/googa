<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/version.php';
require_once __DIR__ . '/lib/mail.php';

if (empty($_SESSION['googa_login_csrf'])) {
    $_SESSION['googa_login_csrf'] = bin2hex(random_bytes(24));
}
if (empty($_SESSION['googa_payment_csrf'])) {
    $_SESSION['googa_payment_csrf'] = bin2hex(random_bytes(24));
}
$notice = '';
$error = '';
$paymentState = (string)($_GET['payment'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = (string)($_POST['csrf'] ?? '');
    if (!hash_equals((string)$_SESSION['googa_login_csrf'], $csrf)) {
        $error = 'Codsiga lama xaqiijin. Fadlan mar kale isku day.';
    } else {
        $action = (string)($_POST['action'] ?? '');
        $email = googa_normalize_email((string)($_POST['email'] ?? ''));
        $data = googa_load_data();
        $user = isset($data['users'][$email]) && is_array($data['users'][$email]) ? $data['users'][$email] : null;
        if ($action === 'login') {
            if (is_array($user) && googa_verify_password($user, (string)($_POST['password'] ?? ''))) {
                googa_login_user($user);
                header('Location: ./', true, 303);
                exit;
            }
            $error = 'E-mailka ama furaha sirta ahi sax ma aha.';
        } elseif ($action === 'reset') {
            if (is_array($user)) {
                $token = googa_create_password_token($data, $email);
                if (is_string($token)) {
                    googa_save_data($data);
                    if (!googa_send_password_email($email, $token)) {
                        error_log('Googa password email could not be sent for ' . $email);
                    }
                }
            }
            $notice = 'Haddii e-mailkaasi ku jiro Googa, waxaan kuu soo dirnay xiriir aad ku samaysato furaha cusub.';
        }
    }
}
?>
<!doctype html>
<html lang="so">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#103654">
<title>Googa – halxiraalo Af-Soomaali ah</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
<style>
.landing{position:relative;width:min(100% - 24px,760px);margin:12px auto;background:#fffdf7;border-radius:28px;padding:20px;box-shadow:var(--shadow)}.lang{position:absolute;right:14px;top:14px;width:44px;height:44px;border:1px solid #cfe0e5;border-radius:50%;background:#fff;font-size:21px;cursor:pointer}.brand{display:flex;align-items:center;justify-content:center;gap:5px}.brand img{width:90px;height:90px;object-fit:contain}.brand b{font:800 22px 'Baloo 2',sans-serif;color:#087f89}.landing h1{max-width:610px;margin:-2px auto 7px;text-align:center;font:800 clamp(32px,7vw,46px)/1.02 'Baloo 2',sans-serif;color:#103654}.lead{max-width:570px;margin:0 auto 16px;text-align:center;color:#536b7e}.plans{display:grid;grid-template-columns:1fr 1fr;gap:11px}.plans form{margin:0}.plan{width:100%;min-height:122px;border:2px solid #cae7e7;border-radius:19px;background:#e9f9f9;color:#103654;padding:12px;font-weight:800;cursor:pointer}.plan:hover,.plan:focus-visible{border-color:#087f89;transform:translateY(-2px)}.plan span,.plan small{display:block}.plan b{display:block;font:800 31px 'Baloo 2',sans-serif}.plan em{display:inline-block;margin-top:5px;padding:4px 10px;border-radius:99px;background:#087f89;color:#fff;font-size:12px;font-style:normal}.divider{display:flex;align-items:center;gap:10px;margin:17px 0 13px;color:#6a7e8d;font-size:13px}.divider:before,.divider:after{content:"";height:1px;flex:1;background:#dbe7e9}.login-grid{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:end}.field{display:grid;gap:5px;text-align:left}.field label{font-size:13px;font-weight:800}.field input{width:100%;min-height:46px;border:2px solid #d3e2e5;border-radius:13px;padding:10px 12px;font:inherit;background:#fff}.fields{display:grid;grid-template-columns:1fr 1fr;gap:9px}.primary{min-height:46px;border:0;border-radius:13px;background:#103654;color:#fff;padding:11px 18px;font:800 15px Nunito,sans-serif;cursor:pointer}.reset-row{margin:10px 0 0;text-align:center}.reset-button{border:0;background:transparent;color:#087f89;text-decoration:underline;font:800 14px Nunito,sans-serif;cursor:pointer}.reset-box{display:none;margin-top:11px;padding:12px;border-radius:15px;background:#f1f8f8}.reset-box.open{display:block}.reset-form{display:grid;grid-template-columns:1fr auto;gap:8px}.message{margin:12px 0 0;padding:10px 12px;border-radius:13px;background:#dff5e5;font-weight:800;font-size:14px}.message.error{background:#ffe1df}.terms{margin:13px 0 0;text-align:center;color:#607687;font-size:12px;line-height:1.4}
@media(max-width:600px){.landing{width:min(100% - 14px,760px);margin:5px auto;padding:12px 13px 14px;border-radius:22px}.brand img{width:72px;height:72px}.brand b{font-size:19px}.lang{right:8px;top:8px}.landing h1{font-size:clamp(29px,9vw,37px)}.lead{font-size:14px;margin-bottom:11px}.plans{gap:8px}.plan{min-height:112px;padding:8px 5px}.plan b{font-size:27px}.divider{margin:12px 0 9px}.fields{grid-template-columns:1fr}.login-grid{grid-template-columns:1fr}.primary{width:100%}.reset-form{grid-template-columns:1fr}.terms{margin-top:9px}}
</style>
<style>.plans form{position:relative}.plans form>.read-aloud{position:absolute;right:8px;top:8px;z-index:2;margin:0}.plan{padding-right:42px}@media(max-width:600px){.plan{padding-right:38px}}</style>
<main class="landing">
  <button class="lang" id="lang" type="button" aria-label="Vis norsk tekst">🇳🇴</button>
  <div class="brand"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa"><b>GOOGA</b></div>
  <h1 data-so="Halxiraalo Af-Soomaali ah.<br>Waqtiyo yar oo wadajir ah." data-no="Somaliske gåter.<br>Små stunder sammen.">Halxiraalo Af-Soomaali ah.<br>Waqtiyo yar oo wadajir ah.</h1>
  <p class="lead" data-so="Ku baro Af-Soomaaliga halxiraalo, cod iyo ciyaar qoyska oo dhan ah." data-no="Lær somali gjennom gåter, lyd og lek for hele familien." data-speak-so="Halxiraalo Af-Soomaali ah. Waqtiyo yar oo wadajir ah. Ku baro Af-Soomaaliga halxiraalo, cod iyo ciyaar qoyska oo dhan ah." data-speak-audio="audio/ui/login-hero.mp3">Ku baro Af-Soomaaliga halxiraalo, cod iyo ciyaar qoyska oo dhan ah.</p>
  <div class="plans">
    <form method="post" action="checkout.php"><input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_payment_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="kind" value="trial"><button class="plan" type="submit"><span data-so="Marka hore tijaabi" data-no="Prøv først">Marka hore tijaabi</span><b>kr 5</b><small data-so="2 maalmood, ka dib kr 50/bishii" data-no="2 dager, deretter kr 50/mnd">2 maalmood, ka dib kr 50/bishii</small><em data-so="Dooro →" data-no="Velg →">Dooro →</em></button><button class="read-aloud" type="button" data-speak-button data-speak-so="Tijaabada Googa. Shan karoon laba maalmood, ka dib konton karoon bishii." data-speak-audio="audio/ui/plan-trial.mp3" aria-label="Dhegeyso qiimaha tijaabada">🔊</button></form>
    <form method="post" action="checkout.php"><input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_payment_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="kind" value="monthly"><button class="plan" type="submit"><span data-so="Rukhsad bille ah" data-no="Månedsabonnement">Rukhsad bille ah</span><b>kr 50</b><small data-so="bishii" data-no="per måned">bishii</small><em data-so="Dooro →" data-no="Velg →">Dooro →</em></button><button class="read-aloud" type="button" data-speak-button data-speak-so="Rukhsadda Googa. Konton karoon bishii." data-speak-audio="audio/ui/plan-monthly.mp3" aria-label="Dhegeyso qiimaha bisha">🔊</button></form>
  </div>
  <div class="divider" data-so="Hore ayaad xisaab u leedahay?" data-no="Har du allerede en konto?" data-speak-so="Hore ayaad xisaab u leedahay? Geli e-mailkaaga iyo furaha sirta ah." data-speak-audio="audio/ui/login-account.mp3">Hore ayaad xisaab u leedahay?</div>
  <form method="post" class="login-grid">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_login_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="login">
    <div class="fields"><div class="field"><label for="email" data-so="E-mail" data-no="E-post">E-mail</label><input id="email" name="email" type="email" autocomplete="username" required></div><div class="field"><label for="password" data-so="Furaha sirta" data-no="Passord">Furaha sirta</label><input id="password" name="password" type="password" autocomplete="current-password" required></div></div>
    <button class="primary" type="submit" data-so="Soo gal" data-no="Logg inn">Soo gal</button>
  </form>
  <p class="reset-row" data-speak-so="Ma illowday furaha sirta ah? Waxaad samayn kartaa mid cusub." data-speak-audio="audio/ui/login-reset.mp3"><button class="reset-button" id="showReset" type="button" data-so="Ma illowday furaha? Samee mid cusub" data-no="Glemt passord? Opprett et nytt">Ma illowday furaha? Samee mid cusub</button></p>
  <section class="reset-box<?= $notice !== '' ? ' open' : '' ?>" id="resetBox"><form method="post" class="reset-form"><input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_login_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="reset"><div class="field"><label for="resetEmail" data-so="E-mailkaaga" data-no="E-postadressen din">E-mailkaaga</label><input id="resetEmail" name="email" type="email" autocomplete="email" required></div><button class="primary" type="submit" data-so="Soo dir xiriirka" data-no="Send passordlenke">Soo dir xiriirka</button></form></section>
  <?php if ($notice !== ''): ?><p class="message" data-speak-so="<?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <?php if ($error !== ''): ?><p class="message error" data-speak-so="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <?php if ($paymentState === 'cancelled'): ?><p class="message error" data-so="Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin." data-no="Betalingen ble avbrutt. Ingen penger er trukket." data-speak-so="Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin." data-speak-audio="audio/ui/payment-cancelled.mp3">Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin.</p><?php endif; ?>
  <?php if ($paymentState === 'error'): ?><p class="message error" data-so="Lacag-bixinta lama bilaabi karin hadda. Fadlan mar kale isku day." data-no="Betalingen kunne ikke startes nå. Prøv igjen." data-speak-so="Lacag-bixinta lama bilaabi karin hadda. Fadlan mar kale isku day." data-speak-audio="audio/ui/payment-error.mp3">Lacag-bixinta lama bilaabi karin hadda. Fadlan mar kale isku day.</p><?php endif; ?>
  <p class="terms" data-so="Stripe ayaa si ammaan ah u maamusha lacag-bixinta. Carruurtu waxay ku xirmaan QR-ka qoyska kadib marka waalidku galo." data-no="Stripe håndterer betalingen sikkert. Barn kobles til med familie-QR etter at forelderen har logget inn." data-speak-so="Stripe ayaa si ammaan ah u maamusha lacag-bixinta. Carruurtu waxay ku xirmaan QR-ka qoyska kadib marka waalidku galo." data-speak-audio="audio/ui/login-terms.mp3">Stripe ayaa si ammaan ah u maamusha lacag-bixinta. Carruurtu waxay ku xirmaan QR-ka qoyska kadib marka waalidku galo.</p>
</main>
<script src="<?= htmlspecialchars(googa_asset_url('assets/read-aloud.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script>const langButton=document.getElementById('lang');let lang='so';langButton.onclick=()=>{lang=lang==='so'?'no':'so';document.documentElement.lang=lang;langButton.textContent=lang==='so'?'🇳🇴':'🇸🇴';langButton.setAttribute('aria-label',lang==='so'?'Vis norsk tekst':'Muuji Af-Soomaali');document.querySelectorAll('[data-so]').forEach(el=>el.innerHTML=el.dataset[lang]);document.querySelectorAll('[data-so][data-speak-ready]').forEach(el=>delete el.dataset.speakReady);window.GoogaReadAloud?.enhance()};document.getElementById('showReset').onclick=()=>{document.getElementById('resetBox').classList.toggle('open');document.getElementById('resetEmail').focus()};</script>
</html>
