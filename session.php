<?php
declare(strict_types=1);

session_name('googa');
session_start();

require_once __DIR__ . '/lib/store.php';
require_once __DIR__ . '/lib/version.php';

header('Content-Type: application/javascript; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

$context = googa_session_context();
$payload = [
    'email' => $context['email'],
    'account_email' => $context['account_email'] ?? $context['email'],
    'name' => $context['name'],
    'role' => $context['role'],
    'mode' => $context['mode'],
    'access' => $context['access'],
    'family_device' => !empty($context['family_device']),
];
echo 'window.GOOGA_SESSION = ' . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';';
echo 'window.GOOGA_BUILD = ' . json_encode(googa_version_data(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';';
