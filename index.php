<?php
declare(strict_types=1);

session_name('googa');
session_start();

require_once __DIR__ . '/lib/store.php';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (session_id() !== '') {
        session_destroy();
    }
    header('Location: ./');
    exit;
}

if (empty($_SESSION['googa_email'])) {
    require __DIR__ . '/login.php';
    exit;
}

$context = googa_session_context();

if ($context['role'] === 'owner' && empty($_SESSION['googa_mode'])) {
    require __DIR__ . '/owner-mode.php';
    exit;
}

if (!$context['access']['allowed']) {
    require __DIR__ . '/login.php';
    exit;
}

require __DIR__ . '/index.html';
