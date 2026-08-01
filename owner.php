<?php
declare(strict_types=1);

session_name('googa');
session_start();

require_once __DIR__ . '/lib/store.php';
require_once __DIR__ . '/lib/version.php';
require_once __DIR__ . '/lib/stripe.php';

$context = googa_session_context();
googa_require_owner($context);

if (empty($_SESSION['googa_csrf'])) {
    $_SESSION['googa_csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['googa_csrf'];
$message = '';

$data = googa_load_data();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = (string)($_POST['csrf'] ?? '');
    if (!hash_equals($csrf, $postedToken)) {
        http_response_code(400);
        exit('Invalid request');
    }
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'create-ambassador') {
        try {
            $promo = googa_stripe_create_ambassador((string)($_POST['ambassador_name'] ?? ''), (string)($_POST['ambassador_code'] ?? ''), (int)($_POST['ambassador_percent'] ?? 20));
            $data['ambassadors'][(string)$promo['id']] = [
                'name' => trim((string)($_POST['ambassador_name'] ?? '')),
                'code' => (string)($promo['code'] ?? ''),
                'percent' => max(1, min(50, (int)($_POST['ambassador_percent'] ?? 20))),
                'promotion_code_id' => (string)$promo['id'],
                'coupon_id' => (string)(($promo['coupon']['id'] ?? $promo['coupon'] ?? '')),
                'created_at' => googa_now(),
            ];
            googa_save_data($data);
            $message = 'Ambassadørkode opprettet i Stripe.';
        } catch (Throwable $error) {
            $message = 'Kunne ikke opprette kode: ' . $error->getMessage();
        }
    }
    $email = googa_normalize_email((string)($_POST['email'] ?? ''));
    if ($email !== '') {
        $user = googa_ensure_user($data, $email, (string)($_POST['name'] ?? ''));
        $stripe = is_array($user['stripe'] ?? null) ? $user['stripe'] : [];
        if ($action === 'save-user') {
            $user['name'] = trim((string)($_POST['name'] ?? ''));
            $user['role'] = in_array($email, GOOGA_OWNER_EMAILS, true) ? 'owner' : 'user';
            $user['discount_percent'] = max(0, min(100, (int)($_POST['discount_percent'] ?? 0)));
            $user['trial_ends_at'] = googa_date_or_null((string)($_POST['trial_ends_at'] ?? ''));
            $user['access_override_until'] = googa_date_or_null((string)($_POST['access_override_until'] ?? ''));
            $user['access_override_reason'] = trim((string)($_POST['access_override_reason'] ?? ''));
            $user['notes'] = trim((string)($_POST['notes'] ?? ''));
            $user['stripe'] = $stripe;
            googa_write_user($data, $user);
            $message = 'Bruker lagret.';
        }
        if ($action === 'quick-discount') {
            $user['discount_percent'] = max(0, min(100, (int)($_POST['percent'] ?? 0)));
            googa_write_user($data, $user);
            $message = 'Rabatt oppdatert.';
        }
        if ($action === 'grant-30-days') {
            $user['access_override_until'] = gmdate('c', strtotime('+30 days 23:59:59 UTC'));
            $user['access_override_reason'] = 'Owner grant 30 days';
            googa_write_user($data, $user);
            $message = '30 dagers tilgang aktivert.';
        }
        if ($action === 'clear-override') {
            $user['access_override_until'] = null;
            $user['access_override_reason'] = null;
            googa_write_user($data, $user);
            $message = 'Manuell tilgang fjernet.';
        }
        googa_save_data($data);
    }
}

$data = googa_load_data();
$users = $data['users'];
ksort($users);
$selectedEmail = googa_normalize_email((string)($_GET['email'] ?? ''));
$selected = $selectedEmail !== '' && isset($users[$selectedEmail]) ? $users[$selectedEmail] : googa_default_user('');
$selectedStripe = is_array($selected['stripe'] ?? null) ? $selected['stripe'] : [];
$ambassadors = is_array($data['ambassadors'] ?? null) ? $data['ambassadors'] : [];

function googa_form_date(?string $iso): string
{
    if ($iso === null || $iso === '') {
        return '';
    }
    $ts = strtotime($iso);
    return $ts ? gmdate('Y-m-d', $ts) : '';
}
?><!doctype html>
<html lang="no">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Googa – eierdashbord</title>
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('styles.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(googa_asset_url('owner-commerce.css'), ENT_QUOTES, 'UTF-8') ?>">
<style>
.owner-shell{width:min(1100px,100%);margin:0 auto;padding:20px max(16px,env(safe-area-inset-right)) 40px max(16px,env(safe-area-inset-left))}
.owner-header{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.owner-header h1{margin:2px 0 0;font:800 clamp(32px,5vw,46px)/1.03 'Baloo 2',Nunito,system-ui,sans-serif;letter-spacing:-.02em}.owner-header .muted{margin:6px 0 0}
.owner-back{font-weight:800;color:#103654;text-decoration:none}
.owner-grid{display:grid;grid-template-columns:1.2fr .9fr;gap:18px;margin-top:18px}
.panel{background:#fffdf7;border-radius:26px;padding:20px;box-shadow:0 16px 35px #1036541b}
.panel h2{margin:0 0 14px;font:800 28px 'Baloo 2',Nunito,system-ui,sans-serif;color:#103654}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:10px 8px;border-bottom:1px solid #e5edf4;text-align:left;vertical-align:top}
.table th{font-size:13px;text-transform:uppercase;color:#5b7185}
.pill{display:inline-block;padding:4px 10px;border-radius:999px;background:#e5f7f7;font-weight:800;font-size:12px}
.pill.owner{background:#f6c5db}
.pill.ok{background:#c8f4de}
.pill.no{background:#ffe0dc}
.quick{display:flex;gap:6px;flex-wrap:wrap}
.quick form{margin:0}
.quick button,.save{min-height:44px;border:0;border-radius:12px;background:#103654;color:#fff;padding:8px 12px;font-weight:800}
.quick .alt{background:#0b8691}
.quick .muted{background:#8ca0b3;color:#fff}
.field{display:grid;gap:6px;margin-bottom:12px}
.field input,.field select,.field textarea{width:100%;border:2px solid #d7e3ee;border-radius:12px;padding:10px 12px;font:inherit}
.field textarea{min-height:84px;resize:vertical}
.message{margin:0 0 12px;padding:10px 12px;border-radius:12px;background:#e5f8ec;font-weight:800}
@media (max-width:900px){.owner-grid{grid-template-columns:1fr}}
@media (max-width:620px){.owner-shell{padding-top:14px}.owner-header{align-items:flex-start}.owner-header h1{font-size:34px}.owner-header .quick{width:100%;justify-content:space-between}.owner-back{min-height:44px;display:inline-flex;align-items:center}.owner-grid{margin-top:12px}.panel{padding:16px;border-radius:22px}.table thead{display:none}.table,.table tbody,.table tr,.table td{display:block;width:100%}.table tbody{display:grid;gap:12px}.table tr{padding:12px;border:1px solid #d8e5eb;border-radius:16px;background:#fff}.table td{display:grid;grid-template-columns:82px minmax(0,1fr);gap:10px;padding:7px 0;border:0;overflow-wrap:anywhere}.table td::before{content:attr(data-label);color:#526b80;font-size:11px;font-weight:800;letter-spacing:.04em;text-transform:uppercase}.table td:first-child a{display:inline-flex;align-items:center;min-height:40px}.table td:last-child{grid-template-columns:1fr;padding-top:10px;border-top:1px solid #e5edf4}.table td:last-child::before{margin-bottom:2px}.table .quick{gap:8px}.table .quick button{min-width:54px}.field input,.field select,.field textarea{min-height:48px}.save{width:100%}}
</style>
<style>
.owner-header{padding:18px 20px;border:1px solid #ffffffc9;border-radius:24px;background:#ffffffa8;box-shadow:0 12px 28px #10365413;backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px)}.owner-header .eyebrow{display:inline-flex;margin:0;padding:5px 10px;border-radius:999px;background:#103654;color:#fff;font-size:10px;letter-spacing:.13em}.owner-header .quick{padding:6px;border-radius:15px;background:#ffffffad}.owner-back{display:inline-flex;align-items:center;min-height:42px;padding:8px 12px;border-radius:11px;background:#e5f7f7;text-decoration:none}.panel{border:1px solid #ffffffd2;background:linear-gradient(145deg,#fffef9,#fffdf5);box-shadow:0 18px 42px #10365418}.panel h2{padding-bottom:10px;border-bottom:1px solid #10365410}.field input,.field select,.field textarea{border:1px solid #d0dfe4;background:#fff;box-shadow:inset 0 2px 4px #10365408}.field input:focus,.field select:focus,.field textarea:focus{border-color:#087f89;outline:3px solid #087f8918}.quick button,.save{box-shadow:0 5px 13px #10365420;transition:transform .15s}.quick button:hover,.save:hover{transform:translateY(-1px)}
@media(max-width:620px){.owner-header{padding:15px;border-radius:20px}.owner-header .quick{padding:4px}.panel{border-radius:20px}.owner-header h1{letter-spacing:-.025em}}
</style>
<main class="owner-shell">
  <div class="owner-header">
    <div>
      <p class="eyebrow">OWNER DASHBOARD</p>
      <h1>Rabatter, tilgang og brukerstatus</h1>
      <p class="muted"><?= htmlspecialchars($context['email'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="quick">
      <a class="owner-back" href="./">Til appen</a>
      <a class="owner-back" href="owner-mode.php">Bytt modus</a>
    </div>
  </div>
  <?php if ($message !== ''): ?><p class="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  <div class="owner-grid">
    <section class="panel">
      <h2>Brukere</h2>
      <table class="table">
        <thead>
          <tr>
            <th>E-post</th>
            <th>Rolle</th>
            <th>Tilgang</th>
            <th>Rabatt</th>
            <th>Hurtigvalg</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $email => $user): $access = googa_access_state($user, 'paid'); ?>
          <tr>
            <td data-label="E-post">
              <a href="?email=<?= urlencode($email) ?>"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></a><br>
              <small><?= htmlspecialchars((string)($user['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small>
            </td>
            <td data-label="Rolle"><span class="pill <?= ($user['role'] ?? 'user') === 'owner' ? 'owner' : '' ?>"><?= htmlspecialchars((string)($user['role'] ?? 'user'), ENT_QUOTES, 'UTF-8') ?></span></td>
            <td data-label="Tilgang"><span class="pill <?= $access['allowed'] ? 'ok' : 'no' ?>"><?= htmlspecialchars($access['label'], ENT_QUOTES, 'UTF-8') ?></span></td>
            <td data-label="Rabatt"><?= (int)($user['discount_percent'] ?? 0) ?>%</td>
            <td data-label="Hurtigvalg">
              <div class="quick">
                <?php foreach ([10,25,50] as $percent): ?>
                <form method="post">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="action" value="quick-discount">
                  <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="percent" value="<?= $percent ?>">
                  <button type="submit"><?= $percent ?>%</button>
                </form>
                <?php endforeach; ?>
                <form method="post">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="action" value="grant-30-days">
                  <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
                  <button class="alt" type="submit">30 dager</button>
                </form>
                <form method="post">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="action" value="clear-override">
                  <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
                  <button class="muted" type="submit">Nullstill</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
    <section class="panel">
      <h2>Rediger bruker</h2>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="action" value="save-user">
        <div class="field">
          <label for="email">E-post</label>
          <input id="email" name="email" type="email" value="<?= htmlspecialchars((string)($selected['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="field">
          <label for="name">Navn</label>
          <input id="name" name="name" value="<?= htmlspecialchars((string)($selected['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="field">
          <label for="role_view">Rolle</label>
          <input id="role_view" value="<?= htmlspecialchars(in_array((string)($selected['email'] ?? ''), GOOGA_OWNER_EMAILS, true) ? 'owner' : 'user', ENT_QUOTES, 'UTF-8') ?>" readonly>
        </div>
        <div class="field">
          <label for="discount_percent">Rabattnivå</label>
          <select id="discount_percent" name="discount_percent">
            <?php foreach ([0,10,25,50,100] as $percent): ?>
            <option value="<?= $percent ?>"<?= ((int)($selected['discount_percent'] ?? 0) === $percent) ? ' selected' : '' ?>><?= $percent ?>%</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label>Stripe-status</label>
          <input value="<?= htmlspecialchars((string)($selectedStripe['subscription_status'] ?? 'none'), ENT_QUOTES, 'UTF-8') ?>" readonly>
          <small>Oppdateres automatisk fra Stripe. Bruk «Manuell tilgang» for en tidsavgrenset gave.</small>
        </div>
        <div class="field">
          <label for="trial_ends_at">Trial slutter</label>
          <input id="trial_ends_at" name="trial_ends_at" type="date" value="<?= htmlspecialchars(googa_form_date($selected['trial_ends_at'] ?? null), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="field">
          <label for="access_override_until">Manuell tilgang til</label>
          <input id="access_override_until" name="access_override_until" type="date" value="<?= htmlspecialchars(googa_form_date($selected['access_override_until'] ?? null), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="field">
          <label for="access_override_reason">Årsak til manuell tilgang</label>
          <input id="access_override_reason" name="access_override_reason" value="<?= htmlspecialchars((string)($selected['access_override_reason'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="field">
          <label>Stripe-kunde</label>
          <input value="<?= htmlspecialchars((string)($selectedStripe['customer_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" readonly>
        </div>
        <div class="field">
          <label for="notes">Notater</label>
          <textarea id="notes" name="notes"><?= htmlspecialchars((string)($selected['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
        <button class="save" type="submit">Lagre bruker</button>
      </form>
    </section>
  </div>
  <section class="panel" style="margin-top:18px">
    <h2>Ambassadører og sporbare rabattkoder</h2>
    <p>Hver kode gir avtalt rabatt på Googa de tre første månedene. Stripe teller innløsningene; opprett bare koder etter at vilkår er avtalt.</p>
    <form method="post" class="ambassador-form">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="action" value="create-ambassador">
      <div class="field"><label for="ambassador_name">Navn</label><input id="ambassador_name" name="ambassador_name" required></div>
      <div class="field"><label for="ambassador_code">Kode</label><input id="ambassador_code" name="ambassador_code" minlength="4" pattern="[A-Za-z0-9]+" placeholder="NAVN20" required></div>
      <div class="field"><label for="ambassador_percent">Rabatt</label><select id="ambassador_percent" name="ambassador_percent"><?php foreach ([10,15,20,25] as $percent): ?><option value="<?= $percent ?>"<?= $percent === 20 ? ' selected' : '' ?>><?= $percent ?>%</option><?php endforeach; ?></select></div>
      <button class="save" type="submit">Opprett i Stripe</button>
    </form>
    <?php if ($ambassadors): ?><table class="table"><thead><tr><th>Navn</th><th>Kode</th><th>Rabatt</th><th>Stripe-ID</th></tr></thead><tbody><?php foreach ($ambassadors as $ambassador): ?><tr><td data-label="Navn"><?= htmlspecialchars((string)($ambassador['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td><td data-label="Kode"><strong><?= htmlspecialchars((string)($ambassador['code'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong></td><td data-label="Rabatt"><?= (int)($ambassador['percent'] ?? 0) ?>% · 3 mnd</td><td data-label="Stripe-ID"><small><?= htmlspecialchars((string)($ambassador['promotion_code_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small></td></tr><?php endforeach; ?></tbody></table><?php endif; ?>
  </section>
</main>
</html>
