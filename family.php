<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/store.php';
require_once __DIR__ . '/lib/version.php';

$context = googa_session_context();
if (empty($context['authenticated']) || ($context['email'] ?? '') === '' || empty($context['access']['allowed'])) {
    header('Location: ./');
    exit;
}
if (empty($_SESSION['googa_family_csrf'])) {
    $_SESSION['googa_family_csrf'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['googa_family_csrf'];
?><!doctype html>
<html lang="so">
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Googa – Qoyska</title>
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
<style>
.family{max-width:780px;margin:5vh auto;background:#fffdf7;border-radius:32px;padding:28px;box-shadow:0 18px 45px #10365422}.family h1{color:#103654}.family p{line-height:1.6}.qrbox{min-height:290px;display:grid;place-items:center;margin:22px 0;padding:18px;border-radius:24px;background:#e5f7f7;text-align:center}.qrbox canvas{max-width:100%;height:auto}.pwa-guide{margin:18px 0;padding:18px;border:1px solid #d7e9e9;border-radius:20px;background:#f5fbfb}.pwa-guide h2{margin:0 0 8px;color:#103654;font-size:1.1rem}.pwa-guide ol{margin:8px 0 0;padding-left:22px}.pwa-guide li{margin:8px 0}.go{border:0;border-radius:16px;background:#0b8691;color:#fff;padding:14px 19px;font-weight:800;font-size:16px;cursor:pointer}.ghost{display:inline-block;margin-left:8px;border-radius:16px;background:#103654;color:#fff;padding:14px 19px;font-weight:800;text-decoration:none}.muted{color:#5b7185}.devices{margin-top:24px;padding-top:18px;border-top:1px solid #dce8ee}.device{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #e5edf4}.device button{border:0;border-radius:10px;background:#fff0ee;color:#9b3727;padding:8px 10px;font-weight:800;cursor:pointer}.pending{margin-top:22px;padding:16px;border-radius:20px;background:#fff5cb}.pending h2{margin:0 0 8px}.pending button{border:0;border-radius:10px;padding:8px 10px;margin-left:6px;font-weight:800;cursor:pointer;background:#0b8691;color:#fff}.pending button.reject{background:#9b3727}.notice{padding:12px 14px;border-radius:16px;background:#fff5cb;font-weight:800}@media(max-width:600px){.family{margin:0;border-radius:0;min-height:100vh}.ghost{margin:10px 0 0}}
</style>
<main class="family">
  <img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa" width="120">
  <p class="eyebrow">GOOGA QOYSKA</p><h1>La wadaag carruurtaada</h1>
  <p>QR-koodhkani wuu joogto yahay. Marka qalab cusub sawiro, adiga ayaa marka hore oggolaanaya codsiga. Ilaa saddex qalab ayaa geli kara rukhsadda qoyska.</p>
  <div class="qrbox" id="qrbox"><p class="muted">QR-koodh ayaa la diyaarinayaa …</p></div>
  <aside class="pwa-guide" aria-label="Sida Googa loogu kaydiyo taleefanka"><h2>Sidee Googa loogu kaydiyaa taleefanka?</h2><p>Marka QR-koodhka lagu furo qalabka ilmaha, ku kaydi Googa shaashadda guriga si loogu furo sida app caadi ah.</p><ol><li><strong>iPhone ama iPad:</strong> Ku fur Googa <strong>Safari</strong>, taabo calaamadda <strong>La wadaag</strong> (□↑), dooro <strong>Ku dar Shaashadda Guriga</strong>, dabadeed taabo <strong>Ku dar</strong>.</li><li><strong>Android:</strong> Ku fur Googa <strong>Chrome</strong>, taabo saddexda dhibcood (⋮) ee kore, dooro <strong>Ku rakib app-ka</strong> ama <strong>Ku dar shaashadda guriga</strong>, dabadeed xaqiiji.</li></ol><p class="muted">Astaanta Googa waxay markaas ka soo muuqanaysaa shaashadda guriga.</p></aside>
  <button class="go" id="rotate" type="button">Beddel QR-koodhka</button><a class="ghost" href="./">Ku noqo ciyaarta</a>
  <p class="notice">Haddii qalab afraad la oggolaado, qalabka ugu horreeyey si toos ah ayaa looga saaraa.</p>
  <section class="pending"><h2>Codsiyada cusub</h2><div id="pending"><p class="muted">Weli codsi ma jiro.</p></div></section>
  <section class="devices"><h2>Qalabka ku xiran</h2><div id="devices"><p class="muted">Waa la soo dejinayaa …</p></div></section>
</main>
<script src="<?= htmlspecialchars(googa_asset_url('assets/vendor/qrcode.bundle.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script>
const csrf=<?= json_encode($csrf, JSON_UNESCAPED_SLASHES) ?>,box=document.querySelector('#qrbox'),devices=document.querySelector('#devices'),pending=document.querySelector('#pending');let activeUrl='';
async function api(action,data){const opts={cache:'no-store',credentials:'same-origin'};if(data){opts.method='POST';opts.headers={'Content-Type':'application/x-www-form-urlencoded'};opts.body=new URLSearchParams({...data,csrf})}const r=await fetch('family-api.php?action='+action,opts);return r.json()}
function renderDevices(items){devices.innerHTML=items.length?items.map((d,i)=>`<div class="device"><span>${d.label}<br><small class="muted">${i===0?'Qalabka ugu horreeyey':'Qalab ku xiran'}</small></span><button data-remove="${d.id}">Ka saar</button></div>`).join(''):`<p class="muted">Weli qalab carruur ah lama xirin.</p>`;devices.querySelectorAll('button').forEach(b=>b.onclick=async()=>{await api('remove',{id:b.dataset.remove});await loadStatus()})}
function renderPending(items){pending.innerHTML=items.length?items.map(d=>`<div class="device"><span>${d.label}<br><small class="muted">Sugaya oggolaanshahaaga</small></span><span><button data-approve="${d.id}">Oggolow</button><button class="reject" data-reject="${d.id}">Diid</button></span></div>`).join(''):`<p class="muted">Weli codsi ma jiro.</p>`;pending.querySelectorAll('[data-approve]').forEach(b=>b.onclick=async()=>{await api('approve',{id:b.dataset.approve});await loadStatus()});pending.querySelectorAll('[data-reject]').forEach(b=>b.onclick=async()=>{await api('reject',{id:b.dataset.reject});await loadStatus()})}
async function renderQr(url){if(!url||url===activeUrl)return;activeUrl=url;const canvas=document.createElement('canvas');box.innerHTML='';box.appendChild(canvas);await window.QRCode.toCanvas(canvas,url,{width:280,margin:1})}
async function loadStatus(){try{const d=await api('status');if(!d.ok)return;await renderQr(d.scanUrl);renderDevices(d.devices);renderPending(d.pending)}catch(e){box.innerHTML='<p class="muted">Xogta qoyska lama heli karo hadda.</p>'}}
document.querySelector('#rotate').onclick=async()=>{if(window.confirm('Ma rabtaa inaad beddesho QR-koodhka? Koodhkii hore ma shaqayn doono.')){activeUrl='';await api('rotate',{});await loadStatus()}};loadStatus();setInterval(loadStatus,3000);
</script></html>
