<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/store.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
header('Referrer-Policy: no-referrer');

$context = googa_session_context();
if (empty($context['authenticated']) || ($context['email'] ?? '') === '' || empty($context['access']['allowed'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Family access unavailable']);
    exit;
}
if (empty($_SESSION['googa_family_csrf'])) {
    $_SESSION['googa_family_csrf'] = bin2hex(random_bytes(24));
}
$csrf = (string)$_SESSION['googa_family_csrf'];
$action = (string)($_GET['action'] ?? 'status');
if (!in_array($action, ['status', 'rotate', 'remove', 'approve', 'reject'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Unknown action']);
    exit;
}
if ($action !== 'status' && ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($csrf, (string)($_POST['csrf'] ?? '')))) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$data = googa_load_data();
$user = googa_ensure_user($data, (string)$context['email'], (string)$context['name']);
$family = googa_family_data($user);
$changed = false;
if ($family['pairing_version'] === '') {
    $family['pairing_version'] = googa_family_new_pairing_version();
    $changed = true;
}
$beforePending = count($family['pending_devices']);
$family['pending_devices'] = array_values(array_filter($family['pending_devices'], static fn(array $request): bool => googa_is_future($request['expires_at'] ?? null)));
$changed = $changed || $beforePending !== count($family['pending_devices']);

if ($action === 'rotate') {
    $family['pairing_version'] = googa_family_new_pairing_version();
    $family['pending_devices'] = [];
    $changed = true;
}
if ($action === 'remove') {
    $idHash = trim((string)($_POST['id'] ?? ''));
    $oldCount = count($family['devices']);
    $family['devices'] = array_values(array_filter($family['devices'], static fn(array $device): bool => !hash_equals((string)($device['id_hash'] ?? ''), $idHash)));
    $changed = $changed || $oldCount !== count($family['devices']);
}
if (in_array($action, ['approve', 'reject'], true)) {
    $requestHash = trim((string)($_POST['id'] ?? ''));
    $selected = null;
    $remaining = [];
    foreach ($family['pending_devices'] as $request) {
        if ($selected === null && hash_equals((string)($request['request_hash'] ?? ''), $requestHash)) {
            $selected = $request;
            continue;
        }
        $remaining[] = $request;
    }
    if (is_array($selected)) {
        $family['pending_devices'] = $remaining;
        if ($action === 'approve') {
            $family['devices'][] = [
                'id_hash' => (string)$selected['device_hash'],
                'label' => 'Qalabka carruurta ' . (count($family['devices']) + 1),
                'joined_at' => googa_now(),
            ];
            usort($family['devices'], static fn(array $a, array $b): int => strcmp((string)($a['joined_at'] ?? ''), (string)($b['joined_at'] ?? '')));
            while (count($family['devices']) > 3) {
                array_shift($family['devices']);
            }
        }
        $changed = true;
    }
}
if ($changed) {
    $user['family'] = $family;
    googa_write_user($data, $user);
    googa_save_data($data);
}
$devices = array_map(static fn(array $device): array => ['id' => (string)($device['id_hash'] ?? ''), 'label' => (string)($device['label'] ?? 'Qalab'), 'joinedAt' => (string)($device['joined_at'] ?? '')], $family['devices']);
$pending = array_map(static fn(array $request): array => ['id' => (string)($request['request_hash'] ?? ''), 'label' => 'Qalab cusub', 'requestedAt' => (string)($request['requested_at'] ?? '')], $family['pending_devices']);
echo json_encode([
    'ok' => true,
    'scanUrl' => GOOGA_PUBLIC_BASE_URL . '/family-join.php?t=' . rawurlencode(googa_family_pairing_token((string)$context['email'], $family['pairing_version'])),
    'devices' => $devices,
    'pending' => $pending,
    'maxDevices' => 3,
    'csrf' => $csrf,
]);
