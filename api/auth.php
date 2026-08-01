<?php
declare(strict_types=1);

session_name('googa');
session_start();

require_once __DIR__ . '/../lib/store.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

$action = (string)($_GET['action'] ?? '');
$token = trim((string)($_GET['t'] ?? ''));
$plan = (string)($_GET['plan'] ?? '');
if (!in_array($plan, ['trial', 'monthly'], true)) {
    $plan = '';
}
if (!in_array($action, ['create', 'poll'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Unknown action']);
    exit;
}

// The public Googa API calls this operation "poll". The shared QR bridge
// exposes the same read-only operation as "status".
$bridgeAction = $action === 'poll' ? 'status' : $action;
$url = 'https://marents.no/vismalight/api/external-qr.php?app=googa&action=' . $bridgeAction . ($token !== '' ? '&t=' . rawurlencode($token) : '');
$ch = curl_init($url);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
$raw = curl_exec($ch);
$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$data = json_decode(is_string($raw) ? $raw : '', true);

if (!is_array($data) || $code >= 400) {
    http_response_code(502);
    echo json_encode(['error' => 'QR bridge unavailable']);
    exit;
}

if ($action === 'poll' && ($data['status'] ?? '') === 'approved') {
    $consume = 'https://marents.no/vismalight/api/external-qr.php?app=googa&action=consume&t=' . rawurlencode($token);
    $ch = curl_init($consume);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
    $used = json_decode((string)curl_exec($ch), true);
    curl_close($ch);
    if (!empty($used['ok'])) {
        $_SESSION['googa_email'] = googa_normalize_email((string)($data['email'] ?? ''));
        $_SESSION['googa_name'] = (string)($data['identity'] ?? '');
        if ($plan !== '') {
            $_SESSION['googa_checkout_choice'] = $plan;
        } else {
            unset($_SESSION['googa_checkout_choice']);
        }
        unset($_SESSION['googa_mode']);
        unset($_SESSION['googa_family_owner'], $_SESSION['googa_family_device']);
        $store = googa_load_data();
        googa_ensure_user($store, (string)$_SESSION['googa_email'], (string)$_SESSION['googa_name']);
        googa_save_data($store);
    }
}

echo json_encode([
    'token' => $data['token'] ?? null,
    'scanUrl' => $data['scanUrl'] ?? null,
    'state' => $data['status'] ?? 'pending',
    'expiresAt' => $data['expiresAt'] ?? null
]);
