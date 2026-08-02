<?php
declare(strict_types=1);

require_once __DIR__ . '/store.php';

function googa_send_password_email(string $email, string $token): bool
{
    $url = GOOGA_PUBLIC_BASE_URL . '/reset-password.php?t=' . rawurlencode($token);
    $subject = 'Samee furahaaga cusub ee Googa';
    $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $html = '<!doctype html><html lang="so"><body style="margin:0;background:#e8f7f8;font-family:Arial,sans-serif;color:#103654">'
        . '<div style="max-width:560px;margin:30px auto;background:#fffdf7;border-radius:24px;padding:28px">'
        . '<h1 style="margin:0 0 12px">Samee furahaaga cusub</h1>'
        . '<p>Waxaan helnay codsi lagu sameynayo ama lagu beddelayo furaha sirta ah ee Googa.</p>'
        . '<p><a href="' . $safeUrl . '" style="display:inline-block;background:#087f89;color:#fff;text-decoration:none;font-weight:bold;padding:14px 22px;border-radius:14px">Samee furaha cusub</a></p>'
        . '<p style="color:#5b7185">Xiriirkani wuxuu shaqaynayaa hal saac. Haddii aadan codsan, waad iska indho tiri kartaa farriintan.</p>'
        . '<hr style="border:0;border-top:1px solid #d9e7e9;margin:24px 0"><p style="font-size:13px;color:#5b7185">Googa · ferdighet.no</p>'
        . '</div></body></html>';
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: Googa <noreply@ferdighet.no>',
        'Reply-To: Sandra Marents <sandramarents@gmail.com>',
        'X-Mailer: Googa',
    ];
    return @mail($email, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, implode("\r\n", $headers), '-fnoreply@ferdighet.no');
}

function googa_send_gift_email(string $email, string $name, int $months, string $token): bool
{
    $url = GOOGA_PUBLIC_BASE_URL . '/reset-password.php?t=' . rawurlencode($token) . '&new=1';
    $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $safeName = htmlspecialchars($name !== '' ? $name : 'Salaan', ENT_QUOTES, 'UTF-8');
    $subject = 'Waxaad heshay hadiyad Googa ah';
    $html = '<!doctype html><html lang="so"><body style="margin:0;background:#e8f7f8;font-family:Arial,sans-serif;color:#103654"><div style="max-width:560px;margin:30px auto;background:#fffdf7;border-radius:24px;padding:28px">'
        . '<h1 style="margin:0 0 12px">' . $safeName . ', waxaad heshay Googa 🎁</h1><p>Qof ayaa ku siiyey <b>' . $months . ' bilood</b> oo Googa ah. Samee furahaaga si aad qoyska ula bilowdo.</p>'
        . '<p><a href="' . $safeUrl . '" style="display:inline-block;background:#087f89;color:#fff;text-decoration:none;font-weight:bold;padding:14px 22px;border-radius:14px">Samee furaha oo fur Googa</a></p>'
        . '<p style="color:#5b7185">Xiriirkani wuxuu shaqaynayaa hal saac. Haddii uu dhaco, isticmaal “Ma illowday furaha?” bogga gelitaanka.</p><hr style="border:0;border-top:1px solid #d9e7e9;margin:24px 0"><p style="font-size:13px;color:#5b7185">Googa · ferdighet.no</p></div></body></html>';
    $headers = ['MIME-Version: 1.0','Content-Type: text/html; charset=UTF-8','From: Sandra Marents <noreply@ferdighet.no>','Reply-To: Sandra Marents <sandramarents@gmail.com>','X-Mailer: Googa'];
    return @mail($email, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, implode("\r\n", $headers), '-fnoreply@ferdighet.no');
}

function googa_mail_plain_text(string $html): string
{
    $text = preg_replace('/<\s*br\s*\/?>/i', "\n", $html) ?? $html;
    $text = preg_replace('/<\/\s*(p|div|h[1-6]|li)\s*>/i', "\n", $text) ?? $text;
    return trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
}

function googa_send_email_with_attachment(string $email, string $subject, string $html, string $attachmentData, string $filename): bool
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $attachmentData === '') return false;
    if (getenv('GOOGA_DISABLE_OUTBOUND_MAIL') === '1') return true;
    $mixed = '=_Googa_' . bin2hex(random_bytes(16));
    $alternative = '=_Googa_alt_' . bin2hex(random_bytes(16));
    $safeFilename = preg_replace('/[^A-Za-z0-9._-]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename) ?: 'googa-avtale.pdf') ?: 'googa-avtale.pdf';
    $body = '--' . $mixed . "\r\nContent-Type: multipart/alternative; boundary=\"" . $alternative . "\"\r\n\r\n"
        . '--' . $alternative . "\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n"
        . quoted_printable_encode(googa_mail_plain_text($html)) . "\r\n\r\n"
        . '--' . $alternative . "\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n"
        . quoted_printable_encode($html) . "\r\n\r\n--" . $alternative . "--\r\n\r\n"
        . '--' . $mixed . "\r\nContent-Type: application/pdf; name=\"" . $safeFilename . "\"\r\nContent-Disposition: attachment; filename=\"" . $safeFilename . "\"\r\nContent-Transfer-Encoding: base64\r\n\r\n"
        . chunk_split(base64_encode($attachmentData)) . "\r\n--" . $mixed . "--\r\n";
    $encodedSubject = function_exists('mb_encode_mimeheader') ? mb_encode_mimeheader($subject, 'UTF-8') : '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: multipart/mixed; boundary="' . $mixed . '"',
        'From: Sandra Marents <noreply@ferdighet.no>',
        'Sender: Sandra Marents <noreply@ferdighet.no>',
        'Reply-To: Sandra Marents <sandramarents@gmail.com>',
        'X-Mailer: Googa',
    ];
    return @mail($email, $encodedSubject, $body, implode("\r\n", $headers), '-fnoreply@ferdighet.no');
}
