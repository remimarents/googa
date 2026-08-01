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
.family{max-width:1080px;margin:20px auto;background:#fffdf7;border-radius:28px;padding:22px 26px;box-shadow:0 18px 45px #10365422}.family h1{margin:0;color:#103654;font-size:clamp(28px,3vw,42px)}.family p{line-height:1.45}.family-head{display:flex;align-items:center;gap:16px;margin-bottom:14px}.family-head img{width:76px;height:auto}.family-head .eyebrow{margin:0 0 3px}.family-grid{display:grid;grid-template-columns:272px minmax(0,1fr);gap:24px;align-items:start}.qrbox{width:100%;min-height:236px;display:grid;place-items:center;padding:10px;border-radius:22px;background:#e5f7f7;text-align:center}.qrbox canvas{max-width:100%;height:auto}.intro{margin:0 0 12px;font-size:16px}.qr-actions{display:flex;gap:8px;margin-top:10px}.go{border:0;border-radius:13px;background:#0b8691;color:#fff;padding:10px 13px;font-weight:800;font-size:14px;cursor:pointer}.ghost{display:inline-block;border-radius:13px;background:#103654;color:#fff;padding:10px 13px;font-weight:800;font-size:14px;text-decoration:none}.muted{color:#5b7185}.panel-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.devices,.pending{margin:0;padding:13px 15px;border-radius:18px;background:#f5fbfb}.pending{background:#fff5cb}.devices h2,.pending h2{margin:0 0 5px;color:#103654;font-size:17px}.device{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #e5edf4;font-size:14px}.device:last-child{border-bottom:0}.device button{border:0;border-radius:9px;background:#fff0ee;color:#9b3727;padding:6px 8px;font-weight:800;cursor:pointer}.pending button{border:0;border-radius:9px;padding:6px 8px;margin-left:4px;font-weight:800;cursor:pointer;background:#0b8691;color:#fff}.pending button.reject{background:#9b3727}.notice{margin:10px 0 0;padding:9px 11px;border-radius:13px;background:#fff5cb;font-weight:800;font-size:13px}.pwa-guide{display:grid;grid-template-columns:230px 1fr;gap:18px;align-items:start;margin:16px 0 0;padding:13px 16px;border:1px solid #d7e9e9;border-radius:18px;background:#f5fbfb}.pwa-guide h2{margin:0 0 4px;color:#103654;font-size:17px}.pwa-guide p{margin:0;font-size:13px}.pwa-steps{display:grid;grid-template-columns:1fr 1fr;gap:14px}.pwa-steps p{font-size:13px}.pwa-steps strong{color:#103654}@media(max-width:760px){.family{margin:0;border-radius:0;min-height:100vh;padding:22px}.family-grid{grid-template-columns:1fr}.qrbox{max-width:280px;margin:0 auto}.panel-grid,.pwa-guide,.pwa-steps{grid-template-columns:1fr}.family-head img{width:70px}.pwa-guide{gap:9px}}
</style>
<main class="family">
  <header class="family-head"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa"><div><p class="eyebrow">GOOGA QOYSKA</p><h1>La wadaag carruurtaada</h1></div></header>
  <div class="family-grid">
    <section><div class="qrbox" id="qrbox"><p class="muted">QR-koodh ayaa la diyaarinayaa …</p></div><div class="qr-actions"><button class="go" id="rotate" type="button">Beddel QR-koodhka</button><a class="ghost" href="./">Ciyaarta</a></div><p class="notice">Qalabka 4aad wuxuu beddelaa kii ugu horreeyey.</p></section>
    <section><p class="intro">QR-koodhkani wuu joogto yahay. Qalab cusub marka uu sawiro, adiga ayaa marka hore oggolaanaya codsiga.</p><div class="panel-grid"><section class="pending"><h2>Codsiyada cusub</h2><div id="pending"><p class="muted">Weli codsi ma jiro.</p></div></section><section class="devices"><h2>Qalabka ku xiran</h2><div id="devices"><p class="muted">Waa la soo dejinayaa …</p></div></section></div></section>
  </div>
  <aside class="pwa-guide" aria-label="Sida Googa loogu kaydiyo taleefanka"><div><h2>Ku kaydi Googa</h2><p>Ka dhig app ku jira shaashadda guriga.</p></div><div class="pwa-steps"><p><strong>iPhone/iPad:</strong> Safari → La wadaag (□↑) → Ku dar Shaashadda Guriga → Ku dar.</p><p><strong>Android:</strong> Chrome → ⋮ → Ku rakib app-ka / Ku dar shaashadda guriga.</p></div></aside>
</main>
<script src="<?= htmlspecialchars(googa_asset_url('assets/vendor/qrcode.bundle.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script>
const csrf=<?= json_encode($csrf, JSON_UNESCAPED_SLASHES) ?>,box=document.querySelector('#qrbox'),devices=document.querySelector('#devices'),pending=document.querySelector('#pending');let activeUrl='';
async function api(action,data){const opts={cache:'no-store',credentials:'same-origin'};if(data){opts.method='POST';opts.headers={'Content-Type':'application/x-www-form-urlencoded'};opts.body=new URLSearchParams({...data,csrf})}const r=await fetch('family-api.php?action='+action,opts);return r.json()}
function renderDevices(items){devices.innerHTML=items.length?items.map((d,i)=>`<div class="device"><span>${d.label}<br><small class="muted">${i===0?'Qalabka ugu horreeyey':'Qalab ku xiran'}</small></span><button data-remove="${d.id}">Ka saar</button></div>`).join(''):`<p class="muted">Weli qalab carruur ah lama xirin.</p>`;devices.querySelectorAll('button').forEach(b=>b.onclick=async()=>{await api('remove',{id:b.dataset.remove});await loadStatus()})}
function renderPending(items){pending.innerHTML=items.length?items.map(d=>`<div class="device"><span>${d.label}<br><small class="muted">Sugaya oggolaanshahaaga</small></span><span><button data-approve="${d.id}">Oggolow</button><button class="reject" data-reject="${d.id}">Diid</button></span></div>`).join(''):`<p class="muted">Weli codsi ma jiro.</p>`;pending.querySelectorAll('[data-approve]').forEach(b=>b.onclick=async()=>{await api('approve',{id:b.dataset.approve});await loadStatus()});pending.querySelectorAll('[data-reject]').forEach(b=>b.onclick=async()=>{await api('reject',{id:b.dataset.reject});await loadStatus()})}
async function renderQr(url){if(!url||url===activeUrl)return;activeUrl=url;const canvas=document.createElement('canvas');box.innerHTML='';box.appendChild(canvas);await window.QRCode.toCanvas(canvas,url,{width:214,margin:1})}
async function loadStatus(){try{const d=await api('status');if(!d.ok)return;await renderQr(d.scanUrl);renderDevices(d.devices);renderPending(d.pending)}catch(e){box.innerHTML='<p class="muted">Xogta qoyska lama heli karo hadda.</p>'}}
document.querySelector('#rotate').onclick=async()=>{if(window.confirm('Ma rabtaa inaad beddesho QR-koodhka? Koodhkii hore ma shaqayn doono.')){activeUrl='';await api('rotate',{});await loadStatus()}};loadStatus();setInterval(loadStatus,3000);
</script></html>
