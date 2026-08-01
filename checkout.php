<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/stripe.php';

$context = googa_session_context();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals((string)($_SESSION['googa_payment_csrf'] ?? ''), (string)($_POST['csrf'] ?? ''))) {
    http_response_code(400);
    exit('Ugyldig betalingsforespørsel.');
}
$kind = (string)($_POST['kind'] ?? '');
if (!in_array($kind, ['trial', 'monthly'], true)) {
    http_response_code(400);
    exit('Ugyldig betalingsvalg.');
}
try {
    $user = ($context['email'] ?? '') !== '' ? (array)$context['user'] : null;
    $session = googa_stripe_create_checkout($user, $kind);
    $url = (string)($session['url'] ?? '');
    if ($url === '') {
        throw new RuntimeException('Stripe returnerte ingen betalingslenke.');
    }
    $_SESSION['googa_checkout_intent'] = (string)($session['metadata']['googa_intent'] ?? '');
    header('Location: ' . $url, true, 303);
} catch (Throwable $error) {
    error_log('Googa checkout: ' . $error->getMessage());
    header('Location: ./?payment=error');
}
exit;
