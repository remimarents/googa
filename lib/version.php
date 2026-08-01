<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

function googa_version_data(): array
{
    static $data = null;
    if (is_array($data)) {
        return $data;
    }
    $fallback = [
        'version' => '2026.08.01.1',
        'updatedAt' => gmdate('c'),
        'notes' => 'fallback',
    ];
    if (!is_file(GOOGA_VERSION_FILE)) {
        $data = $fallback;
        return $data;
    }
    $raw = file_get_contents(GOOGA_VERSION_FILE);
    $json = json_decode(is_string($raw) ? $raw : '', true);
    if (!is_array($json) || empty($json['version'])) {
        $data = $fallback;
        return $data;
    }
    $data = [
        'version' => (string)$json['version'],
        'updatedAt' => (string)($json['updatedAt'] ?? gmdate('c')),
        'notes' => (string)($json['notes'] ?? ''),
    ];
    return $data;
}

function googa_app_version(): string
{
    $data = googa_version_data();
    return $data['version'];
}

function googa_asset_url(string $path): string
{
    $separator = str_contains($path, '?') ? '&' : '?';
    return $path . $separator . 'v=' . rawurlencode(googa_app_version());
}
