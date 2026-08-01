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
if (!in_array($kind, ['trial', 'annual', 'ordreise_lifetime', 'gift_3', 'gift_6', 'gift_12'], true)) {
    http_response_code(400);
    exit('Ugyldig betalingsvalg.');
}
try {
    $user = ($context['email'] ?? '') !== '' ? (array)$context['user'] : null;
    if ($kind === 'ordreise_lifetime' && (!is_array($user) || !googa_has_active_googa_subscription($user))) {
        throw new RuntimeException('Ordreise krever et aktivt Googa-abonnement.');
    }
    $session = googa_stripe_create_checkout($user, $kind, [
        'recipient_email' => (string)($_POST['recipient_email'] ?? ''),
        'recipient_name' => (string)($_POST['recipient_name'] ?? ''),
    ]);
    $url = (string)($session['url'] ?? '');
    if ($url === '') {
        throw new RuntimeException('Stripe returnerte ingen betalingslenke.');
    }
    $_SESSION['googa_checkout_intent'] = (string)($session['metadata']['googa_intent'] ?? '');
    header('Location: ' . $url, true, 303);
} catch (Throwable $error) {
    error_log('Googa checkout: ' . $error->getMessage());
    header('Location: ' . ($kind === 'ordreise_lifetime' ? './ordreise/help.php?payment=subscription-required' : './?payment=error'));
}
exit;
