<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

function googa_now(): string
{
    return gmdate('c');
}

function googa_normalize_email(string $email): string
{
    return strtolower(trim($email));
}

function googa_storage_bootstrap(): void
{
    $dir = dirname(GOOGA_STORAGE_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (!is_file(GOOGA_STORAGE_FILE)) {
        file_put_contents(GOOGA_STORAGE_FILE, json_encode(googa_default_data(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        @chmod(GOOGA_STORAGE_FILE, 0644);
    }
}

function googa_default_user(string $email, string $name = ''): array
{
    $role = in_array($email, GOOGA_OWNER_EMAILS, true) ? 'owner' : 'user';
    return [
        'email' => $email,
        'name' => $name,
        'role' => $role,
        'created_at' => googa_now(),
        'updated_at' => googa_now(),
        'password_hash' => null,
        'password_set_at' => null,
        'password_reset_hash' => null,
        'password_reset_expires_at' => null,
        'password_reset_requested_at' => null,
        'discount_percent' => 0,
        'trial_ends_at' => null,
        'access_override_until' => null,
        'access_override_reason' => null,
        'stripe' => [
            'product_id' => GOOGA_PRODUCT_ID,
            'monthly_price_id' => GOOGA_MONTHLY_PRICE_ID,
            'intro_price_id' => GOOGA_INTRO_PRICE_ID,
            'customer_id' => null,
            'subscription_id' => null,
            'checkout_session_id' => null,
            'subscription_status' => 'none',
            'current_period_ends_at' => null,
            'coupon_id' => null,
            'promo_code_id' => null,
        ],
        'family' => [
            'pairing_version' => null,
            'devices' => [],
            'pending_devices' => [],
        ],
        'notes' => '',
    ];
}

function googa_default_data(): array
{
    $users = [];
    foreach (GOOGA_OWNER_EMAILS as $email) {
        $users[$email] = googa_default_user($email);
    }
    return [
        'users' => $users,
        'updated_at' => googa_now(),
    ];
}

function googa_load_data(): array
{
    googa_storage_bootstrap();
    $raw = file_get_contents(GOOGA_STORAGE_FILE);
    $data = json_decode(is_string($raw) ? $raw : '', true);
    if (!is_array($data)) {
        $data = googa_default_data();
    }
    if (!isset($data['users']) || !is_array($data['users'])) {
        $data['users'] = [];
    }
    foreach (GOOGA_OWNER_EMAILS as $email) {
        if (!isset($data['users'][$email])) {
            $data['users'][$email] = googa_default_user($email);
        }
    }
    return $data;
}

function googa_save_data(array $data): void
{
    $data['updated_at'] = googa_now();
    file_put_contents(GOOGA_STORAGE_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    @chmod(GOOGA_STORAGE_FILE, 0644);
}

function googa_user_has_password(array $user): bool
{
    return str_starts_with((string)($user['password_hash'] ?? ''), '$');
}

function googa_verify_password(array $user, string $password): bool
{
    $hash = (string)($user['password_hash'] ?? '');
    return $hash !== '' && password_verify($password, $hash);
}

function googa_set_password(array &$data, string $email, string $password): bool
{
    $email = googa_normalize_email($email);
    if (strlen($password) < 10 || !isset($data['users'][$email]) || !is_array($data['users'][$email])) {
        return false;
    }
    $user = $data['users'][$email];
    $user['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    $user['password_set_at'] = googa_now();
    $user['password_reset_hash'] = null;
    $user['password_reset_expires_at'] = null;
    $user['password_reset_requested_at'] = null;
    googa_write_user($data, $user);
    return true;
}

function googa_create_password_token(array &$data, string $email, bool $ignoreRateLimit = false): ?string
{
    $email = googa_normalize_email($email);
    if (!isset($data['users'][$email]) || !is_array($data['users'][$email])) {
        return null;
    }
    $user = $data['users'][$email];
    $last = strtotime((string)($user['password_reset_requested_at'] ?? '')) ?: 0;
    if (!$ignoreRateLimit && $last > time() - 60) {
        return null;
    }
    $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    $user['password_reset_hash'] = hash('sha256', $token);
    $user['password_reset_expires_at'] = gmdate('c', time() + 3600);
    $user['password_reset_requested_at'] = googa_now();
    googa_write_user($data, $user);
    return $token;
}

function googa_find_user_by_password_token(array $data, string $token): ?array
{
    if (!preg_match('/^[A-Za-z0-9_-]{40,60}$/', $token)) {
        return null;
    }
    $hash = hash('sha256', $token);
    foreach ($data['users'] as $user) {
        if (!is_array($user) || !googa_is_future($user['password_reset_expires_at'] ?? null)) {
            continue;
        }
        if (hash_equals((string)($user['password_reset_hash'] ?? ''), $hash)) {
            return $user;
        }
    }
    return null;
}

function googa_login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['googa_email'] = googa_normalize_email((string)($user['email'] ?? ''));
    $_SESSION['googa_name'] = (string)($user['name'] ?? '');
    unset($_SESSION['googa_mode'], $_SESSION['googa_family_owner'], $_SESSION['googa_family_device']);
}

function googa_ensure_user(array &$data, string $email, string $name = ''): array
{
    $email = googa_normalize_email($email);
    if ($email === '') {
        return googa_default_user('');
    }
    if (!isset($data['users'][$email]) || !is_array($data['users'][$email])) {
        $data['users'][$email] = googa_default_user($email, $name);
    }
    if ($name !== '' && empty($data['users'][$email]['name'])) {
        $data['users'][$email]['name'] = $name;
    }
    if (in_array($email, GOOGA_OWNER_EMAILS, true)) {
        $data['users'][$email]['role'] = 'owner';
    }
    $data['users'][$email]['updated_at'] = googa_now();
    return $data['users'][$email];
}

function googa_write_user(array &$data, array $user): void
{
    $email = googa_normalize_email((string)($user['email'] ?? ''));
    if ($email === '') {
        return;
    }
    $user['email'] = $email;
    if (in_array($email, GOOGA_OWNER_EMAILS, true)) {
        $user['role'] = 'owner';
    }
    $user['updated_at'] = googa_now();
    $data['users'][$email] = $user;
}

function googa_date_or_null(?string $value): ?string
{
    $value = trim((string)$value);
    if ($value === '') {
        return null;
    }
    $timestamp = strtotime($value . ' 23:59:59 UTC');
    return $timestamp ? gmdate('c', $timestamp) : null;
}

function googa_is_future(?string $iso): bool
{
    if ($iso === null || $iso === '') {
        return false;
    }
    $ts = strtotime($iso);
    return $ts !== false && $ts >= time();
}

function googa_access_state(array $user, string $mode = 'paid'): array
{
    $role = (string)($user['role'] ?? 'user');
    if ($role === 'owner' && $mode === 'demo') {
        return ['allowed' => true, 'source' => 'owner_demo', 'label' => 'Owner demo'];
    }
    if ($role === 'owner' && $mode === 'paid') {
        return ['allowed' => true, 'source' => 'owner_paid', 'label' => 'Owner paid'];
    }
    if (googa_is_future($user['access_override_until'] ?? null)) {
        return ['allowed' => true, 'source' => 'override', 'label' => 'Manual override'];
    }
    $stripe = is_array($user['stripe'] ?? null) ? $user['stripe'] : [];
    $status = (string)($stripe['subscription_status'] ?? 'none');
    if (in_array($status, ['active', 'trialing'], true)) {
        return ['allowed' => true, 'source' => 'stripe', 'label' => 'Stripe ' . $status];
    }
    if (googa_is_future($user['trial_ends_at'] ?? null)) {
        return ['allowed' => true, 'source' => 'trial', 'label' => 'Trial'];
    }
    return ['allowed' => false, 'source' => 'none', 'label' => 'No active access'];
}

function googa_family_data(array $user): array
{
    $family = is_array($user['family'] ?? null) ? $user['family'] : [];
    $devices = is_array($family['devices'] ?? null) ? $family['devices'] : [];
    $pending = is_array($family['pending_devices'] ?? null) ? $family['pending_devices'] : [];
    return [
        'pairing_version' => (string)($family['pairing_version'] ?? ''),
        'devices' => array_values(array_filter($devices, 'is_array')),
        'pending_devices' => array_values(array_filter($pending, 'is_array')),
    ];
}

function googa_family_secret(): string
{
    static $secret = null;
    if (is_string($secret)) {
        return $secret;
    }
    $secret = is_file(GOOGA_FAMILY_SECRET_FILE) ? trim((string)file_get_contents(GOOGA_FAMILY_SECRET_FILE)) : '';
    if (strlen($secret) < 32) {
        throw new RuntimeException('Family QR is not configured.');
    }
    return $secret;
}

function googa_family_pairing_token(string $email, string $version): string
{
    return rtrim(strtr(base64_encode(hash_hmac('sha256', googa_normalize_email($email) . '|' . $version, googa_family_secret(), true)), '+/', '-_'), '=');
}

function googa_family_new_pairing_version(): string
{
    return bin2hex(random_bytes(16));
}

function googa_family_device_is_valid(array $user, string $deviceId): bool
{
    if ($deviceId === '') {
        return false;
    }
    $expected = hash('sha256', $deviceId);
    foreach (googa_family_data($user)['devices'] as $device) {
        if (hash_equals((string)($device['id_hash'] ?? ''), $expected)) {
            return true;
        }
    }
    return false;
}

function googa_session_context(): array
{
    $email = googa_normalize_email((string)($_SESSION['googa_email'] ?? ''));
    $name = trim((string)($_SESSION['googa_name'] ?? ''));
    $mode = (string)($_SESSION['googa_mode'] ?? '');
    $familyOwner = googa_normalize_email((string)($_SESSION['googa_family_owner'] ?? ''));
    $familyDevice = trim((string)($_SESSION['googa_family_device'] ?? ''));
    $data = googa_load_data();
    $user = $email !== '' ? googa_ensure_user($data, $email, $name) : null;
    if ($email !== '') {
        googa_save_data($data);
    }
    $role = is_array($user) ? (string)($user['role'] ?? 'user') : 'guest';
    if ($email === '' && $familyOwner !== '' && isset($data['users'][$familyOwner]) && is_array($data['users'][$familyOwner])) {
        $owner = $data['users'][$familyOwner];
        if (googa_family_device_is_valid($owner, $familyDevice)) {
            return [
                'authenticated' => true,
                'email' => '',
                'account_email' => $familyOwner,
                'name' => 'Qoyska Googa',
                'role' => 'child',
                'mode' => 'family',
                'user' => $owner,
                'access' => googa_access_state($owner, 'paid'),
                'family_device' => true,
            ];
        }
        unset($_SESSION['googa_family_owner'], $_SESSION['googa_family_device']);
    }
    return [
        'authenticated' => $email !== '',
        'email' => $email,
        'account_email' => $email,
        'name' => $name,
        'role' => $role,
        'mode' => $mode,
        'user' => $user,
        'access' => is_array($user) ? googa_access_state($user, $mode === '' ? 'paid' : $mode) : ['allowed' => false, 'source' => 'guest', 'label' => 'Guest'],
        'family_device' => false,
    ];
}

function googa_require_owner(array $context): void
{
    if (($context['role'] ?? '') !== 'owner') {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}
