<?php
declare(strict_types=1);

require_once __DIR__ . '/store.php';

function googa_stripe_config(): array
{
    static $config = null;
    if (is_array($config)) {
        return $config;
    }
    $config = is_file(GOOGA_STRIPE_ENV_FILE) ? parse_ini_file(GOOGA_STRIPE_ENV_FILE, false, INI_SCANNER_RAW) : false;
    if (!is_array($config) || empty($config['STRIPE_API_KEY']) || empty($config['STRIPE_WEBHOOK_SECRET'])) {
        throw new RuntimeException('Stripe is not configured.');
    }
    return $config;
}

function googa_stripe_request(string $method, string $path, array $params = []): array
{
    $config = googa_stripe_config();
    $url = 'https://api.stripe.com/v1/' . ltrim($path, '/');
    $curl = curl_init($url);
    if ($curl === false) {
        throw new RuntimeException('Unable to start Stripe request.');
    }
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => $config['STRIPE_API_KEY'] . ':',
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => ['Stripe-Version: 2025-04-30.basil'],
    ];
    if ($method === 'POST') {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = http_build_query($params, '', '&');
    }
    curl_setopt_array($curl, $options);
    $body = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $error = curl_error($curl);
    curl_close($curl);
    $decoded = is_string($body) ? json_decode($body, true) : null;
    if ($status < 200 || $status >= 300 || !is_array($decoded)) {
        error_log('Googa Stripe error: HTTP ' . $status . ' ' . $error);
        throw new RuntimeException('Stripe could not complete the request.');
    }
    return $decoded;
}

function googa_stripe_create_checkout(array $user, string $kind): array
{
    $email = googa_normalize_email((string)($user['email'] ?? ''));
    if ($email === '') {
        throw new RuntimeException('Missing account email.');
    }
    $discount = max(0, min(100, (int)($user['discount_percent'] ?? 0)));
    $params = [
        'mode' => 'subscription',
        'success_url' => GOOGA_PUBLIC_BASE_URL . '/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => GOOGA_PUBLIC_BASE_URL . '/access.php?payment=cancelled',
        'customer_email' => $email,
        'client_reference_id' => $email,
        'payment_method_collection' => 'always',
        'line_items[0][price]' => GOOGA_MONTHLY_PRICE_ID,
        'line_items[0][quantity]' => 1,
        'metadata[googa_email]' => $email,
        'metadata[googa_kind]' => $kind,
        'subscription_data[metadata][googa_email]' => $email,
        'subscription_data[metadata][googa_kind]' => $kind,
    ];
    if ($kind === 'trial') {
        $params['line_items[1][price]'] = GOOGA_INTRO_PRICE_ID;
        $params['line_items[1][quantity]'] = 1;
        $params['subscription_data[trial_period_days]'] = 2;
        $params['subscription_data[trial_settings][end_behavior][missing_payment_method]'] = 'cancel';
    }
    if ($discount > 0 && $discount < 100) {
        $coupon = googa_stripe_request('POST', 'coupons', [
            'percent_off' => $discount,
            'duration' => 'forever',
            'name' => 'Googa rabatt ' . $discount . '%',
            'metadata[googa_email]' => $email,
        ]);
        $params['discounts[0][coupon]'] = (string)$coupon['id'];
    }
    if ($discount === 100) {
        throw new RuntimeException('Full rabatt må gis fra eierpanelet som manuell tilgang.');
    }
    return googa_stripe_request('POST', 'checkout/sessions', $params);
}

function googa_stripe_find_user(array $data, string $email, string $customerId, string $subscriptionId): ?array
{
    $email = googa_normalize_email($email);
    if ($email !== '' && isset($data['users'][$email]) && is_array($data['users'][$email])) {
        return $data['users'][$email];
    }
    foreach ($data['users'] as $user) {
        $stripe = is_array($user['stripe'] ?? null) ? $user['stripe'] : [];
        if (($customerId !== '' && ($stripe['customer_id'] ?? '') === $customerId) || ($subscriptionId !== '' && ($stripe['subscription_id'] ?? '') === $subscriptionId)) {
            return $user;
        }
    }
    return null;
}

function googa_stripe_apply_subscription(array &$data, array $subscription, string $email = ''): bool
{
    $customerId = (string)($subscription['customer'] ?? '');
    $subscriptionId = (string)($subscription['id'] ?? '');
    $metadata = is_array($subscription['metadata'] ?? null) ? $subscription['metadata'] : [];
    $email = $email !== '' ? $email : (string)($metadata['googa_email'] ?? '');
    $user = googa_stripe_find_user($data, $email, $customerId, $subscriptionId);
    if (!is_array($user)) {
        return false;
    }
    $stripe = is_array($user['stripe'] ?? null) ? $user['stripe'] : [];
    $stripe['customer_id'] = $customerId ?: ($stripe['customer_id'] ?? null);
    $stripe['subscription_id'] = $subscriptionId ?: ($stripe['subscription_id'] ?? null);
    $stripe['subscription_status'] = (string)($subscription['status'] ?? 'none');
    $periodEnd = (int)($subscription['current_period_end'] ?? 0);
    $stripe['current_period_ends_at'] = $periodEnd > 0 ? gmdate('c', $periodEnd) : null;
    $user['stripe'] = $stripe;
    googa_write_user($data, $user);
    return true;
}

function googa_stripe_apply_checkout_session(array &$data, array $session): bool
{
    $metadata = is_array($session['metadata'] ?? null) ? $session['metadata'] : [];
    $email = googa_normalize_email((string)($metadata['googa_email'] ?? $session['client_reference_id'] ?? ''));
    $subscriptionId = (string)($session['subscription'] ?? '');
    if ($email === '' || $subscriptionId === '') {
        return false;
    }
    $subscription = googa_stripe_request('GET', 'subscriptions/' . rawurlencode($subscriptionId));
    $ok = googa_stripe_apply_subscription($data, $subscription, $email);
    if ($ok) {
        $user = googa_stripe_find_user($data, $email, (string)($session['customer'] ?? ''), $subscriptionId);
        if (is_array($user)) {
            $stripe = is_array($user['stripe'] ?? null) ? $user['stripe'] : [];
            $stripe['checkout_session_id'] = (string)($session['id'] ?? '');
            $user['stripe'] = $stripe;
            googa_write_user($data, $user);
        }
    }
    return $ok;
}

function googa_stripe_verify_signature(string $payload, string $header): bool
{
    $config = googa_stripe_config();
    $parts = [];
    foreach (explode(',', $header) as $piece) {
        [$key, $value] = array_pad(explode('=', $piece, 2), 2, '');
        $parts[trim($key)][] = trim($value);
    }
    $timestamp = isset($parts['t'][0]) ? (int)$parts['t'][0] : 0;
    if ($timestamp < time() - 300 || empty($parts['v1'])) {
        return false;
    }
    $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $config['STRIPE_WEBHOOK_SECRET']);
    foreach ($parts['v1'] as $signature) {
        if (hash_equals($expected, $signature)) {
            return true;
        }
    }
    return false;
}

function googa_stripe_create_portal_session(array $user): array
{
    $stripe = is_array($user['stripe'] ?? null) ? $user['stripe'] : [];
    $customerId = trim((string)($stripe['customer_id'] ?? ''));
    if ($customerId === '') {
        throw new RuntimeException('No Stripe customer for this account.');
    }
    $config = googa_stripe_config();
    $params = [
        'customer' => $customerId,
        'return_url' => GOOGA_PUBLIC_BASE_URL . '/',
        'locale' => 'auto',
    ];
    if (!empty($config['STRIPE_PORTAL_CONFIGURATION_ID'])) {
        $params['configuration'] = $config['STRIPE_PORTAL_CONFIGURATION_ID'];
    }
    return googa_stripe_request('POST', 'billing_portal/sessions', $params);
}
