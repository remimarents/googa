<?php
declare(strict_types=1);
session_name('googa');session_start();
require_once __DIR__.'/lib/store.php';require_once __DIR__.'/lib/ambassador.php';
$context=googa_session_context();googa_require_owner($context);
try{$pdf=googa_ambassador_owner_pdf((string)($_GET['id']??''));header('Content-Type: application/pdf');header('Content-Disposition: inline; filename="Googa-ambassadoravtale.pdf"');header('Cache-Control: no-store, private, max-age=0');header('Content-Length: '.strlen($pdf));echo $pdf;}catch(Throwable $e){http_response_code(404);echo 'Fant ikke avtalen.';}
