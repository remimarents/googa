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
    file_put_contents(GOOGA_STORAGE_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    @chmod(GOOGA_STORAGE_FILE, 0644);
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

function googa_session_context(): array
{
    $email = googa_normalize_email((string)($_SESSION['googa_email'] ?? ''));
    $name = trim((string)($_SESSION['googa_name'] ?? ''));
    $mode = (string)($_SESSION['googa_mode'] ?? '');
    $data = googa_load_data();
    $user = $email !== '' ? googa_ensure_user($data, $email, $name) : null;
    if ($email !== '') {
        googa_save_data($data);
    }
    $role = is_array($user) ? (string)($user['role'] ?? 'user') : 'guest';
    return [
        'email' => $email,
        'name' => $name,
        'role' => $role,
        'mode' => $mode,
        'user' => $user,
        'access' => is_array($user) ? googa_access_state($user, $mode === '' ? 'paid' : $mode) : ['allowed' => false, 'source' => 'guest', 'label' => 'Guest'],
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
