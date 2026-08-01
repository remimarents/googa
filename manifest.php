<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/version.php';

header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

echo json_encode([
    'name' => 'Googa',
    'short_name' => 'Googa',
    'start_url' => './',
    'display' => 'standalone',
    'background_color' => '#dff7ff',
    'theme_color' => '#103654',
    'icons' => [
        ['src' => googa_asset_url('assets/icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
        ['src' => googa_asset_url('assets/icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
