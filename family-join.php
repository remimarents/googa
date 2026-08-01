<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/store.php';

header('Cache-Control: no-store, max-age=0');
header('Referrer-Policy: no-referrer');

$token = trim((string)($_GET['t'] ?? ''));
if (!preg_match('/^[A-Za-z0-9_-]{40,80}$/', $token)) {
    http_response_code(400);
    exit('Koodhkan lama aqbalin.');
}
$data = googa_load_data();
$ownerEmail = '';
$owner = null;
foreach ($data['users'] as $email => $candidate) {
    $family = googa_family_data($candidate);
    if ($family['pairing_version'] !== '' && hash_equals(googa_family_pairing_token((string)$email, $family['pairing_version']), $token)) {
        $ownerEmail = googa_normalize_email((string)$email);
        $owner = $candidate;
        break;
    }
}
if (!is_array($owner) || !googa_access_state($owner, 'paid')['allowed']) {
    http_response_code(403);
    exit('Koodhkani ma shaqaynayo hadda.');
}
$currentOwner = googa_normalize_email((string)($_SESSION['googa_family_owner'] ?? ''));
$currentDevice = trim((string)($_SESSION['googa_family_device'] ?? ''));
if ($currentOwner === $ownerEmail && googa_family_device_is_valid($owner, $currentDevice)) {
    header('Location: ./', true, 303);
    exit;
}

$family = googa_family_data($owner);
$deviceId = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
$requestId = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
$family['pending_devices'][] = [
    'request_hash' => hash('sha256', $requestId),
    'device_hash' => hash('sha256', $deviceId),
    'requested_at' => googa_now(),
    'expires_at' => gmdate('c', time() + (10 * 60)),
];
$owner['family'] = $family;
googa_write_user($data, $owner);
googa_save_data($data);

session_regenerate_id(true);
unset($_SESSION['googa_email'], $_SESSION['googa_name'], $_SESSION['googa_mode'], $_SESSION['googa_family_owner'], $_SESSION['googa_family_device']);
$_SESSION['googa_family_pending_owner'] = $ownerEmail;
$_SESSION['googa_family_pending_request'] = $requestId;
$_SESSION['googa_family_pending_device'] = $deviceId;
header('Location: family-pending.php', true, 303);
exit;
