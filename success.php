<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/stripe.php';

$sessionId = trim((string)($_GET['session_id'] ?? ''));
$ready = false;
$next = './?payment=processing';
if (preg_match('/^cs_(live|test)_[A-Za-z0-9]+$/', $sessionId)) {
    try {
        $checkout = googa_stripe_request('GET', 'checkout/sessions/' . rawurlencode($sessionId));
        $data = googa_load_data();
        $ready = googa_stripe_apply_checkout_session($data, $checkout);
        $email = googa_stripe_checkout_email($checkout);
        $metadata = is_array($checkout['metadata'] ?? null) ? $checkout['metadata'] : [];
        $expectedIntent = (string)($_SESSION['googa_checkout_intent'] ?? '');
        $actualIntent = (string)($metadata['googa_intent'] ?? '');
        $sameBrowser = $expectedIntent !== '' && $actualIntent !== '' && hash_equals($expectedIntent, $actualIntent);
        $isOrdreise = ($metadata['googa_kind'] ?? '') === 'ordreise_lifetime';
        $isGift = str_starts_with((string)($metadata['googa_kind'] ?? ''), 'gift_');
        if ($ready && $sameBrowser && $isGift) {
            unset($_SESSION['googa_checkout_intent']);
            googa_save_data($data);
            header('Location: ./gift.php?payment=success', true, 303);
            exit;
        }
        if ($ready && $sameBrowser && isset($data['users'][$email])) {
            $user = $data['users'][$email];
            googa_login_user($user);
            unset($_SESSION['googa_checkout_intent']);
            if ($isOrdreise) {
                $next = './ordreise/?payment=success';
            } elseif (!googa_user_has_password($user)) {
                $token = googa_create_password_token($data, $email, true);
                $next = './reset-password.php?t=' . rawurlencode((string)$token) . '&new=1';
            } else {
                $next = './';
            }
        }
        if ($ready) {
            googa_save_data($data);
        }
    } catch (Throwable $error) {
        error_log('Googa success: ' . $error->getMessage());
    }
}
header('Location: ' . $next, true, 303);
exit;
