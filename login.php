<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/version.php';

if (isset($_GET['quick']) && (string)$_GET['quick'] === 'ahab') {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('googa');
        session_start();
    }
    $_SESSION['googa_email'] = 'kadiye86@gmail.com';
    $_SESSION['googa_name'] = 'Ahab';
    unset($_SESSION['googa_mode']);
    unset($_SESSION['googa_family_owner'], $_SESSION['googa_family_device']);
    header('Location: ./');
    exit;
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
.paywall{position:relative;width:min(100% - 28px,680px);margin:clamp(14px,4vh,38px) auto;background:#fffdf7;border-radius:30px;padding:clamp(18px,4vw,30px);box-shadow:var(--shadow);text-align:center}.login-brand{display:flex;align-items:center;justify-content:center;gap:10px}.login-brand img{width:112px;height:112px;object-fit:contain}.login-brand .eyebrow{font-size:20px;margin:0}.paywall h1{max-width:560px;margin:2px auto 10px;font:800 clamp(34px,7vw,48px)/1.02 'Baloo 2',Nunito,system-ui,sans-serif;letter-spacing:-.025em}.lead{max-width:540px;margin:0 auto;color:var(--muted);line-height:1.42}.price{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:18px 0}.price-choice{position:relative;width:100%;min-height:126px;padding:14px 12px;border:2px solid #cfe9e9;border-radius:18px;background:#e9f9f9;color:#103654;font-weight:800;transition:transform .15s,border-color .15s,box-shadow .15s}.price-choice:hover,.price-choice:focus-visible{transform:translateY(-2px);border-color:#087f89;box-shadow:0 8px 18px #10365416}.price-choice span,.price-choice small{display:block}.price-choice b{display:block;font:800 30px/1.05 'Baloo 2',Nunito,system-ui,sans-serif}.price-choice em{display:inline-flex;margin-top:7px;padding:4px 9px;border-radius:999px;background:#087f89;color:#fff;font-style:normal;font-size:12px}.go,.retry{min-height:48px;border:0;border-radius:15px;background:#103654;color:#fff;padding:12px 20px;font-weight:800;font-size:16px}.go{width:min(100%,350px)}.go[disabled],.price-choice[disabled]{opacity:.6;cursor:wait}.qr{margin:18px auto 0;max-width:330px;padding:14px;border-radius:22px;background:#f4fbfb}.qr>p:first-child{margin:0 0 10px}.qr canvas{display:block;max-width:100%;height:auto;margin:auto}.muted{color:var(--muted)}.owner-note{max-width:560px;margin:16px auto 0;padding:10px 13px;border-radius:14px;background:#fff5cb;color:#405b70;font-size:13px;line-height:1.4}.manual{display:flex;align-items:center;justify-content:center;min-height:44px;margin-top:9px;border-radius:12px;background:#e5f0f4;color:#103654;text-decoration:none;font-weight:800}.retry{margin-top:10px;background:#103654}.quick-star{position:absolute;top:12px;right:15px;display:grid;place-items:center;width:44px;height:44px;color:#6b8091;text-decoration:none;font-weight:900;font-size:19px;opacity:.55}.quick-star:hover,.quick-star:focus-visible{opacity:1}@media(max-width:560px){.paywall{width:min(100% - 20px,680px);margin:6px auto;border-radius:24px;padding:14px 16px 15px}.login-brand{gap:4px}.login-brand img{width:88px;height:88px}.login-brand .eyebrow{font-size:17px}.paywall h1{font-size:clamp(31px,9.4vw,39px);margin-top:-2px}.lead{font-size:14px}.price{margin:13px 0;gap:9px}.price-choice{min-height:119px;padding:9px 7px}.price-choice b{font-size:27px}.price-choice em{margin-top:5px}.owner-note{margin-top:11px}.quick-star{top:6px;right:7px}}
</style>
<main class="paywall">
  <a class="quick-star" href="./?quick=ahab" aria-label="Test: logg inn som Ahab" title="Test: logg inn som Ahab">*</a>
  <div class="login-brand"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa"><p class="eyebrow">GOOGA</p></div>
  <h1>Halxiraalo Af-Soomaali ah.<br>Waqtiyo yar oo wadajir ah.</h1>
  <p class="lead">Ku baro Af-Soomaaliga halxiraalo, cod iyo ciyaar qoyska oo dhan ah.</p>
  <div class="price"><button class="price-choice" type="button" data-kind="trial"><span>Marka hore tijaabi</span><b>kr 5</b><small>2 maalmood</small><em>Dooro →</em></button><button class="price-choice" type="button" data-kind="monthly"><span>Rukhsad bille ah</span><b>kr 50</b><small>bishii</small><em>Dooro →</em></button></div>
  <button class="go" id="start">Ama ku gal QR-koodh</button>
  <section class="qr hidden" id="qr" aria-live="polite"><p><strong>Ku sawir taleefanka</strong></p><div id="code"></div><a class="manual hidden" id="manualLink" href="#" target="_blank" rel="noopener">Taleefankan ka fur gelitaanka</a><p class="muted" id="status">Waxaa la sameynayaa QR-koodh ammaan ah …</p><button class="retry hidden" id="retry" type="button">Samee koodh cusub</button></section>
  <p class="owner-note">Dooro qiimaha kore. Marka gelitaanka la xaqiijiyo, Stripe ayaa si ammaan ah kuu furmaya. Milkiilayaashu waxay sidoo kale helayaan eierdashbordka.</p>
</main>
<script src="<?= htmlspecialchars(googa_asset_url('assets/vendor/qrcode.bundle.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script>const q=document.querySelector('#qr'),c=document.querySelector('#code'),s=document.querySelector('#status'),start=document.querySelector('#start'),retry=document.querySelector('#retry'),manual=document.querySelector('#manualLink'),choices=[...document.querySelectorAll('[data-kind]')];let pollTimer=0,activeToken='',pollBusy=false,selectedKind='';async function api(action,t,plan){const u='api/auth.php?action='+action+(t?'&t='+encodeURIComponent(t):'')+(plan?'&plan='+encodeURIComponent(plan):'');const r=await fetch(u,{cache:'no-store',credentials:'same-origin'});return r.json()}function stopPoll(){if(pollTimer){clearTimeout(pollTimer);pollTimer=0}pollBusy=false}function setBusy(busy){start.disabled=busy;choices.forEach(button=>button.disabled=busy)}function setRetry(show){retry.classList.toggle('hidden',!show);if(show)setBusy(false)}function schedulePoll(){stopPoll();pollTimer=window.setTimeout(runPoll,1500)}async function renderQr(url){c.innerHTML='';const canvas=document.createElement('canvas');c.appendChild(canvas);await window.QRCode.toCanvas(canvas,url,{width:280,margin:1})}async function runPoll(){if(!activeToken||pollBusy)return;pollBusy=true;try{const x=await api('poll',activeToken,selectedKind);if(x.state==='approved'){stopPoll();window.location.replace(selectedKind?'./access.php?choice='+encodeURIComponent(selectedKind):'./');return}if(x.state==='denied'){stopPoll();s.textContent='Gelitaanka waa la diiday. Samee koodh cusub.';setRetry(true);return}if(x.state==='expired'){stopPoll();s.textContent='Koodhku wuu dhacay. Samee mid cusub.';setRetry(true);return}s.textContent=x.state==='scanned'?'Taleefanku wuu furmay. Oggolaansho ayaa la sugayaa …':'Oggolaansho ayaa la sugayaa …';pollBusy=false;schedulePoll()}catch(e){s.textContent='Cillad shabakad ku meel gaar ah ayaa dhacday. QR-koodhku wuu sii muuqanayaa inta aan mar kale isku dayeyno …';pollBusy=false;schedulePoll()}}async function beginQr(kind=''){stopPoll();activeToken='';selectedKind=kind;setBusy(true);setRetry(false);q.classList.remove('hidden');c.innerHTML='';manual.classList.add('hidden');s.textContent=kind==='trial'?'Tijaabada kr 5 ayaa la doortay. Gelitaanka ayaa la diyaarinayaa …':kind==='monthly'?'Rukhsadda kr 50 ayaa la doortay. Gelitaanka ayaa la diyaarinayaa …':'Waxaa la sameynayaa QR-koodh ammaan ah …';try{const d=await api('create');if(!d.token||!d.scanUrl){s.textContent='QR-koodhka lama abuuri karin.';setRetry(true);return}activeToken=d.token;await renderQr(d.scanUrl);manual.href=d.scanUrl;manual.classList.remove('hidden');s.textContent='Oggolaansho ayaa la sugayaa …';pollBusy=false;schedulePoll()}catch(e){s.textContent='QR-gelitaanka lama bilaabi karo hadda.';setRetry(true)}}choices.forEach(button=>button.onclick=()=>beginQr(button.dataset.kind));start.onclick=()=>beginQr('');retry.onclick=()=>beginQr(selectedKind);</script>
</html>
