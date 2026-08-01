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
    $headers = ['MIME-Version: 1.0','Content-Type: text/html; charset=UTF-8','From: Googa <noreply@ferdighet.no>','Reply-To: Sandra Marents <sandramarents@gmail.com>','X-Mailer: Googa'];
    return @mail($email, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, implode("\r\n", $headers), '-fnoreply@ferdighet.no');
}
