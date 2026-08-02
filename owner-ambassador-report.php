<?php
declare(strict_types=1);
session_name('googa');session_start();require_once __DIR__ . '/lib/store.php';
$context=googa_session_context();googa_require_owner($context);$data=googa_load_data();$year=max(2026,min(2100,(int)($_GET['year']??date('Y'))));
header('Content-Type: text/csv; charset=UTF-8');header('Content-Disposition: attachment; filename="googa-ambassadorer-'.$year.'.csv"');
$out=fopen('php://output','wb');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,['Ambassadør','E-post','Kode','Kunde','Stripe-faktura','Betalt dato','Grunnlag NOK','Provisjon NOK','Status','Utbetalt dato'],';');
foreach((array)($data['commissions']??[]) as $commission){if((int)substr((string)($commission['paid_at']??''),0,4)!==$year)continue;$amb=$data['ambassadors'][(string)($commission['ambassador_id']??'')]??[];fputcsv($out,[(string)($amb['name']??''),(string)($commission['ambassador_email']??''),(string)($amb['code']??''),(string)($commission['customer_email']??''),(string)($commission['invoice_id']??''),(string)($commission['paid_at']??''),number_format((int)($commission['eligible_paid_ore']??0)/100,2,'.',''),number_format((int)($commission['commission_ore']??0)/100,2,'.',''),(string)($commission['status']??''),(string)($commission['payout_at']??'')],';');}fclose($out);
