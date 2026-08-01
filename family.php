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
<html lang="so"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Googa – Qoyska</title>
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
<style>.family{max-width:780px;margin:5vh auto;background:#fffdf7;border-radius:32px;padding:28px;box-shadow:0 18px 45px #10365422}.family h1{color:#103654}.family p{line-height:1.6}.qrbox{min-height:290px;display:grid;place-items:center;margin:22px 0;padding:18px;border-radius:24px;background:#e5f7f7;text-align:center}.qrbox canvas{max-width:100%;height:auto}.go{border:0;border-radius:16px;background:#0b8691;color:#fff;padding:14px 19px;font-weight:800;font-size:16px;cursor:pointer}.ghost{display:inline-block;margin-left:8px;border-radius:16px;background:#103654;color:#fff;padding:14px 19px;font-weight:800;text-decoration:none}.muted{color:#5b7185}.devices{margin-top:24px;padding-top:18px;border-top:1px solid #dce8ee}.device{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #e5edf4}.device button{border:0;border-radius:10px;background:#fff0ee;color:#9b3727;padding:8px 10px;font-weight:800;cursor:pointer}.notice{padding:12px 14px;border-radius:16px;background:#fff5cb;font-weight:800}@media(max-width:600px){.family{margin:0;border-radius:0;min-height:100vh}.ghost{margin:10px 0 0}}</style>
<main class="family"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa" width="120"><p class="eyebrow">GOOGA QOYSKA</p><h1>La wadaag carruurtaada</h1><p>Ku sawir QR-koodhkan qalabka ilmaha. Ilaa saddex qalab ayaa geli kara rukhsadda qoyska. Haddii qalab afraad ku xirmo, qalabka ugu horreeyey si toos ah ayaa looga saaraa.</p><div class="qrbox" id="qrbox"><p class="muted" id="qrstatus">QR-koodh ayaa la diyaarinayaa …</p></div><button class="go" id="newqr" type="button">Samee QR-koodh cusub</button><a class="ghost" href="./">Ku noqo ciyaarta</a><p class="notice" id="expiry"></p><section class="devices"><h2>Qalabka ku xiran</h2><div id="devices"><p class="muted">Waa la soo dejinayaa …</p></div></section></main>
<script src="<?= htmlspecialchars(googa_asset_url('assets/vendor/qrcode.bundle.js'), ENT_QUOTES, 'UTF-8') ?>"></script><script>const csrf=<?= json_encode($csrf, JSON_UNESCAPED_SLASHES) ?>,box=document.querySelector('#qrbox'),status=document.querySelector('#qrstatus'),devices=document.querySelector('#devices'),expiry=document.querySelector('#expiry');let activeUrl='';async function api(action,data){const opts={cache:'no-store',credentials:'same-origin'};if(data){opts.method='POST';opts.headers={'Content-Type':'application/x-www-form-urlencoded'};opts.body=new URLSearchParams({...data,csrf})}const r=await fetch('family-api.php?action='+action,opts);return r.json()}function showDevices(items,max){devices.innerHTML=items.length?items.map((d,i)=>`<div class="device"><span>${d.label}<br><small class="muted">${i===0?'Qalabka ugu horreeyey':'Qalab ku xiran'}</small></span><button data-id="${d.id}">Ka saar</button></div>`).join(''):`<p class="muted">Weli qalab carruur ah lama xirin.</p>`;devices.querySelectorAll('button').forEach(b=>b.onclick=async()=>{await api('remove',{id:b.dataset.id});await loadStatus()})}async function loadStatus(){const d=await api('status');if(d.ok)showDevices(d.devices,d.maxDevices)}async function create(){box.innerHTML='<p class="muted">QR-koodh ayaa la diyaarinayaa …</p>';expiry.textContent='';try{const d=await api('create',{});if(!d.ok||!d.scanUrl)throw Error();activeUrl=d.scanUrl;const canvas=document.createElement('canvas');box.innerHTML='';box.appendChild(canvas);await window.QRCode.toCanvas(canvas,activeUrl,{width:280,margin:1});expiry.textContent='Koodhkani wuxuu shaqaynayaa 15 daqiiqo. Hal QR-koodh ayaa ku filan ilaa saddex qalab.';await loadStatus()}catch(e){box.innerHTML='<p class="muted">QR-koodhka lama samayn karin. Isku day mar kale.</p>'}}document.querySelector('#newqr').onclick=create;loadStatus();create();</script></html>
