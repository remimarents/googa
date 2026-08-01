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
<!doctype html><html lang="so"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><title>Googa – furaha cusub</title><link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>"><style>.reset{width:min(100% - 20px,520px);margin:clamp(12px,8vh,70px) auto;background:#fffdf7;border-radius:26px;padding:24px;box-shadow:var(--shadow)}.reset img{display:block;width:92px;height:92px;object-fit:contain;margin:auto}.reset h1{text-align:center;margin:0 0 8px;font:800 36px/1.05 'Baloo 2',sans-serif}.reset p{line-height:1.45}.field{display:grid;gap:5px;margin-top:12px}.field label{font-weight:800}.field input{min-height:48px;border:2px solid #d3e2e5;border-radius:13px;padding:10px 12px;font:inherit}.submit{width:100%;min-height:49px;margin-top:15px;border:0;border-radius:14px;background:#087f89;color:white;font-weight:800;font-size:16px}.error{padding:11px;border-radius:13px;background:#ffe1df}.expired{text-align:center}.expired a{display:inline-block;background:#103654;color:#fff;text-decoration:none;padding:13px 18px;border-radius:14px;font-weight:800}</style><main class="reset"><img src="<?= htmlspecialchars(googa_asset_url('assets/googa-mascot.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Googa"><?php if (!is_array($user)): ?><div class="expired"><h1>Xiriirku wuu dhacay</h1><p>Xiriirkan lama isticmaali karo ama waqtigiisii ayaa dhammaaday.</p><a href="./">Codso xiriir cusub</a></div><?php else: ?><h1>Samee furaha cusub</h1><p>Dooro fure gaar ah oo ka kooban ugu yaraan 10 xaraf.</p><form method="post"><input type="hidden" name="t" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"><div class="field"><label for="password">Furaha cusub</label><input id="password" name="password" type="password" minlength="10" autocomplete="new-password" required></div><div class="field"><label for="confirm">Ku celi furaha</label><input id="confirm" name="confirm" type="password" minlength="10" autocomplete="new-password" required></div><button class="submit" type="submit">Kaydi oo gal Googa</button></form><?php if ($error !== ''): ?><p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?><?php endif; ?></main></html>
