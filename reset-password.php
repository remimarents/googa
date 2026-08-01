<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/store.php';
require_once __DIR__ . '/lib/version.php';

$token = trim((string)($_GET['t'] ?? $_POST['t'] ?? ''));
$data = googa_load_data();
$user = googa_find_user_by_password_token($data, $token);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_array($user)) {
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm'] ?? '');
    if (strlen($password) < 10) {
        $error = 'Furaha sirta ahi waa inuu ka koobnaadaa ugu yaraan 10 xaraf.';
    } elseif (!hash_equals($password, $confirm)) {
        $error = 'Labada fure isku mid ma aha.';
    } elseif (googa_set_password($data, (string)$user['email'], $password)) {
        googa_save_data($data);
        $fresh = $data['users'][(string)$user['email']];
        googa_login_user($fresh);
        header('Location: ./', true, 303);
        exit;
    }
}
?>
<!doctype html><html lang="so"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><title>Googa – furaha cusub</title><link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>"><style>body{display:grid;place-items:center;padding:max(12px,env(safe-area-inset-top)) 10px max(12px,env(safe-area-inset-bottom))}.reset{position:relative;isolation:isolate;overflow:hidden;width:min(100%,540px);margin:0;background:linear-gradient(145deg,#fffef9,#fff9e7 62%,#dbf4f4 135%);border:1px solid #ffffffd5;border-radius:31px;padding:27px;box-shadow:0 28px 70px #10365420}.reset:before{content:"";position:absolute;z-index:-1;width:230px;height:230px;right:-145px;top:-145px;border:40px solid #07858d10;border-radius:50%}.reset img{display:block;width:108px;height:108px;object-fit:contain;margin:auto;filter:drop-shadow(0 11px 11px #10365424)}.reset h1{text-align:center;margin:0 0 8px;font:800 clamp(34px,9vw,42px)/1.03 'Baloo 2',sans-serif;letter-spacing:-.03em}.reset p{line-height:1.5}.field{display:grid;gap:6px;margin-top:13px}.field label{font-size:13px;font-weight:900}.field input{min-height:50px;border:1px solid #cfdfe3;border-radius:14px;padding:10px 13px;font:inherit;background:#ffffffd9;box-shadow:inset 0 2px 4px #10365408}.field input:focus{border-color:#087f89;outline:3px solid #087f8918}.submit{width:100%;min-height:51px;margin-top:17px;border:0;border-radius:15px;background:linear-gradient(145deg,#128e96,#08727a);color:white;font-weight:900;font-size:16px;box-shadow:0 8px 20px #087f8930}.error{padding:11px;border:1px solid #efb3ad;border-radius:13px;background:#ffe1df}.expired{text-align:center}.expired p{padding:13px;border-radius:16px;background:#ffffffa8}.expired a{display:inline-flex;align-items:center;justify-content:center;min-height:49px;background:linear-gradient(145deg,#174565,#0c304c);color:#fff;text-decoration:none;padding:13px 18px;border-radius:14px;font-weight:900;box-shadow:0 8px 19px #10365428}@media(max-width:540px){.reset{padding:21px 17px;border-radius:26px}.reset img{width:94px;height:94px}}</style><main class="reset"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa"><?php if (!is_array($user)): ?><div class="expired"><p class="eyebrow">GOOGA</p><h1>Xiriirku wuu dhacay</h1><p data-speak-so="Xiriirku wuu dhacay. Xiriirkan lama isticmaali karo ama waqtigiisii ayaa dhammaaday. Codso xiriir cusub." data-speak-audio="audio/ui/password-expired.mp3">Xiriirkan lama isticmaali karo ama waqtigiisii ayaa dhammaaday.</p><a href="./">Codso xiriir cusub</a></div><?php else: ?><p class="eyebrow" style="text-align:center">GOOGA</p><h1>Samee furaha cusub</h1><p data-speak-so="Samee furaha cusub. Dooro fure gaar ah oo ka kooban ugu yaraan toban xaraf." data-speak-audio="audio/ui/password-new.mp3">Dooro fure gaar ah oo ka kooban ugu yaraan 10 xaraf.</p><form method="post"><input type="hidden" name="t" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"><div class="field"><label for="password">Furaha cusub</label><input id="password" name="password" type="password" minlength="10" autocomplete="new-password" required></div><div class="field"><label for="confirm">Ku celi furaha</label><input id="confirm" name="confirm" type="password" minlength="10" autocomplete="new-password" required></div><button class="submit" type="submit">Kaydi oo gal Googa</button></form><?php if ($error !== ''): ?><p class="error" data-speak-so="<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?><?php endif; ?></main><script src="<?= htmlspecialchars(googa_asset_url('assets/read-aloud.js'), ENT_QUOTES, 'UTF-8') ?>"></script></html>
