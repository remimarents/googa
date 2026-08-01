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
if (!in_array($action, ['status', 'create', 'remove'], true)) {
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
if ($action === 'create') {
    $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    $family['pairing_token_hash'] = hash('sha256', $token);
    $family['pairing_expires_at'] = gmdate('c', time() + (15 * 60));
    $user['family'] = $family;
    googa_write_user($data, $user);
    googa_save_data($data);
    echo json_encode([
        'ok' => true,
        'scanUrl' => GOOGA_PUBLIC_BASE_URL . '/family-join.php?t=' . rawurlencode($token),
        'expiresAt' => $family['pairing_expires_at'],
        'devices' => count($family['devices']),
    ]);
    exit;
}
if ($action === 'remove') {
    $idHash = trim((string)($_POST['id'] ?? ''));
    $family['devices'] = array_values(array_filter($family['devices'], static fn(array $device): bool => !hash_equals((string)($device['id_hash'] ?? ''), $idHash)));
    $user['family'] = $family;
    googa_write_user($data, $user);
    googa_save_data($data);
}
$devices = array_map(static fn(array $device): array => [
    'id' => (string)($device['id_hash'] ?? ''),
    'label' => (string)($device['label'] ?? 'Qalab'),
    'joinedAt' => (string)($device['joined_at'] ?? ''),
], $family['devices']);
echo json_encode(['ok' => true, 'devices' => $devices, 'maxDevices' => 3, 'csrf' => $csrf]);
