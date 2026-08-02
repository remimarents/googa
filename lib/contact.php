<?php
declare(strict_types=1);

function googa_contact_recipients(): array
{
    $file = '/home/ferdighet/.googa-contact.env';
    if (!is_readable($file)) return [];
    $values = parse_ini_file($file, false, INI_SCANNER_RAW);
    if (!is_array($values)) return [];
    $to = trim((string)($values['GOOGA_CONTACT_TO'] ?? ''));
    $cc = trim((string)($values['GOOGA_CONTACT_CC'] ?? ''));
    return filter_var($to, FILTER_VALIDATE_EMAIL) && filter_var($cc, FILTER_VALIDATE_EMAIL) ? [$to, $cc] : [];
}

function googa_contact_send(string $name, string $email, string $topic, string $message): bool
{
    [$to, $cc] = googa_contact_recipients() ?: ['', ''];
    if ($to === '' || $cc === '') return false;
    $subject = 'Googa – henvendelse' . ($topic !== '' ? ': ' . $topic : '');
    $body = "Navn: {$name}\nE-post: {$email}\nTema: {$topic}\n\n{$message}\n";
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: Googa <noreply@ferdighet.no>',
        'Reply-To: ' . $email,
        'Cc: ' . $cc,
        'X-Mailer: Googa',
    ];
    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers), '-fnoreply@ferdighet.no');
}
