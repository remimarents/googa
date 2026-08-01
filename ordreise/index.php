<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/../lib/store.php';

$context = googa_session_context();
if (empty($context['authenticated']) || empty($context['access']['allowed'])) {
    header('Location: ../');
    exit;
}
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
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <main id="app"></main>
    <script>window.GOOGA_ORDREISE_USER=<?= json_encode((string)$context['email'], JSON_UNESCAPED_UNICODE) ?>;window.GOOGA_ORDREISE_OWNER=<?= ($context['role'] ?? '') === 'owner' ? 'true' : 'false' ?>;</script>
    <script src="wordbank.js"></script>
    <script src="word-lists.js"></script>
    <script src="levels.js"></script>
    <script src="app.js"></script>
  </body>
</html>
