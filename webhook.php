<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/stripe.php';

$payload = (string)file_get_contents('php://input');
$signature = (string)($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');
try {
    if (!googa_stripe_verify_signature($payload, $signature)) {
        http_response_code(400);
        exit('Invalid signature');
    }
    $event = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
    $object = is_array($event['data']['object'] ?? null) ? $event['data']['object'] : [];
    $type = (string)($event['type'] ?? '');
    $data = googa_load_data();
    $changed = false;
    if ($type === 'checkout.session.completed') {
        $changed = googa_stripe_apply_checkout_session($data, $object);
    } elseif (in_array($type, ['customer.subscription.created', 'customer.subscription.updated', 'customer.subscription.deleted'], true)) {
        $changed = googa_stripe_apply_subscription($data, $object);
    } elseif (in_array($type, ['invoice.paid', 'invoice.payment_failed'], true)) {
        $subscriptionId = (string)($object['subscription'] ?? '');
        if ($subscriptionId !== '') {
            $subscription = googa_stripe_request('GET', 'subscriptions/' . rawurlencode($subscriptionId));
            $changed = googa_stripe_apply_subscription($data, $subscription);
        }
        if ($type === 'invoice.paid') {
            $changed = googa_stripe_record_first_paid($data, $object) || $changed;
            $changed = googa_stripe_record_commission($data, $object) || $changed;
        }
    }
    if ($changed) {
        googa_save_data($data);
    }
    http_response_code(200);
    echo 'ok';
} catch (Throwable $error) {
    error_log('Googa webhook: ' . $error->getMessage());
    http_response_code(500);
    echo 'retry';
}
