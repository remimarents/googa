<?php
declare(strict_types=1);

require_once __DIR__ . '/store.php';
require_once __DIR__ . '/mail.php';

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

function googa_stripe_request(string $method, string $path, array $params = [], ?string $idempotencyKey = null): array
{
    $config = googa_stripe_config();
    $url = 'https://api.stripe.com/v1/' . ltrim($path, '/');
    if ($method === 'GET' && $params !== []) {
        $url .= '?' . http_build_query($params, '', '&');
    }
    $curl = curl_init($url);
    if ($curl === false) {
        throw new RuntimeException('Unable to start Stripe request.');
    }
    $headers = ['Stripe-Version: 2025-04-30.basil'];
    if ($idempotencyKey !== null && trim($idempotencyKey) !== '') {
        $headers[] = 'Idempotency-Key: ' . substr(preg_replace('/[^A-Za-z0-9_.-]/', '-', $idempotencyKey) ?? '', 0, 255);
    }
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => $config['STRIPE_API_KEY'] . ':',
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => $headers,
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

function googa_stripe_create_checkout(?array $user, string $kind, array $options = []): array
{
    $email = googa_normalize_email((string)($user['email'] ?? ''));
    $discount = max(0, min(100, (int)($user['discount_percent'] ?? 0)));
    $intent = bin2hex(random_bytes(16));
    $ambassador = null;
    if (in_array($kind, ['trial', 'annual'], true) && $discount === 0) {
        $ambassador = googa_find_ambassador_by_code(googa_load_data(), (string)($options['ambassador_code'] ?? ''));
        if (is_array($ambassador) && $email !== '' && $email === googa_normalize_email((string)($ambassador['email'] ?? ''))) {
            $ambassador = null;
        }
    }
    if (str_starts_with($kind, 'gift_')) {
        $months = (int)substr($kind, 5);
        $recipientEmail = googa_normalize_email((string)($options['recipient_email'] ?? ''));
        $recipientName = trim((string)($options['recipient_name'] ?? ''));
        if (!isset(GOOGA_GIFT_PRICE_IDS[$months]) || $recipientEmail === '') {
            throw new RuntimeException('Gaven mangler en gyldig mottaker eller varighet.');
        }
        return googa_stripe_request('POST', 'checkout/sessions', [
            'mode' => 'payment',
            'success_url' => GOOGA_PUBLIC_BASE_URL . '/success.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => GOOGA_PUBLIC_BASE_URL . '/gift.php?payment=cancelled',
            'client_reference_id' => $intent,
            'line_items[0][price]' => GOOGA_GIFT_PRICE_IDS[$months],
            'line_items[0][quantity]' => 1,
            'metadata[googa_app]' => 'googa',
            'metadata[googa_intent]' => $intent,
            'metadata[googa_kind]' => $kind,
            'metadata[googa_recipient_email]' => $recipientEmail,
            'metadata[googa_recipient_name]' => mb_substr($recipientName, 0, 120),
            'metadata[googa_gift_months]' => $months,
        ]);
    }
    if ($kind === 'ordreise_lifetime') {
        if (!is_array($user) || $email === '') {
            throw new RuntimeException('Du må være innlogget for å kjøpe Ordreise.');
        }
        return googa_stripe_request('POST', 'checkout/sessions', [
            'mode' => 'payment',
            'success_url' => GOOGA_PUBLIC_BASE_URL . '/success.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => GOOGA_PUBLIC_BASE_URL . '/ordreise/?payment=cancelled',
            'client_reference_id' => $intent,
            'line_items[0][price]' => GOOGA_ORDREISE_LIFETIME_PRICE_ID,
            'line_items[0][quantity]' => 1,
            'metadata[googa_app]' => 'googa',
            'metadata[googa_intent]' => $intent,
            'metadata[googa_kind]' => $kind,
            'metadata[googa_email]' => $email,
            'metadata[googa_product]' => 'ordreise_lifetime',
            'customer_email' => $email,
        ]);
    }
    $params = [
        'mode' => 'subscription',
        'success_url' => GOOGA_PUBLIC_BASE_URL . '/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => GOOGA_PUBLIC_BASE_URL . '/?payment=cancelled',
        'client_reference_id' => $intent,
        'payment_method_collection' => 'always',
        'line_items[0][price]' => $kind === 'annual' ? GOOGA_ANNUAL_PRICE_ID : GOOGA_MONTHLY_PRICE_ID,
        'line_items[0][quantity]' => 1,
        'metadata[googa_app]' => 'googa',
        'metadata[googa_intent]' => $intent,
        'metadata[googa_kind]' => $kind,
        'subscription_data[metadata][googa_app]' => 'googa',
        'subscription_data[metadata][googa_intent]' => $intent,
        'subscription_data[metadata][googa_kind]' => $kind,
        'optional_items[0][price]' => GOOGA_ORGANIZATION_PRICE_ID,
        'optional_items[0][quantity]' => 1,
    ];
    if ($email !== '') {
        $params['customer_email'] = $email;
        $params['metadata[googa_email]'] = $email;
        $params['subscription_data[metadata][googa_email]'] = $email;
    }
    if ($kind === 'trial') {
        $params['line_items[1][price]'] = GOOGA_INTRO_PRICE_ID;
        $params['line_items[1][quantity]'] = 1;
        $params['subscription_data[trial_period_days]'] = 2;
        $params['subscription_data[trial_settings][end_behavior][missing_payment_method]'] = 'cancel';
    }
    if (is_array($ambassador)) {
        $couponId = $kind === 'annual' ? (string)($ambassador['annual_coupon_id'] ?? '') : (string)($ambassador['monthly_coupon_id'] ?? '');
        if ($couponId !== '') {
            $params['discounts[0][coupon]'] = $couponId;
            foreach (['metadata', 'subscription_data[metadata]'] as $prefix) {
                $params[$prefix . '[googa_ambassador_id]'] = (string)$ambassador['id'];
                $params[$prefix . '[googa_ambassador_code]'] = (string)$ambassador['code'];
                $params[$prefix . '[googa_ambassador_email]'] = (string)$ambassador['email'];
                $params[$prefix . '[googa_referral_started_at]'] = googa_now();
            }
        }
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

function googa_stripe_checkout_email(array $session): string
{
    $details = is_array($session['customer_details'] ?? null) ? $session['customer_details'] : [];
    $email = googa_normalize_email((string)($details['email'] ?? $session['customer_email'] ?? ''));
    if ($email === '' && !empty($session['customer'])) {
        $customer = googa_stripe_request('GET', 'customers/' . rawurlencode((string)$session['customer']));
        $email = googa_normalize_email((string)($customer['email'] ?? ''));
    }
    return $email;
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
    $kind = (string)($metadata['googa_kind'] ?? '');
    if (($metadata['googa_app'] ?? '') !== 'googa' || (!in_array($kind, ['trial', 'monthly', 'annual', 'ordreise_lifetime'], true) && !str_starts_with($kind, 'gift_'))) {
        return false;
    }
    if (($session['status'] ?? '') !== 'complete' || !in_array((string)($session['payment_status'] ?? ''), ['paid', 'no_payment_required'], true)) {
        return false;
    }
    if (str_starts_with($kind, 'gift_')) {
        $recipientEmail = googa_normalize_email((string)($metadata['googa_recipient_email'] ?? ''));
        $months = max(0, (int)($metadata['googa_gift_months'] ?? 0));
        if ($recipientEmail === '' || !isset(GOOGA_GIFT_PRICE_IDS[$months])) {
            return false;
        }
        googa_ensure_user($data, $recipientEmail, (string)($metadata['googa_recipient_name'] ?? ''));
        $user = $data['users'][$recipientEmail];
        $sessionId = (string)($session['id'] ?? '');
        $gifts = is_array($user['gifts'] ?? null) ? $user['gifts'] : [];
        if ($sessionId !== '' && isset($gifts[$sessionId])) {
            return true;
        }
        $base = googa_is_future($user['access_override_until'] ?? null)
            ? new DateTimeImmutable((string)$user['access_override_until'])
            : new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $user['access_override_until'] = $base->modify('+' . $months . ' months')->format(DATE_ATOM);
        $user['access_override_reason'] = 'Googa gave ' . $months . ' måneder · Stripe ' . (string)($session['id'] ?? '');
        $gifts[$sessionId] = ['months' => $months, 'purchased_at' => googa_now()];
        $user['gifts'] = $gifts;
        googa_write_user($data, $user);
        $token = googa_create_password_token($data, $recipientEmail, true);
        if (is_string($token) && !googa_send_gift_email($recipientEmail, (string)($metadata['googa_recipient_name'] ?? ''), $months, $token)) {
            error_log('Googa gift email could not be sent for ' . $recipientEmail);
        }
        return true;
    }
    $email = googa_normalize_email((string)($metadata['googa_email'] ?? ''));
    if ($email === '') {
        $email = googa_stripe_checkout_email($session);
    }
    if (($metadata['googa_kind'] ?? '') === 'ordreise_lifetime') {
        if ($email === '') {
            return false;
        }
        googa_ensure_user($data, $email, (string)(($session['customer_details']['name'] ?? '')));
        $user = googa_stripe_find_user($data, $email, (string)($session['customer'] ?? ''), '');
        if (!is_array($user)) {
            return false;
        }
        $entitlements = is_array($user['entitlements'] ?? null) ? $user['entitlements'] : [];
        $entitlements['ordreise_full'] = [
            'status' => 'active',
            'product_id' => GOOGA_ORDREISE_LIFETIME_PRODUCT_ID,
            'price_id' => GOOGA_ORDREISE_LIFETIME_PRICE_ID,
            'checkout_session_id' => (string)($session['id'] ?? ''),
            'payment_intent_id' => (string)($session['payment_intent'] ?? ''),
            'customer_id' => (string)($session['customer'] ?? ''),
            'purchased_at' => gmdate('c'),
        ];
        $user['entitlements'] = $entitlements;
        googa_write_user($data, $user);
        return true;
    }
    $subscriptionId = (string)($session['subscription'] ?? '');
    if ($email === '' || $subscriptionId === '') {
        return false;
    }
    googa_ensure_user($data, $email, (string)(($session['customer_details']['name'] ?? '')));
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
        $ambassadorId = (string)($metadata['googa_ambassador_id'] ?? '');
        $ambassadorEmail = googa_normalize_email((string)($metadata['googa_ambassador_email'] ?? ''));
        if ($ambassadorId !== '' && $ambassadorEmail !== '' && $ambassadorEmail !== $email && isset($data['ambassadors'][$ambassadorId])) {
            $startedAt = (string)($metadata['googa_referral_started_at'] ?? googa_now());
            $data['referrals'][$subscriptionId] = [
                'ambassador_id' => $ambassadorId,
                'ambassador_email' => $ambassadorEmail,
                'code' => (string)($metadata['googa_ambassador_code'] ?? ''),
                'customer_email' => $email,
                'subscription_id' => $subscriptionId,
                'checkout_session_id' => (string)($session['id'] ?? ''),
                'started_at' => $startedAt,
                'commission_ends_at' => gmdate('c', strtotime('+' . GOOGA_AMBASSADOR_COMMISSION_MONTHS . ' months', strtotime($startedAt) ?: time())),
                'status' => 'active',
            ];
            $paidInvoices = googa_stripe_request('GET', 'invoices', ['subscription' => $subscriptionId, 'status' => 'paid', 'limit' => 10]);
            foreach ((array)($paidInvoices['data'] ?? []) as $paidInvoice) googa_stripe_record_commission($data, $paidInvoice);
        }
        $sessionId = (string)($session['id'] ?? '');
        if ($sessionId !== '') {
            $lineItems = googa_stripe_request('GET', 'checkout/sessions/' . rawurlencode($sessionId) . '/line_items', ['limit' => 20]);
            foreach ((array)($lineItems['data'] ?? []) as $lineItem) {
                $priceId = (string)(($lineItem['price']['id'] ?? ''));
                if ($priceId === GOOGA_ORGANIZATION_PRICE_ID) {
                    $data['organization_orders'][$sessionId] = [
                        'email' => $email,
                        'status' => 'paid_followup_required',
                        'created_at' => googa_now(),
                    ];
                }
            }
        }
    }
    return $ok;
}

function googa_stripe_backfill_first_paid_at(array &$data, string $email): ?string
{
    $email = googa_normalize_email($email);
    if (!isset($data['users'][$email])) return null;
    $user = $data['users'][$email];
    $customerId = (string)(($user['stripe']['customer_id'] ?? ''));
    if ($customerId === '') return null;
    $invoices = googa_stripe_request('GET', 'invoices', ['customer' => $customerId, 'status' => 'paid', 'limit' => 100]);
    $paidTimes = [];
    foreach ((array)($invoices['data'] ?? []) as $invoice) {
        $paid = (int)(($invoice['status_transitions']['paid_at'] ?? $invoice['created'] ?? 0));
        if ($paid > 0) $paidTimes[] = $paid;
    }
    if (!$paidTimes) return null;
    $firstPaidAt = gmdate('c', min($paidTimes));
    $user['stripe']['first_paid_at'] = $firstPaidAt;
    googa_write_user($data, $user);
    return $firstPaidAt;
}

function googa_stripe_create_ambassador(array &$data, string $email, string $code, string $idempotencySeed = ''): array
{
    $email = googa_normalize_email($email);
    $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code) ?? '');
    if (!isset($data['users'][$email]) || strlen($code) < 4) throw new RuntimeException('Velg en kunde og en kode med minst fire tegn.');
    if (empty($data['users'][$email]['stripe']['first_paid_at'])) googa_stripe_backfill_first_paid_at($data, $email);
    $user = $data['users'][$email];
    if (!googa_ambassador_user_eligible($user)) throw new RuntimeException('Kunden må ha hatt et aktivt betalt abonnement i minst 30 dager.');
    foreach ((array)($data['ambassadors'] ?? []) as $existing) {
        if (strtoupper((string)($existing['code'] ?? '')) === $code) throw new RuntimeException('Koden finnes allerede.');
        if (googa_normalize_email((string)($existing['email'] ?? '')) === $email && ($existing['status'] ?? '') === 'active') throw new RuntimeException('Kunden er allerede ambassadør.');
    }
    $seed = $idempotencySeed !== '' ? hash('sha256', $idempotencySeed) : bin2hex(random_bytes(16));
    $monthlyCoupon = googa_stripe_request('POST', 'coupons', [
        'percent_off' => 50,
        'duration' => 'repeating',
        'duration_in_months' => 2,
        'name' => 'Googa ambassadør: kr 25 avslag i 2 mnd',
        'applies_to[products][0]' => GOOGA_PRODUCT_ID,
        'metadata[googa_ambassador_email]' => $email,
        'metadata[googa_ambassador_code]' => $code,
    ], 'googa-ambassador-monthly-' . $seed);
    $annualCoupon = googa_stripe_request('POST', 'coupons', [
        'amount_off' => 5000,
        'currency' => 'nok',
        'duration' => 'once',
        'name' => 'Googa ambassadør: kr 50 avslag første år',
        'applies_to[products][0]' => GOOGA_PRODUCT_ID,
        'metadata[googa_ambassador_email]' => $email,
        'metadata[googa_ambassador_code]' => $code,
    ], 'googa-ambassador-annual-' . $seed);
    return [
        'id' => bin2hex(random_bytes(10)), 'email' => $email, 'name' => (string)($user['name'] ?: $email), 'code' => $code,
        'status' => 'active', 'monthly_coupon_id' => (string)$monthlyCoupon['id'], 'annual_coupon_id' => (string)$annualCoupon['id'],
        'commission_percent' => GOOGA_AMBASSADOR_COMMISSION_PERCENT, 'commission_months' => GOOGA_AMBASSADOR_COMMISSION_MONTHS,
        'qualified_at' => googa_now(), 'created_at' => googa_now(),
    ];
}

function googa_stripe_invoice_subscription_id(array $invoice): string
{
    return (string)($invoice['subscription'] ?? ($invoice['parent']['subscription_details']['subscription'] ?? ''));
}

function googa_stripe_record_first_paid(array &$data, array $invoice): bool
{
    $subscriptionId = googa_stripe_invoice_subscription_id($invoice);
    if ($subscriptionId === '') return false;
    $user = googa_stripe_find_user($data, '', '', $subscriptionId);
    if (!is_array($user) || !empty($user['stripe']['first_paid_at'])) return false;
    $paidAt = (int)(($invoice['status_transitions']['paid_at'] ?? $invoice['created'] ?? time()));
    $user['stripe']['first_paid_at'] = gmdate('c', $paidAt);
    googa_write_user($data, $user);
    return true;
}

function googa_stripe_record_commission(array &$data, array $invoice): bool
{
    if (($invoice['status'] ?? '') !== 'paid') return false;
    $invoiceId = (string)($invoice['id'] ?? '');
    $subscriptionId = googa_stripe_invoice_subscription_id($invoice);
    if ($invoiceId === '' || $subscriptionId === '' || isset($data['commissions'][$invoiceId])) return false;
    $referral = $data['referrals'][$subscriptionId] ?? null;
    if (!is_array($referral) || ($referral['status'] ?? '') !== 'active' || !googa_is_future($referral['commission_ends_at'] ?? null)) return false;
    $ambassadorId = (string)($referral['ambassador_id'] ?? '');
    $ambassador = $data['ambassadors'][$ambassadorId] ?? null;
    $ambassadorEmail = googa_normalize_email((string)($ambassador['email'] ?? ''));
    if (!is_array($ambassador) || ($ambassador['status'] ?? '') !== 'active' || !isset($data['users'][$ambassadorEmail]) || !googa_ambassador_user_eligible($data['users'][$ambassadorEmail])) return false;
    $eligiblePaid = 0;
    foreach ((array)($invoice['lines']['data'] ?? []) as $line) {
        $priceId = (string)($line['price']['id'] ?? ($line['pricing']['price_details']['price'] ?? ''));
        if (!in_array($priceId, [GOOGA_MONTHLY_PRICE_ID, GOOGA_ANNUAL_PRICE_ID], true)) continue;
        $lineNet = (int)($line['amount'] ?? 0);
        foreach ((array)($line['discount_amounts'] ?? []) as $discount) $lineNet -= (int)($discount['amount'] ?? 0);
        $eligiblePaid += max(0, $lineNet);
    }
    if ($eligiblePaid <= 0) return false;
    $paidAt = (int)(($invoice['status_transitions']['paid_at'] ?? time()));
    $data['commissions'][$invoiceId] = [
        'invoice_id' => $invoiceId, 'subscription_id' => $subscriptionId, 'ambassador_id' => $ambassadorId,
        'ambassador_email' => $ambassadorEmail, 'customer_email' => (string)($referral['customer_email'] ?? ''),
        'eligible_paid_ore' => $eligiblePaid, 'commission_ore' => (int)round($eligiblePaid * GOOGA_AMBASSADOR_COMMISSION_PERCENT / 100),
        'status' => 'pending', 'paid_at' => gmdate('c', $paidAt), 'available_at' => gmdate('c', $paidAt + GOOGA_AMBASSADOR_HOLD_DAYS * 86400),
        'payout_at' => null,
    ];
    return true;
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
