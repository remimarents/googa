<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
http_response_code(410);
echo 'QR login has been retired. Use email and password.';
