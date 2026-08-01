<?php
declare(strict_types=1);
session_name('googa'); session_start();
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store, max-age=0');
$action=(string)($_GET['action']??''); $token=trim((string)($_GET['t']??''));
if (!in_array($action,['create','poll'],true)) { http_response_code(400); echo json_encode(['error'=>'Unknown action']); exit; }
$url='https://marents.no/vismalight/api/external-qr.php?app=googa&action='.$action.($token!==''?'&t='.rawurlencode($token):'');
$ch=curl_init($url); curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>10]); $raw=curl_exec($ch); $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch); $data=json_decode(is_string($raw)?$raw:'',true);
if (!is_array($data) || $code>=400) { http_response_code(502); echo json_encode(['error'=>'QR bridge unavailable']); exit; }
if ($action==='poll' && ($data['status']??'')==='approved') { $consume='https://marents.no/vismalight/api/external-qr.php?app=googa&action=consume&t='.rawurlencode($token); $ch=curl_init($consume); curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>10]); $used=json_decode((string)curl_exec($ch),true); curl_close($ch); if (!empty($used['ok'])) { $_SESSION['googa_email']=(string)($data['email']??''); $_SESSION['googa_name']=(string)($data['identity']??''); } }
echo json_encode(['token'=>$data['token']??null,'scanUrl'=>$data['scanUrl']??null,'state'=>$data['status']??'pending','expiresAt'=>$data['expiresAt']??null]);
