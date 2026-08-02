<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/lib/ambassador.php';

try {
    $counts = googa_ambassador_retry_automatic_activations();
    echo json_encode($counts, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
    exit(0);
} catch (Throwable $error) {
    error_log('Googa ambassador activation worker failed: ' . $error->getMessage());
    echo json_encode(['ok' => false], JSON_THROW_ON_ERROR) . PHP_EOL;
    exit(1);
}
