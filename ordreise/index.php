<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/../lib/store.php';
require_once __DIR__ . '/../lib/version.php';

$context = googa_session_context();
if (empty($context['authenticated'])) {
    header('Location: ../');
    exit;
}
$user = is_array($context['user'] ?? null) ? $context['user'] : [];
$premium = googa_has_ordreise_full_access($user);
$freeLive = googa_ordreise_free_is_live();
$canPurchase = googa_has_active_googa_subscription($user) && !$premium;
if (!$premium && !$freeLive) {
    header('Location: help.php');
    exit;
}
$monthlyRelease = googa_ordreise_months_since_purchase($user);
$assetVersion = googa_app_version();
if (empty($_SESSION['googa_payment_csrf'])) {
    $_SESSION['googa_payment_csrf'] = bin2hex(random_bytes(24));
}
if (empty($_SESSION['googa_social_csrf'])) $_SESSION['googa_social_csrf'] = bin2hex(random_bytes(24));
$social = googa_family_social_data(is_array($context['user'] ?? null) ? $context['user'] : []);
?>
<!doctype html>
<html lang="so">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="theme-color" content="#087f89">
    <meta name="description" content="Safarka Erayada – ciyaarta erayada Af-Soomaaliga ee Googa.">
    <link rel="manifest" href="manifest.webmanifest">
    <title>Googa – Safarka Erayada</title>
    <link rel="stylesheet" href="styles.css?v=<?= rawurlencode($assetVersion) ?>">
    <link rel="stylesheet" href="wheel.css?v=<?= rawurlencode($assetVersion) ?>">
    <link rel="stylesheet" href="rewards.css?v=<?= rawurlencode($assetVersion) ?>">
    <link rel="stylesheet" href="../pwa-update.css?v=<?= rawurlencode($assetVersion) ?>">
  </head>
  <body>
    <main id="app"></main>
    <script>window.GOOGA_ORDREISE_USER=<?= json_encode((string)$context['email'], JSON_UNESCAPED_UNICODE) ?>;window.GOOGA_ORDREISE_OWNER=<?= ($context['role'] ?? '') === 'owner' ? 'true' : 'false' ?>;window.GOOGA_ORDREISE_PREMIUM=<?= $premium ? 'true' : 'false' ?>;window.GOOGA_ORDREISE_CAN_PURCHASE=<?= $canPurchase ? 'true' : 'false' ?>;window.GOOGA_ORDREISE_MONTHLY_RELEASE=<?= $monthlyRelease ?>;window.GOOGA_ORDREISE_PAYMENT_CSRF=<?= json_encode((string)$_SESSION['googa_payment_csrf'], JSON_UNESCAPED_UNICODE) ?>;window.GOOGA_ORDREISE_PAYMENT=<?= json_encode((string)($_GET['payment'] ?? ''), JSON_UNESCAPED_UNICODE) ?>;window.GOOGA_FAMILY_SOCIAL=<?= json_encode(['enabled'=>!empty($social['enabled']),'csrf'=>(string)$_SESSION['googa_social_csrf']], JSON_UNESCAPED_UNICODE) ?>;window.GOOGA_PWA_UPDATE={version:<?= json_encode($assetVersion) ?>,versionUrl:'../version.php',reloadUrl:'./'};</script>
    <script src="wordbank.js?v=<?= rawurlencode($assetVersion) ?>"></script>
    <script src="somaliweb-provisional.js?v=<?= rawurlencode($assetVersion) ?>"></script>
    <script src="word-lists.js?v=<?= rawurlencode($assetVersion) ?>"></script>
    <script src="levels.js?v=<?= rawurlencode($assetVersion) ?>"></script>
    <script src="rewards.js?v=<?= rawurlencode($assetVersion) ?>"></script>
    <script src="app.js?v=<?= rawurlencode($assetVersion) ?>"></script>
    <script src="../pwa-update.js?v=<?= rawurlencode($assetVersion) ?>"></script>
    <?php require __DIR__ . '/../owner-corrections-init.php'; ?>
  </body>
</html>
