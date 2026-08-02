<?php
declare(strict_types=1);

require_once __DIR__ . '/store.php';
require_once __DIR__ . '/mail.php';

function googa_ambassador_private_dir(): string
{
    $override = trim((string)getenv('GOOGA_AMBASSADOR_PRIVATE_DIR'));
    return $override !== '' ? $override : GOOGA_AMBASSADOR_PRIVATE_DIR;
}

function googa_ambassador_store_file(): string { return googa_ambassador_private_dir() . '/applications.json'; }
function googa_ambassador_document_dir(): string { return googa_ambassador_private_dir() . '/documents'; }

function googa_ambassador_prepare_private_storage(): void
{
    $dir = googa_ambassador_private_dir();
    if (!is_dir($dir) && !mkdir($dir, 0700, true) && !is_dir($dir)) throw new RuntimeException('Kunne ikke opprette privat ambassadørlagring.');
    @chmod($dir, 0700);
    $documents = googa_ambassador_document_dir();
    if (!is_dir($documents) && !mkdir($documents, 0700, true) && !is_dir($documents)) throw new RuntimeException('Kunne ikke opprette privat dokumentlager.');
    @chmod($documents, 0700);
    if (!is_file(googa_ambassador_store_file())) {
        file_put_contents(googa_ambassador_store_file(), json_encode(['version' => 1, 'applications' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
        @chmod(googa_ambassador_store_file(), 0600);
    }
}

function googa_ambassador_read_store(): array
{
    googa_ambassador_prepare_private_storage();
    $raw = file_get_contents(googa_ambassador_store_file());
    $data = json_decode(is_string($raw) ? $raw : '', true);
    return is_array($data) && is_array($data['applications'] ?? null) ? $data : ['version' => 1, 'applications' => []];
}

function googa_ambassador_store_transaction(callable $callback): mixed
{
    googa_ambassador_prepare_private_storage();
    $lock = fopen(googa_ambassador_store_file() . '.lock', 'c+');
    if (!$lock || !flock($lock, LOCK_EX)) throw new RuntimeException('Kunne ikke låse ambassadørlagringen.');
    try {
        $data = googa_ambassador_read_store();
        $result = $callback($data);
        $tmp = googa_ambassador_store_file() . '.tmp.' . bin2hex(random_bytes(5));
        file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), LOCK_EX);
        @chmod($tmp, 0600);
        if (!rename($tmp, googa_ambassador_store_file())) throw new RuntimeException('Kunne ikke lagre ambassadørsøknaden.');
        @chmod(googa_ambassador_store_file(), 0600);
        return $result;
    } finally { flock($lock, LOCK_UN); fclose($lock); }
}

function googa_ambassador_secret(): string
{
    static $secret = null;
    if (is_string($secret)) return $secret;
    $override = trim((string)getenv('GOOGA_AMBASSADOR_SECRET'));
    $encoded = $override !== '' ? $override : (is_file(GOOGA_AMBASSADOR_SECRET_FILE) ? trim((string)file_get_contents(GOOGA_AMBASSADOR_SECRET_FILE)) : '');
    $decoded = base64_decode($encoded, true);
    if (!is_string($decoded) || strlen($decoded) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) throw new RuntimeException('Ambassadørkryptering er ikke konfigurert.');
    return $secret = $decoded;
}

function googa_ambassador_encrypt_id(string $value): array
{
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    return ['algorithm' => 'secretbox-v1', 'nonce' => base64_encode($nonce), 'ciphertext' => base64_encode(sodium_crypto_secretbox($value, $nonce, googa_ambassador_secret()))];
}

function googa_ambassador_normalize_identity(string $value): string
{
    return strtoupper(preg_replace('/[^\p{L}\p{N}]/u', '', trim($value)) ?? '');
}

function googa_ambassador_find_for_email(string $email): ?array
{
    $email = googa_normalize_email($email);
    $matches = array_values(array_filter(googa_ambassador_read_store()['applications'], static fn($a) => is_array($a) && googa_normalize_email((string)($a['account_email'] ?? '')) === $email));
    if (!$matches) return null;
    usort($matches, static fn($a, $b) => strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? '')));
    return $matches[0];
}

function googa_ambassador_document(array $application): array
{
    return [
        'agreement_id' => (string)$application['id'], 'version' => GOOGA_AMBASSADOR_AGREEMENT_VERSION,
        'title' => 'Ambassadøravtale for Googa', 'issued_at' => (string)$application['created_at'],
        'parties' => [
            'googa' => 'REMI MARENTS, org.nr. 928 096 041, som leverer Googa (heretter «Googa»)',
            'ambassador' => (string)$application['name'] . ', ' . (string)$application['email'] . ', offentlig ID som slutter på ' . (string)$application['identity_last4'] . ' (heretter «Ambassadøren»)',
        ],
        'clauses' => [
            ['title'=>'1. Formål og rolle','text'=>'Ambassadøren anbefaler Googa til nye familier som selv kjøper et ordinært familieabonnement direkte fra Googa. Ambassadøren er en selvstendig oppdragstaker og kan ikke inngå avtaler, gi løfter eller påta seg forpliktelser på vegne av Googa. Avtalen etablerer ikke et ansettelsesforhold.'],
            ['title'=>'2. Krav for å delta','text'=>'Ambassadøren må være minst 18 år, være betalende Googa-kunde og ha hatt et aktivt betalt abonnement i minst 30 dager før aktivering. Ambassadørkoden pauses automatisk dersom abonnementet ikke lenger er aktivt. Opptjent provisjon beholdes med forbehold om tilbakeføringer etter punkt 6.'],
            ['title'=>'3. Kundens fordel','text'=>'En ny familie som bruker en gyldig ambassadørkode ved kjøp, får den fordelen som vises i Googa-kassen. Ved avtaleversjonens dato er fordelen kr 25 rabatt på hver av de to første fulle månedsbetalingene, eller kr 50 rabatt på første årsbetaling. Googa kan endre fremtidige kundefordeler med skriftlig varsel uten å endre allerede gjennomførte kjøp.'],
            ['title'=>'4. Provisjonsgrunnlag','text'=>'Ambassadøren opptjener 20 prosent av faktisk innbetalt abonnementsvederlag for direkte salg til nye private sluttbrukere som er korrekt knyttet til Ambassadørens kode, i inntil 12 måneder fra kundens første kvalifiserende betaling. Provisjonsgrunnlaget omfatter bare Googas ordinære måneds- og årsabonnement for familier. Introduksjonsbeløpet på kr 5 inngår ikke.'],
            ['title'=>'5. Salg som uttrykkelig er unntatt','text'=>'Det gis ikke provisjon av organisasjons- eller foreningspakker, skole-, bibliotek-, kommune- eller andre virksomhetskjøp, sponsoravtaler, gaveabonnement, Ordreise eller andre tilleggskjøp, manuelle avtaler, egne kjøp eller kjøp som ikke kan knyttes teknisk til koden ved checkout. Et senere organisasjonskjøp gir ikke provisjon selv om kontaktpersonen tidligere har brukt koden privat.'],
            ['title'=>'6. Opptjening, kontroll og tilbakeføring','text'=>'Provisjon beregnes av innbetalt beløp etter rabatt og eksklusive refusjoner, krediteringer, chargebacks og tap. Hver post holdes i 30 dager før den blir tilgjengelig. Feilførte eller senere tilbakeførte beløp kan korrigeres eller motregnes. Samme kunde eller betaling kan bare tilordnes én ambassadør. Selvhenvisning, kunstige kjøp og omgåelse er ikke tillatt.'],
            ['title'=>'7. Avregning og skatt','text'=>'Tilgjengelig provisjon avregnes manuelt og normalt kvartalsvis. Ambassadøren er selv ansvarlig for å oppgi korrekte betalingsopplysninger og for egne skatte-, avgifts- og rapporteringsforpliktelser. Googa kan be om nødvendig dokumentasjon før utbetaling.'],
            ['title'=>'8. Markedsføring og merkevare','text'=>'All omtale som kan gi provisjon skal merkes tydelig som reklame eller annonse. Opplysninger om pris, innhold og vilkår skal være riktige og oppdaterte. Ambassadøren kan bruke godkjent Googa-materiell, men kan ikke endre logoen, registrere forvekselbare domener eller gi inntrykk av å være ansatt eller offisiell talsperson. Markedsføring skal ikke rettes uforsvarlig mot barn.'],
            ['title'=>'9. Personvern og konfidensialitet','text'=>'Partene skal beskytte personopplysninger, tilgangsdata, ikke-offentlig økonomiinformasjon og annet fortrolig materiale. Ambassadøren skal ikke samle inn eller sende kunders betalingsopplysninger til Googa. ID-nummeret i søknaden brukes til identitets- og oppgjørskontroll, lagres kryptert og er bare tilgjengelig for særskilt autorisert eierkontroll.'],
            ['title'=>'10. Varighet, suspensjon og opphør','text'=>'Avtalen trer i kraft når Ambassadøren har fullført den elektroniske signeringen og Googas automatiske kontroll har bekreftet en komplett signatur, uendret dokumentkontrollsum og fortsatt kvalifisert betalt kundestatus. Ambassadørkoden aktiveres da automatisk. Ved en midlertidig teknisk feil er signaturen fortsatt gyldig, og aktiveringen forsøkes automatisk på nytt. Begge parter kan si opp avtalen skriftlig med 30 dagers varsel. Googa kan suspendere eller avslutte ordningen straks ved svindel, villedende markedsføring, vesentlig avtalebrudd eller sikkerhetsrisiko. Nye salg stanser ved opphør; allerede opptjent og kontrollert provisjon avregnes etter ordinære regler.'],
            ['title'=>'11. Endringer, lovvalg og tvist','text'=>'Vesentlige endringer i provisjonssats, provisjonsgrunnlag eller plikter varsles skriftlig og gjelder bare fremover. Fortsatt deltakelse etter oppgitt ikrafttredelsesdato innebærer aksept; dersom Ambassadøren ikke aksepterer, kan avtalen sies opp før datoen. Avtalen er underlagt norsk rett. Uenighet skal først søkes løst i dialog og ellers behandles av ordinære norske domstoler.'],
            ['title'=>'12. Elektronisk signering','text'=>'Avtalen signeres med en personlig engangslenke sendt til oppgitt e-post. Det å åpne lenken er ikke en signatur. Avtalen signeres først når Ambassadøren har lest avtalen, skrevet sitt fulle navn, bekreftet vilkårene og aktivt trykket på signeringsknappen. Signeringen knyttes til den frosne dokumentversjonen og kontrollsummen, signatarens navn, e-post, tidspunkt og minimerte tekniske kontrollopplysninger. Dette er en enkel elektronisk signatur, ikke BankID eller kvalifisert elektronisk signatur. Etter fullført signering og automatisk serverkontroll opprettes avtalen og ambassadørkoden aktiveres uten manuell eiergodkjenning.'],
        ],
    ];
}

function googa_ambassador_canonicalize(mixed $value): mixed
{
    if (!is_array($value)) return $value;
    if (!array_is_list($value)) ksort($value);
    foreach ($value as $key => $item) $value[$key] = googa_ambassador_canonicalize($item);
    return $value;
}
function googa_ambassador_document_hash(array $document): string { return hash('sha256', json_encode(googa_ambassador_canonicalize($document), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)); }

function googa_pdf_escape(string $text): string
{
    $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
    return str_replace(['\\','(',')',"\r","\n"], ['\\\\','\\(','\\)','',''], is_string($encoded) ? $encoded : $text);
}
function googa_pdf_wrap(string $text, int $max = 88): array
{
    $words = preg_split('/\s+/u', trim($text)) ?: []; $lines=[]; $line='';
    foreach($words as $word){$next=$line===''?$word:$line.' '.$word;if(mb_strlen($next,'UTF-8')>$max&&$line!==''){$lines[]=$line;$line=$word;}else{$line=$next;}}
    if($line!=='')$lines[]=$line; return $lines ?: [''];
}
function googa_ambassador_pdf(array $application, bool $signed): string
{
    $doc = $application['document']; $pages=[]; $stream=''; $y=790;
    $newPage = function() use (&$pages,&$stream,&$y){ if($stream!=='')$pages[]=$stream; $stream="q 0.06 0.21 0.33 rg 0 812 595 30 re f Q\n"; $y=785; };
    $line = function(string $text,int $size=10,bool $bold=false,int $gap=14) use (&$stream,&$y,$newPage){ if($y<55)$newPage();$font=$bold?'F2':'F1';$stream.="BT /$font $size Tf 52 $y Td (".googa_pdf_escape($text).") Tj ET\n";$y-=$gap; };
    $newPage(); $line((string)$doc['title'],22,true,29); $line('Avtale-ID: '.$doc['agreement_id'].'  |  Versjon: '.$doc['version'],9,false,14); $line('Dokumentkontrollsum: '.(string)$application['document_hash'],7,false,22);
    $line('Parter',15,true,21); foreach($doc['parties'] as $party)$line((string)$party,9,false,15); $y-=5;
    foreach($doc['clauses'] as $clause){$line((string)$clause['title'],12,true,18);foreach(googa_pdf_wrap((string)$clause['text']) as $wrapped)$line($wrapped,9,false,13);$y-=7;}
    $line('Signeringsbevis',15,true,22);
    if($signed){$line('ELEKTRONISK SIGNERT',12,true,18);foreach(googa_pdf_wrap('Signert av '.(string)$application['signed_name'].' ('.(string)$application['email'].') den '.(string)$application['signed_at'].'. Signaturkvittering: '.(string)$application['signature_receipt_hash'],82) as $wrapped)$line($wrapped,9,false,13);}else{$line('Ikke signert. PDF-en er den frosne avtalen som ble sendt til signering.',9,false,15);}
    if($stream!=='')$pages[]=$stream;
    $objects=[1=>'<< /Type /Catalog /Pages 2 0 R >>']; $kids=[]; $next=5;
    foreach($pages as $index=>$content){$pageObj=$next++;$contentObj=$next++;$kids[]=$pageObj.' 0 R';$objects[$pageObj]='<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents '.$contentObj.' 0 R >>';$objects[$contentObj]='<< /Length '.strlen($content)." >>\nstream\n".$content."endstream";}
    $objects[2]='<< /Type /Pages /Kids ['.implode(' ',$kids).'] /Count '.count($kids).' >>';$objects[3]='<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';$objects[4]='<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';ksort($objects);
    $pdf="%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";$offsets=[];foreach($objects as $n=>$object){$offsets[$n]=strlen($pdf);$pdf.=$n." 0 obj\n".$object."\nendobj\n";}$xref=strlen($pdf);$max=max(array_keys($objects));$pdf.="xref\n0 ".($max+1)."\n0000000000 65535 f \n";for($i=1;$i<=$max;$i++)$pdf.=sprintf('%010d 00000 n ', $offsets[$i]??0)."\n";$pdf.="trailer\n<< /Size ".($max+1)." /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";return $pdf;
}

function googa_ambassador_write_pdf(string $id, string $kind, string $pdf): array
{
    googa_ambassador_prepare_private_storage();
    if(!preg_match('/^gaa_[a-f0-9]{24}$/',$id)||!in_array($kind,['issued','signed'],true))throw new RuntimeException('Ugyldig dokumentreferanse.');
    $path=googa_ambassador_document_dir().'/'.$id.'-'.$kind.'.pdf';file_put_contents($path,$pdf,LOCK_EX);@chmod($path,0600);return ['path'=>$path,'sha256'=>hash('sha256',$pdf)];
}

function googa_ambassador_create_application(array $user, string $name, string $identity, string $email): array
{
    if(!googa_ambassador_user_eligible($user))throw new RuntimeException('Du må ha vært betalende Googa-kunde i minst 30 dager før du kan søke.');
    $accountEmail=googa_normalize_email((string)($user['email'] ?? ''));
    $email=googa_normalize_email($email);$name=trim(preg_replace('/\s+/u',' ',$name)??'');$identity=googa_ambassador_normalize_identity($identity);
    if(mb_strlen($name)<3||mb_strlen($name)>120)throw new RuntimeException('Skriv inn fullt navn.');
    if(!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Skriv inn en gyldig e-postadresse.');
    if(strlen($identity)<6||strlen($identity)>24)throw new RuntimeException('Kontroller fødselsnummeret eller ID-nummeret.');
    $token=rtrim(strtr(base64_encode(random_bytes(36)),'+/','-_'),'=');$now=googa_now();$id='gaa_'.bin2hex(random_bytes(12));
    $app=['id'=>$id,'status'=>'sent','account_email'=>$accountEmail,'email'=>$email,'name'=>$name,'identity_last4'=>substr($identity,-4),'identity_encrypted'=>googa_ambassador_encrypt_id($identity),'created_at'=>$now,'sent_at'=>$now,'token_hash'=>hash('sha256',$token),'token_expires_at'=>(new DateTimeImmutable($now))->modify('+'.GOOGA_AMBASSADOR_SIGNING_DAYS.' days')->format(DATE_ATOM),'events'=>[['type'=>'created','at'=>$now]]];
    $app['document']=googa_ambassador_document($app);$app['document_hash']=googa_ambassador_document_hash($app['document']);$issued=googa_ambassador_write_pdf($id,'issued',googa_ambassador_pdf($app,false));$app['issued_pdf_path']=$issued['path'];$app['issued_pdf_hash']=$issued['sha256'];
    googa_ambassador_store_transaction(function(array &$data)use($app){foreach($data['applications'] as $existing){if(googa_normalize_email((string)($existing['account_email']??''))===$app['account_email']&&in_array((string)($existing['status']??''),['sent','viewed','signed','activating','activation_pending','active'],true))throw new RuntimeException('Du har allerede en søknad eller en aktiv ambassadøravtale.');}$data['applications'][$app['id']]=$app;});
    return ['application'=>$app,'token'=>$token];
}

function googa_ambassador_find_by_token(array $data,string $token): ?array
{
    if(!preg_match('/^[A-Za-z0-9_-]{40,80}$/',$token))return null;$hash=hash('sha256',$token);
    foreach($data['applications'] as $id=>$app)if(is_array($app)&&hash_equals((string)($app['token_hash']??''),$hash))return ['id'=>(string)$id,'application'=>$app];return null;
}

function googa_ambassador_public_application(array $app): array
{
    return ['id'=>$app['id'],'status'=>$app['status'],'name'=>$app['name'],'email'=>$app['email'],'version'=>$app['document']['version'],'document'=>$app['document'],'document_hash'=>$app['document_hash'],'created_at'=>$app['created_at'],'viewed_at'=>$app['viewed_at']??null,'signed_at'=>$app['signed_at']??null,'signed_name'=>$app['signed_name']??null,'ambassador_code'=>$app['ambassador_code']??null,'activation_message'=>$app['activation_message']??null];
}

function googa_ambassador_open(string $token): array
{
    return googa_ambassador_store_transaction(function(array &$data)use($token){$found=googa_ambassador_find_by_token($data,$token);if(!$found)throw new RuntimeException('Signeringslenken er ugyldig.');$app=$found['application'];if(!in_array($app['status'],['signed','activating','activation_pending','active'],true)&&strtotime((string)$app['token_expires_at'])<time())throw new RuntimeException('Signeringslenken er utløpt.');if($app['status']==='sent'){$app['status']='viewed';$app['viewed_at']=googa_now();$app['events'][]=['type'=>'viewed','at'=>$app['viewed_at']];$data['applications'][$found['id']]=$app;}return googa_ambassador_public_application($app);});
}

function googa_ambassador_sign(string $token,string $name,bool $accepted,bool $adult): array
{
    $result=googa_ambassador_store_transaction(function(array &$data)use($token,$name,$accepted,$adult){$found=googa_ambassador_find_by_token($data,$token);if(!$found)throw new RuntimeException('Signeringslenken er ugyldig.');$app=$found['application'];if(strtotime((string)$app['token_expires_at'])<time())throw new RuntimeException('Signeringslenken er utløpt.');$name=trim(preg_replace('/\s+/u',' ',$name)??'');if(mb_strlen($name)<3||!$accepted||!$adult)throw new RuntimeException('Skriv navnet ditt og bekreft begge punktene.');$now=googa_now();$app['status']='signed';$app['signed_at']=$now;$app['signed_name']=mb_substr($name,0,120);$app['signing_ip_hash']=hash('sha256',$app['id'].'|'.($_SERVER['REMOTE_ADDR']??''));$app['signing_user_agent_hash']=hash('sha256',(string)($_SERVER['HTTP_USER_AGENT']??''));$app['signature_receipt_hash']=hash('sha256',implode('|',[$app['document_hash'],$app['signed_name'],$app['email'],$now]));$app['token_used_at']=$now;$app['token_hash']=null;$app['events'][]=['type'=>'signed','at'=>$now];$signed=googa_ambassador_write_pdf($app['id'],'signed',googa_ambassador_pdf($app,true));$app['signed_pdf_path']=$signed['path'];$app['signed_pdf_hash']=$signed['sha256'];$data['applications'][$found['id']]=$app;return $app;});
    googa_send_ambassador_signed_email($result);
    try {
        $activated = googa_ambassador_activate_signed((string)$result['id']);
        return googa_ambassador_public_application($activated);
    } catch (Throwable $error) {
        error_log('Googa ambassador automatic activation pending for ' . (string)$result['id'] . ': ' . $error->getMessage());
        return googa_ambassador_public_application(googa_ambassador_mark_activation_pending((string)$result['id']));
    }
}

function googa_send_ambassador_signing_email(array $app,string $token): bool
{
    $link=GOOGA_PUBLIC_BASE_URL.'/ambassador-sign.php#t='.rawurlencode($token);$safeLink=htmlspecialchars($link,ENT_QUOTES,'UTF-8');$safeName=htmlspecialchars((string)$app['name'],ENT_QUOTES,'UTF-8');$html='<!doctype html><html lang="no"><body style="margin:0;background:#e8f7f8;font-family:Arial,sans-serif;color:#103654"><main style="max-width:620px;margin:28px auto;background:#fffdf7;border-radius:24px;padding:30px"><p style="font-weight:bold;color:#087f89">GOOGA AMBASSADØR</p><h1>Hei '.$safeName.' – avtalen er klar</h1><p>Takk for at du ønsker å anbefale Googa til andre familier. Les den vedlagte avtalen og signer med den personlige lenken.</p><p><a href="'.$safeLink.'" style="display:inline-block;background:#087f89;color:white;text-decoration:none;padding:15px 22px;border-radius:14px;font-weight:bold">Les og signer avtalen</a></p><p>Lenken er personlig og gyldig i '.GOOGA_AMBASSADOR_SIGNING_DAYS.' dager. Den må ikke videresendes. Når signeringen er fullført og den automatiske kontrollen er godkjent, trer avtalen i kraft og koden aktiveres automatisk.</p><p>Vennlig hilsen<br><b>Sandra Marents</b><br>Googa</p></main></body></html>';
    return googa_send_email_with_attachment((string)$app['email'],'Googa ambassadøravtale – til signering',$html,(string)file_get_contents($app['issued_pdf_path']),'Googa-ambassadoravtale-'.$app['id'].'.pdf');
}

function googa_send_ambassador_signed_email(array $app): void
{
    $pdf=(string)file_get_contents((string)$app['signed_pdf_path']);$html='<!doctype html><html lang="no"><body style="font-family:Arial,sans-serif;color:#103654"><h1>Avtalen er signert</h1><p>Hei '.htmlspecialchars((string)$app['name'],ENT_QUOTES,'UTF-8').'. Vi har mottatt signaturen din. Avtalen trer i kraft og ambassadørkoden aktiveres automatisk når serverkontrollen er fullført. Du får straks en egen e-post med koden.</p><p>Signert kopi ligger vedlagt.</p><p>Vennlig hilsen<br><b>Sandra Marents</b><br>Googa</p></body></html>';
    googa_send_email_with_attachment((string)$app['email'],'Googa ambassadøravtale – signert kopi',$html,$pdf,'Googa-ambassadoravtale-signert-'.$app['id'].'.pdf');
    googa_send_email_with_attachment('sandramarents@gmail.com','Ny signert Googa-ambassadørsøknad',$html,$pdf,'Googa-ambassadoravtale-signert-'.$app['id'].'.pdf');
}

function googa_send_ambassador_approved_email(array $app, array $ambassador): bool
{
    $link = GOOGA_PUBLIC_BASE_URL . '/?amb=' . rawurlencode((string)$ambassador['code']);
    $html = '<!doctype html><html lang="no"><body style="margin:0;background:#e8f7f8;font-family:Arial,sans-serif;color:#103654"><main style="max-width:620px;margin:28px auto;background:#fffdf7;border-radius:24px;padding:30px"><p style="font-weight:bold;color:#087f89">GOOGA AMBASSADØR</p><h1>Avtalen er aktiv 🎉</h1><p>Hei ' . htmlspecialchars((string)$app['name'], ENT_QUOTES, 'UTF-8') . '. Den automatiske kontrollen er fullført, avtalen har trådt i kraft og ambassadørkoden din er aktiv:</p><p style="font-size:28px;font-weight:bold;letter-spacing:.08em">' . htmlspecialchars((string)$ambassador['code'], ENT_QUOTES, 'UTF-8') . '</p><p><a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#087f89;color:#fff;text-decoration:none;padding:14px 20px;border-radius:14px;font-weight:bold">Åpne og del ambassadørlenken</a></p><p>Husk å merke innholdet tydelig som reklame når du kan få provisjon.</p><p>Vennlig hilsen<br><b>Sandra Marents</b><br>Googa</p></main></body></html>';
    return googa_send_email_with_attachment((string)$app['email'], 'Googa-ambassadøren din er aktiv', $html, (string)file_get_contents((string)$app['signed_pdf_path']), 'Googa-ambassadoravtale-signert-'.$app['id'].'.pdf');
}

function googa_ambassador_pdf_for_token(string $token): string
{
    $data=googa_ambassador_read_store();$found=googa_ambassador_find_by_token($data,$token);if(!$found)throw new RuntimeException('Fant ikke avtalen.');$app=$found['application'];$path=in_array((string)$app['status'],['signed','activating','activation_pending','active','approved'],true)?(string)($app['signed_pdf_path']??''):(string)($app['issued_pdf_path']??'');if($path===''||!is_file($path))throw new RuntimeException('Avtalefilen mangler.');return (string)file_get_contents($path);
}

function googa_ambassador_applications(): array
{
    $applications = array_values(googa_ambassador_read_store()['applications']);
    usort($applications, static fn($a, $b) => strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? '')));
    return $applications;
}

function googa_ambassador_application_by_id(string $id): ?array
{
    if (!preg_match('/^gaa_[a-f0-9]{24}$/', $id)) return null;
    $data = googa_ambassador_read_store();
    return is_array($data['applications'][$id] ?? null) ? $data['applications'][$id] : null;
}

function googa_ambassador_suggest_code(array $application): string
{
    $first = preg_split('/\s+/u', trim((string)($application['name'] ?? 'GOOGA')))[0] ?? 'GOOGA';
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $first) ?: 'GOOGA';
    $base = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $ascii) ?? 'GOOGA');
    $base = substr($base !== '' ? $base : 'GOOGA', 0, 10);
    return $base . substr(strtoupper(hash('sha256', (string)($application['id'] ?? ''))), 0, 4);
}

function googa_ambassador_mark_activation_pending(string $applicationId): array
{
    return googa_ambassador_store_transaction(function(array &$data) use ($applicationId) {
        $app = $data['applications'][$applicationId] ?? null;
        if (!is_array($app)) throw new RuntimeException('Fant ikke ambassadøravtalen.');
        if (in_array((string)($app['status'] ?? ''), ['active','approved'], true)) return $app;
        $app['status'] = 'activation_pending';
        $app['activation_message'] = 'Signaturen er gyldig. Automatisk aktivering forsøkes på nytt.';
        $app['activation_next_attempt_at'] = (new DateTimeImmutable(googa_now()))->modify('+5 minutes')->format(DATE_ATOM);
        $app['events'][] = ['type' => 'activation_pending', 'at' => googa_now()];
        $data['applications'][$applicationId] = $app;
        return $app;
    });
}

function googa_ambassador_record_activation_email(string $applicationId, bool $sent): void
{
    googa_ambassador_store_transaction(function(array &$data) use ($applicationId, $sent) {
        $app = $data['applications'][$applicationId] ?? null;
        if (!is_array($app)) return;
        $app['activation_email_sent'] = $sent;
        $app['activation_email_last_attempt_at'] = googa_now();
        $app['events'][] = ['type' => $sent ? 'activation_email_sent' : 'activation_email_failed', 'at' => googa_now()];
        $data['applications'][$applicationId] = $app;
    });
}

function googa_ambassador_activate_signed(string $applicationId): array
{
    require_once __DIR__ . '/stripe.php';
    $app = googa_ambassador_store_transaction(function(array &$store) use ($applicationId) {
        $candidate = $store['applications'][$applicationId] ?? null;
        if (!is_array($candidate)) throw new RuntimeException('Fant ikke ambassadøravtalen.');
        if (in_array((string)($candidate['status'] ?? ''), ['active','approved'], true)) return $candidate;
        if (!in_array((string)($candidate['status'] ?? ''), ['signed','activating','activation_pending'], true)) throw new RuntimeException('Avtalen er ikke ferdig signert.');
        if (empty($candidate['signed_at']) || empty($candidate['signed_name']) || empty($candidate['signature_receipt_hash'])) throw new RuntimeException('Signaturbeviset er ufullstendig.');
        if (!hash_equals((string)$candidate['document_hash'], googa_ambassador_document_hash((array)$candidate['document']))) throw new RuntimeException('Avtaledokumentet har endret seg.');
        $signedPath = (string)($candidate['signed_pdf_path'] ?? '');
        if ($signedPath === '' || !is_file($signedPath) || !hash_equals((string)($candidate['signed_pdf_hash'] ?? ''), hash_file('sha256', $signedPath))) throw new RuntimeException('Den signerte PDF-en kunne ikke valideres.');
        $candidate['status'] = 'activating';
        $candidate['activation_started_at'] = googa_now();
        $candidate['activation_attempts'] = (int)($candidate['activation_attempts'] ?? 0) + 1;
        $candidate['activation_message'] = 'Automatisk kontroll og aktivering pågår.';
        $candidate['events'][] = ['type' => 'activation_started', 'at' => $candidate['activation_started_at']];
        $store['applications'][$applicationId] = $candidate;
        return $candidate;
    });

    $data = googa_load_data();
    $accountEmail = googa_normalize_email((string)($app['account_email'] ?? ''));
    if (!isset($data['users'][$accountEmail]) || !googa_ambassador_user_eligible((array)$data['users'][$accountEmail])) throw new RuntimeException('Kunden oppfyller ikke lenger vilkåret om et aktivt kvalifisert abonnement.');

    $ambassador = null;
    foreach ((array)($data['ambassadors'] ?? []) as $id => $existing) {
        if (!is_array($existing)) continue;
        $sameAgreement = (string)($existing['agreement_id'] ?? '') === $applicationId;
        $sameActiveEmail = googa_normalize_email((string)($existing['email'] ?? '')) === $accountEmail && (string)($existing['status'] ?? '') === 'active';
        if ($sameAgreement || $sameActiveEmail) {
            $existing['id'] = (string)$id;
            $ambassador = $existing;
            break;
        }
    }
    if (!is_array($ambassador)) {
        $baseCode = googa_ambassador_suggest_code($app);
        $code = $baseCode;
        $used = array_map(static fn($item) => strtoupper((string)($item['code'] ?? '')), array_filter((array)($data['ambassadors'] ?? []), 'is_array'));
        for ($suffix = 4; in_array($code, $used, true); $suffix += 2) {
            $code = substr($baseCode, 0, 10) . substr(strtoupper(hash('sha256', $applicationId)), 0, min($suffix, 20));
            if ($suffix > 20) throw new RuntimeException('Kunne ikke opprette en unik ambassadørkode.');
        }
        $ambassador = googa_stripe_create_ambassador($data, $accountEmail, $code, $applicationId);
        $ambassador['agreement_id'] = $applicationId;
        $ambassador['agreement_version'] = (string)($app['document']['version'] ?? '');
        $ambassador['agreement_hash'] = (string)($app['document_hash'] ?? '');
        $data['ambassadors'][(string)$ambassador['id']] = $ambassador;
        googa_save_data($data);
    }

    $active = googa_ambassador_store_transaction(function(array &$store) use ($applicationId, $ambassador) {
        $candidate = $store['applications'][$applicationId] ?? null;
        if (!is_array($candidate)) throw new RuntimeException('Fant ikke ambassadøravtalen.');
        $candidate['status'] = 'active';
        $candidate['activated_at'] = $candidate['activated_at'] ?? googa_now();
        $candidate['activated_by'] = 'automatic-server-validation';
        $candidate['ambassador_id'] = (string)$ambassador['id'];
        $candidate['ambassador_code'] = (string)$ambassador['code'];
        $candidate['activation_message'] = 'Avtalen er aktiv og ambassadørkoden er klar.';
        $candidate['activation_next_attempt_at'] = null;
        $candidate['events'][] = ['type' => 'activated_automatically', 'at' => googa_now()];
        $store['applications'][$applicationId] = $candidate;
        return $candidate;
    });
    if (empty($active['activation_email_sent'])) {
        $sent = googa_send_ambassador_approved_email($active, $ambassador);
        googa_ambassador_record_activation_email($applicationId, $sent);
        $active['activation_email_sent'] = $sent;
    }
    return $active;
}

function googa_ambassador_retry_automatic_activations(): array
{
    $counts = ['checked' => 0, 'activated' => 0, 'pending' => 0, 'email_retried' => 0];
    foreach (googa_ambassador_applications() as $app) {
        $status = (string)($app['status'] ?? '');
        $due = strtotime((string)($app['activation_next_attempt_at'] ?? '')) ?: 0;
        $stale = strtotime((string)($app['activation_started_at'] ?? '')) ?: 0;
        $shouldActivate = $status === 'signed' || ($status === 'activation_pending' && $due <= time()) || ($status === 'activating' && $stale <= time() - 300);
        $shouldRetryEmail = in_array($status, ['active','approved'], true) && empty($app['activation_email_sent']);
        if (!$shouldActivate && !$shouldRetryEmail) continue;
        $counts['checked']++;
        try {
            $activated = googa_ambassador_activate_signed((string)$app['id']);
            if ($shouldActivate) $counts['activated']++;
            if ($shouldRetryEmail && !empty($activated['activation_email_sent'])) $counts['email_retried']++;
        } catch (Throwable $error) {
            error_log('Googa ambassador retry pending for ' . (string)$app['id'] . ': ' . $error->getMessage());
            googa_ambassador_mark_activation_pending((string)$app['id']);
            $counts['pending']++;
        }
    }
    return $counts;
}

function googa_ambassador_owner_pdf(string $applicationId): string
{
    $app = googa_ambassador_application_by_id($applicationId);
    if (!$app) throw new RuntimeException('Fant ikke avtalen.');
    $path = in_array((string)($app['status'] ?? ''), ['signed','activating','activation_pending','active','approved'], true) ? (string)($app['signed_pdf_path'] ?? '') : (string)($app['issued_pdf_path'] ?? '');
    if ($path === '' || !is_file($path)) throw new RuntimeException('Avtalefilen mangler.');
    return (string)file_get_contents($path);
}
