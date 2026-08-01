<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/stripe.php';

$context = googa_session_context();
if (($context['email'] ?? '') === '') {
    header('Location: ./');
    exit;
}
try {
    $portal = googa_stripe_create_portal_session((array)$context['user']);
    $url = (string)($portal['url'] ?? '');
    if ($url === '') {
        throw new RuntimeException('Stripe returned no portal link.');
    }
    header('Location: ' . $url, true, 303);
} catch (Throwable $error) {
    error_log('Googa billing portal: ' . $error->getMessage());
    header('Location: ./access.php?payment=portal-error');
}
exit;
