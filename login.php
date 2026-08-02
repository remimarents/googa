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
        if ($action === 'ambassador') {
            $ambassador = googa_find_ambassador_by_code($data, (string)($_POST['ambassador_code'] ?? ''));
            if (is_array($ambassador)) {
                $_SESSION['googa_ambassador_code'] = (string)$ambassador['code'];
                header('Location: ./', true, 303);
                exit;
            }
            unset($_SESSION['googa_ambassador_code']);
            $error = 'Koodhka safiirku ma shaqaynayo hadda.';
        } elseif ($action === 'login') {
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
$referralData = googa_load_data();
$activeAmbassador = googa_find_ambassador_by_code($referralData, (string)($_SESSION['googa_ambassador_code'] ?? ''));
if (!is_array($activeAmbassador)) {
    unset($_SESSION['googa_ambassador_code']);
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
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('login-commerce.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('login-culture.css'), ENT_QUOTES, 'UTF-8') ?>">
<style>
.landing{position:relative;width:min(100% - 24px,760px);margin:12px auto;background:#fffdf7;border-radius:28px;padding:20px;box-shadow:var(--shadow)}.lang{position:absolute;right:14px;top:14px;width:44px;height:44px;border:1px solid #cfe0e5;border-radius:50%;background:#fff;font-size:21px;cursor:pointer}.brand{display:flex;align-items:center;justify-content:center;gap:5px}.brand img{width:90px;height:90px;object-fit:contain}.brand b{font:800 22px 'Baloo 2',sans-serif;color:#087f89}.landing h1{max-width:610px;margin:-2px auto 7px;text-align:center;font:800 clamp(32px,7vw,46px)/1.02 'Baloo 2',sans-serif;color:#103654}.lead{max-width:570px;margin:0 auto 16px;text-align:center;color:#536b7e}.plans{display:grid;grid-template-columns:1fr 1fr;gap:11px}.plans form{margin:0}.plan{width:100%;min-height:122px;border:2px solid #cae7e7;border-radius:19px;background:#e9f9f9;color:#103654;padding:12px;font-weight:800;cursor:pointer}.plan:hover,.plan:focus-visible{border-color:#087f89;transform:translateY(-2px)}.plan span,.plan small{display:block}.plan b{display:block;font:800 31px 'Baloo 2',sans-serif}.plan em{display:inline-block;margin-top:5px;padding:4px 10px;border-radius:99px;background:#087f89;color:#fff;font-size:12px;font-style:normal}.divider{display:flex;align-items:center;gap:10px;margin:17px 0 13px;color:#6a7e8d;font-size:13px}.divider:before,.divider:after{content:"";height:1px;flex:1;background:#dbe7e9}.login-grid{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:end}.field{display:grid;gap:5px;text-align:left}.field label{font-size:13px;font-weight:800}.field input{width:100%;min-height:46px;border:2px solid #d3e2e5;border-radius:13px;padding:10px 12px;font:inherit;background:#fff}.fields{display:grid;grid-template-columns:1fr 1fr;gap:9px}.primary{min-height:46px;border:0;border-radius:13px;background:#103654;color:#fff;padding:11px 18px;font:800 15px Nunito,sans-serif;cursor:pointer}.reset-row{margin:10px 0 0;text-align:center}.reset-button{border:0;background:transparent;color:#087f89;text-decoration:underline;font:800 14px Nunito,sans-serif;cursor:pointer}.reset-box{display:none;margin-top:11px;padding:12px;border-radius:15px;background:#f1f8f8}.reset-box.open{display:block}.reset-form{display:grid;grid-template-columns:1fr auto;gap:8px}.message{margin:12px 0 0;padding:10px 12px;border-radius:13px;background:#dff5e5;font-weight:800;font-size:14px}.message.error{background:#ffe1df}.terms{margin:13px 0 0;text-align:center;color:#607687;font-size:12px;line-height:1.4}
@media(max-width:600px){.landing{width:min(100% - 14px,760px);margin:5px auto;padding:12px 13px 14px;border-radius:22px}.brand img{width:72px;height:72px}.brand b{font-size:19px}.lang{right:8px;top:8px}.landing h1{font-size:clamp(29px,9vw,37px)}.lead{font-size:14px;margin-bottom:11px}.plans{gap:8px}.plan{min-height:112px;padding:8px 5px}.plan b{font-size:27px}.divider{margin:12px 0 9px}.fields{grid-template-columns:1fr}.login-grid{grid-template-columns:1fr}.primary{width:100%}.reset-form{grid-template-columns:1fr}.terms{margin-top:9px}}
</style>
<style>.plans form{position:relative}.plans form>.read-aloud{position:absolute;right:8px;top:8px;z-index:2;margin:0}.plan{padding-right:42px}@media(max-width:600px){.plan{padding-right:38px}}</style>
<style>
body{padding:max(10px,env(safe-area-inset-top)) 0 max(10px,env(safe-area-inset-bottom))}.landing{overflow:hidden;border:1px solid #ffffffcf;background:linear-gradient(145deg,#fffef9 0%,#fff9e9 58%,#daf5f5 130%);box-shadow:0 28px 70px #10365420,inset 0 1px 0 #fff}.landing:before{content:"";position:absolute;width:250px;height:250px;right:-150px;top:-145px;border:42px solid #07858d10;border-radius:50%;pointer-events:none}.brand{position:relative;width:max-content;margin:0 auto 2px;padding:4px 17px 4px 7px;border:1px solid #ffffff;background:#ffffffb5;border-radius:999px;box-shadow:0 7px 20px #10365413}.brand img{filter:drop-shadow(0 8px 8px #10365420)}.lang{z-index:4;border:1px solid #fff;border-radius:14px;box-shadow:0 7px 18px #10365418}.landing h1{letter-spacing:-.035em;text-wrap:balance}.lead{font-weight:700;line-height:1.5}.offer-label{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:20px 2px 10px;font-weight:900;color:#103654}.offer-label small{display:inline-flex;align-items:center;min-height:30px;padding:5px 10px;border-radius:999px;background:#e1f6ef;color:#16704e;font-size:11px}.plans{gap:13px}.plans form{border-radius:23px}.plan{min-height:148px;border:1px solid #fff;border-radius:23px;background:linear-gradient(145deg,#f3ffff,#d8f4f3);box-shadow:0 12px 25px #10365414,inset 0 1px 0 #fff;transition:transform .18s,box-shadow .18s,border-color .18s}.plans form:first-child .plan{background:linear-gradient(145deg,#fff9df,#ffeaa2)}.plan:hover,.plan:focus-visible{border-color:#07858d;box-shadow:0 17px 32px #1036541e}.plan b{font-size:35px;letter-spacing:-.03em}.divider{font-weight:800}.login-grid{padding:15px;border:1px solid #dbe9eb;border-radius:20px;background:#ffffffb5;box-shadow:inset 0 1px 0 #fff}.field input{border:1px solid #cfdee2;background:#fffdfb;box-shadow:inset 0 2px 4px #10365408}.field input:focus{border-color:#07858d;outline:3px solid #07858d18}.primary{background:linear-gradient(145deg,#174565,#0c304c);box-shadow:0 7px 17px #10365428}.benefits{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin:12px 0 0}.benefits span{display:flex;align-items:center;gap:7px;min-height:40px;padding:8px 10px;border-radius:13px;background:#ffffffa8;color:#526b80;font-size:11px;font-weight:900}.benefits b{display:grid;place-items:center;width:24px;height:24px;border-radius:50%;background:#e5f7f7;color:#087f89}.terms{padding:10px 12px;border-radius:14px;background:#10365408}.read-aloud svg{width:18px;height:18px}
@media(max-width:600px){body{padding-top:4px}.landing{min-height:calc(100dvh - 8px);margin:0 auto;border-radius:25px}.brand{margin-top:1px}.offer-label{margin-top:13px}.plans{gap:9px}.plan{min-height:128px;border-radius:19px}.plan b{font-size:30px}.login-grid{padding:11px;border-radius:17px}.benefits{grid-template-columns:1fr 1fr}.benefits span:last-child{grid-column:1/-1}.landing h1{margin-top:3px}}
</style>
<style>
.plans.single-offer{grid-template-columns:1fr;max-width:620px;margin:0 auto}.plans.single-offer form{box-shadow:0 18px 38px #9a6b111a}.plans.single-offer .plan{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:7px 22px;min-height:205px;padding:22px 30px;text-align:left;background:linear-gradient(135deg,#fff8d7 0%,#ffe58a 58%,#d8f5f1 145%)}.offer-kicker{grid-column:1/-1;width:max-content;padding:5px 10px;border-radius:999px;background:#ffffffa8;color:#8b6417;font-size:11px;letter-spacing:.08em;text-transform:uppercase}.plan .offer-title{font:800 27px/1.02 'Baloo 2',Nunito,sans-serif;letter-spacing:-.02em}.plans.single-offer .plan b{grid-column:2;grid-row:2 / span 2;margin:0;font-size:56px;line-height:.9;white-space:nowrap}.offer-renewal{max-width:390px;font-size:14px;line-height:1.45;color:#35566f}.plans.single-offer .plan em{width:max-content;margin-top:5px;padding:11px 19px;border-radius:13px;font-size:15px;box-shadow:0 7px 16px #087f8928}.offer-cancel{align-self:center;color:#526b80;font-size:12px}.plans.single-offer form>.read-aloud{right:12px;top:12px}.purchase-note{max-width:620px;margin:9px auto 0;text-align:center;color:#526b80;font-size:12px;font-weight:800}
@media(max-width:600px){.plans.single-offer .plan{display:flex;flex-direction:column;justify-content:center;gap:5px;min-height:225px;padding:18px 42px 16px 18px;text-align:center}.offer-kicker{align-self:center}.plan .offer-title{font-size:25px}.plans.single-offer .plan b{font-size:51px}.offer-renewal{font-size:13px}.plans.single-offer .plan em{margin-top:4px;padding:10px 18px}.offer-cancel{font-size:11px}.offer-label{align-items:center}.offer-label>span{max-width:190px}.purchase-note{margin-top:7px;line-height:1.4}}
</style>
<main class="landing">
  <button class="lang" id="lang" type="button" aria-label="Vis norsk tekst">🇳🇴</button>
  <div class="brand"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa"><b>GOOGA</b></div>
  <h1 data-so="Halxiraalo Af-Soomaali ah.<br>Waqtiyo yar oo wadajir ah." data-no="Somaliske gåter.<br>Små stunder sammen.">Halxiraalo Af-Soomaali ah.<br>Waqtiyo yar oo wadajir ah.</h1>
  <p class="lead" data-so="Ku baro Af-Soomaaliga halxiraalo, cod iyo ciyaar qoyska oo dhan ah." data-no="Lær somali gjennom gåter, lyd og lek for hele familien." data-speak-so="Halxiraalo Af-Soomaali ah. Waqtiyo yar oo wadajir ah. Ku baro Af-Soomaaliga halxiraalo, cod iyo ciyaar qoyska oo dhan ah." data-speak-audio="audio/ui/login-hero.mp3">Ku baro Af-Soomaaliga halxiraalo, cod iyo ciyaar qoyska oo dhan ah.</p>
  <div class="free-test free-culture-front"><a href="culture-test.php"><span><b data-so="Bariis på Grandis" data-no="Bariis på Grandis">Bariis på Grandis</b><small data-so="Kulturkompass bilaash ah · akoon looma baahna" data-no="Gratis kulturkompass · ingen innlogging">Kulturkompass bilaash ah · akoon looma baahna</small></span><strong aria-hidden="true">→</strong></a><a class="arab-contact-link" href="contact.php" data-so="💬 Farriin u dir Arab →" data-no="💬 Send melding til Arab →">💬 Farriin u dir Arab →</a></div>
  <div class="offer-label"><span data-so="Maanta ku bilow qiime yar" data-no="Kom i gang i dag">Maanta ku bilow qiime yar</span><small data-so="✓ Lacag-bixin ammaan ah" data-no="✓ Sikker betaling">✓ Lacag-bixin ammaan ah</small></div>
  <div class="plans single-offer">
    <form method="post" action="checkout.php"><input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_payment_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="kind" value="trial"><button class="plan offer" type="submit"><span class="offer-kicker" data-so="2 maalmood oo tijaabo ah" data-no="2 dagers prøveperiode">2 maalmood oo tijaabo ah</span><span class="offer-title" data-so="Googa maanta bilow" data-no="Start Googa i dag">Googa maanta bilow</span><b>kr 5</b><small class="offer-renewal" data-so="Kadib kr 50 bishii. Rukhsaddu si toos ah ayay u cusboonaanaysaa ilaa aad joojiso." data-no="Deretter kr 50 per måned. Abonnementet fornyes automatisk til du sier opp.">Kadib kr 50 bishii. Rukhsaddu si toos ah ayay u cusboonaanaysaa ilaa aad joojiso.</small><em data-so="Ku bilow kr 5 →" data-no="Start for kr 5 →">Ku bilow kr 5 →</em><span class="offer-cancel" data-so="✓ Jooji wakhti kasta" data-no="✓ Si opp når som helst">✓ Jooji wakhti kasta</span></button><button class="read-aloud" type="button" data-speak-button data-speak-so="Googa maanta ku bilow shan karoon. Waxaad helaysaa laba maalmood oo tijaabo ah. Kadib rukhsaddu waa konton karoon bishii ilaa aad joojiso." data-speak-audio="audio/ui/plan-trial.mp3" aria-label="Dhegeyso qiimaha">🔊</button></form>
  </div>
  <?php if (is_array($activeAmbassador)): ?><div class="ambassador-applied"><span>✓</span><div><b data-so="Waxaa kugula taliyey <?= htmlspecialchars((string)$activeAmbassador['name'], ENT_QUOTES, 'UTF-8') ?>" data-no="Anbefalt av <?= htmlspecialchars((string)$activeAmbassador['name'], ENT_QUOTES, 'UTF-8') ?>">Waxaa kugula taliyey <?= htmlspecialchars((string)$activeAmbassador['name'], ENT_QUOTES, 'UTF-8') ?></b><small data-so="Waxaad kaydsanaysaa kr 50 labada bilood ee ugu horreeya." data-no="Du sparer totalt kr 50 de to første hele månedene.">Waxaad kaydsanaysaa kr 50 labada bilood ee ugu horreeya.</small></div></div><?php else: ?><form class="ambassador-code-form" method="post"><input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_login_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="ambassador"><label for="ambassador_code" data-so="Ma haysataa koodh safiir?" data-no="Har du en ambassadørkode?">Ma haysataa koodh safiir?</label><span><input id="ambassador_code" name="ambassador_code" autocomplete="off" minlength="4" required><button type="submit" data-so="Isticmaal" data-no="Bruk kode">Isticmaal</button></span></form><?php endif; ?>
  <div class="commercial-links" id="annual"><form method="post" action="checkout.php"><input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_payment_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="kind" value="annual"><button class="annual-button" type="submit" data-so="★ Kr 499 sanadkii" data-no="★ Årsabonnement kr 499">★ Kr 499 sanadkii</button></form><a class="commercial-link" href="gift.php" data-so="🎁 Hadiyad sii qoys" data-no="🎁 Gi Googa i gave">🎁 Hadiyad sii qoys</a></div>
  <p class="purchase-note" data-so="Hal gujin ayaa ku geynaysa lacag-bixinta ammaan ah ee Stripe." data-no="Ett trykk tar deg til sikker betaling hos Stripe." data-speak-so="Hal gujin ayaa ku geynaysa lacag-bixinta ammaan ah ee Stripe.">Hal gujin ayaa ku geynaysa lacag-bixinta ammaan ah ee Stripe.</p>
  <div class="divider" data-so="Hore ayaad xisaab u leedahay?" data-no="Har du allerede en konto?" data-speak-so="Hore ayaad xisaab u leedahay? Geli e-mailkaaga iyo furaha sirta ah." data-speak-audio="audio/ui/login-account.mp3">Hore ayaad xisaab u leedahay?</div>
  <form method="post" class="login-grid">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_login_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="login">
    <div class="fields"><div class="field"><label for="email" data-so="E-mail" data-no="E-post">E-mail</label><input id="email" name="email" type="email" autocomplete="username" required></div><div class="field"><label for="password" data-so="Furaha sirta" data-no="Passord">Furaha sirta</label><input id="password" name="password" type="password" autocomplete="current-password" required></div></div>
    <button class="primary" type="submit" data-so="Soo gal" data-no="Logg inn">Soo gal</button>
  </form>
  <div class="benefits"><span><b>3</b><i data-so="qalab carruur" data-no="barneenheter">qalab carruur</i></span><span><b>✦</b><i data-so="wax cusub bil kasta" data-no="nytt hver måned">wax cusub bil kasta</i></span><span><b>♪</b><i data-so="cod Af-Soomaali" data-no="somalisk lyd">cod Af-Soomaali</i></span></div>
  <p class="reset-row" data-speak-so="Ma illowday furaha sirta ah? Waxaad samayn kartaa mid cusub." data-speak-audio="audio/ui/login-reset.mp3"><button class="reset-button" id="showReset" type="button" data-so="Ma illowday furaha? Samee mid cusub" data-no="Glemt passord? Opprett et nytt">Ma illowday furaha? Samee mid cusub</button></p>
  <section class="reset-box<?= $notice !== '' ? ' open' : '' ?>" id="resetBox"><form method="post" class="reset-form"><input type="hidden" name="csrf" value="<?= htmlspecialchars((string)$_SESSION['googa_login_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="reset"><div class="field"><label for="resetEmail" data-so="E-mailkaaga" data-no="E-postadressen din">E-mailkaaga</label><input id="resetEmail" name="email" type="email" autocomplete="email" required></div><button class="primary" type="submit" data-so="Soo dir xiriirka" data-no="Send passordlenke">Soo dir xiriirka</button></form></section>
  <?php if ($notice !== ''): ?><p class="message" data-speak-so="<?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <?php if ($error !== ''): ?><p class="message error" data-speak-so="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <?php if ($paymentState === 'cancelled'): ?><p class="message error" data-so="Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin." data-no="Betalingen ble avbrutt. Ingen penger er trukket." data-speak-so="Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin." data-speak-audio="audio/ui/payment-cancelled.mp3">Lacag-bixinta waa la joojiyey. Wax lacag ah lama qaadin.</p><?php endif; ?>
  <?php if ($paymentState === 'error'): ?><p class="message error" data-so="Lacag-bixinta lama bilaabi karin hadda. Fadlan mar kale isku day." data-no="Betalingen kunne ikke startes nå. Prøv igjen." data-speak-so="Lacag-bixinta lama bilaabi karin hadda. Fadlan mar kale isku day." data-speak-audio="audio/ui/payment-error.mp3">Lacag-bixinta lama bilaabi karin hadda. Fadlan mar kale isku day.</p><?php endif; ?>
  <p class="terms" data-so="Waxaad bixinaysaa kr 5 hadda. Laba maalmood kadib rukhsaddu si toos ah ayay ugu soconaysaa kr 50 bishii ilaa aad joojiso. Stripe ayaa si ammaan ah u maamusha lacag-bixinta. Carruurtu waxay ku xirmaan QR-ka qoyska kadib marka waalidku galo." data-no="Du betaler kr 5 nå. Etter to dager fortsetter abonnementet automatisk til kr 50 per måned frem til oppsigelse. Stripe håndterer betalingen sikkert. Barn kobles til med familie-QR etter at forelderen har logget inn." data-speak-so="Waxaad bixinaysaa shan karoon hadda. Laba maalmood kadib rukhsaddu si toos ah ayay ugu soconaysaa konton karoon bishii ilaa aad joojiso. Stripe ayaa si ammaan ah u maamusha lacag-bixinta.">Waxaad bixinaysaa kr 5 hadda. Laba maalmood kadib rukhsaddu si toos ah ayay ugu soconaysaa kr 50 bishii ilaa aad joojiso. Stripe ayaa si ammaan ah u maamusha lacag-bixinta.</p>
</main>
<script src="<?= htmlspecialchars(googa_asset_url('assets/read-aloud.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script>const langButton=document.getElementById('lang');let lang='so';langButton.onclick=()=>{lang=lang==='so'?'no':'so';document.documentElement.lang=lang;langButton.textContent=lang==='so'?'🇳🇴':'🇸🇴';langButton.setAttribute('aria-label',lang==='so'?'Vis norsk tekst':'Muuji Af-Soomaali');document.querySelectorAll('[data-so]').forEach(el=>el.innerHTML=el.dataset[lang]);document.querySelectorAll('[data-so][data-speak-ready]').forEach(el=>delete el.dataset.speakReady);window.GoogaReadAloud?.enhance()};document.getElementById('showReset').onclick=()=>{document.getElementById('resetBox').classList.toggle('open');document.getElementById('resetEmail').focus()};</script>
</html>
