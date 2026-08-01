<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/store.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

$ownerEmail = googa_normalize_email((string)($_SESSION['googa_family_pending_owner'] ?? ''));
$requestId = trim((string)($_SESSION['googa_family_pending_request'] ?? ''));
$deviceId = trim((string)($_SESSION['googa_family_pending_device'] ?? ''));
$data = googa_load_data();
$owner = $ownerEmail !== '' ? ($data['users'][$ownerEmail] ?? null) : null;
if (!is_array($owner) || $requestId === '' || $deviceId === '') {
    echo json_encode(['state' => 'denied']);
    exit;
}
if (googa_family_device_is_valid($owner, $deviceId) && googa_access_state($owner, 'paid')['allowed']) {
    unset($_SESSION['googa_family_pending_owner'], $_SESSION['googa_family_pending_request'], $_SESSION['googa_family_pending_device']);
    $_SESSION['googa_family_owner'] = $ownerEmail;
    $_SESSION['googa_family_device'] = $deviceId;
    echo json_encode(['state' => 'approved']);
    exit;
}
$requestHash = hash('sha256', $requestId);
$deviceHash = hash('sha256', $deviceId);
foreach (googa_family_data($owner)['pending_devices'] as $request) {
    if (googa_is_future($request['expires_at'] ?? null) && hash_equals((string)($request['request_hash'] ?? ''), $requestHash) && hash_equals((string)($request['device_hash'] ?? ''), $deviceHash)) {
        echo json_encode(['state' => 'pending']);
        exit;
    }
}
echo json_encode(['state' => 'denied']);
