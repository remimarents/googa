<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/version.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

echo json_encode(googa_version_data(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
