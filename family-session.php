<?php
declare(strict_types=1);

session_name('googa');
session_start();
require_once __DIR__ . '/lib/store.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

$context = googa_session_context();
echo json_encode([
    'active' => !empty($context['authenticated']) && !empty($context['family_device']) && !empty($context['access']['allowed']),
]);
