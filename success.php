<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/stripe.php';

$context = googa_session_context();
$sessionId = trim((string)($_GET['session_id'] ?? ''));
$ready = false;
if (($context['email'] ?? '') !== '' && preg_match('/^cs_(live|test)_[A-Za-z0-9]+$/', $sessionId)) {
    try {
        $checkout = googa_stripe_request('GET', 'checkout/sessions/' . rawurlencode($sessionId));
        $email = googa_normalize_email((string)(($checkout['metadata']['googa_email'] ?? '') ?: ($checkout['client_reference_id'] ?? '')));
        if ($email === $context['email']) {
            $data = googa_load_data();
            $ready = googa_stripe_apply_checkout_session($data, $checkout);
            if ($ready) {
                googa_save_data($data);
            }
        }
    } catch (Throwable $error) {
        error_log('Googa success: ' . $error->getMessage());
    }
}
header('Location: ' . ($ready ? './' : './access.php?payment=processing'), true, 303);
exit;
