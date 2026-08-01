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
    if (googa_is_future($family['pairing_expires_at']) && hash_equals($family['pairing_token_hash'], hash('sha256', $token))) {
        $ownerEmail = googa_normalize_email((string)$email);
        $owner = $candidate;
        break;
    }
}
if (!is_array($owner) || !googa_access_state($owner, 'paid')['allowed']) {
    http_response_code(403);
    exit('Koodhkan wuu dhacay ama rukhsaddu ma shaqaynayso.');
}

$currentOwner = googa_normalize_email((string)($_SESSION['googa_family_owner'] ?? ''));
$currentDevice = trim((string)($_SESSION['googa_family_device'] ?? ''));
if ($currentOwner === $ownerEmail && googa_family_device_is_valid($owner, $currentDevice)) {
    header('Location: ./', true, 303);
    exit;
}

$family = googa_family_data($owner);
$deviceId = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
$family['devices'][] = [
    'id_hash' => hash('sha256', $deviceId),
    'label' => 'Qalabka carruurta ' . (count($family['devices']) + 1),
    'joined_at' => googa_now(),
];
usort($family['devices'], static fn(array $a, array $b): int => strcmp((string)($a['joined_at'] ?? ''), (string)($b['joined_at'] ?? '')));
while (count($family['devices']) > 3) {
    array_shift($family['devices']);
}
$owner['family'] = $family;
googa_write_user($data, $owner);
googa_save_data($data);

session_regenerate_id(true);
unset($_SESSION['googa_email'], $_SESSION['googa_name'], $_SESSION['googa_mode']);
$_SESSION['googa_family_owner'] = $ownerEmail;
$_SESSION['googa_family_device'] = $deviceId;
header('Location: ./', true, 303);
exit;
