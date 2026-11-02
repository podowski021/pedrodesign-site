<?php
/* Painel Pedro Design — API simples em arquivos JSON.
   Os dados ficam FORA da pasta pública (../painel-data), então não vão para o GitHub
   e não são apagados quando o site é atualizado. */
declare(strict_types=1);

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_name('pd_painel');
session_set_cookie_params(['lifetime' => 60 * 60 * 24 * 30, 'path' => '/painel/', 'secure' => $secure, 'httponly' => true, 'samesite' => 'Strict']);
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

/* ---------- pasta de dados ---------- */
function data_dir(): string {
    $root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
    $cands = [];
    if ($root !== '') $cands[] = dirname($root) . '/painel-data';
    $cands[] = __DIR__ . '/data';
    foreach ($cands as $d) {
        if (is_dir($d) || @mkdir($d, 0700, true)) {
            if (is_writable($d)) {
                if (str_starts_with($d, __DIR__) && !file_exists("$d/.htaccess")) {
                    @file_put_contents("$d/.htaccess", "Require all denied\nDeny from all\n");
                }
                return $d;
            }
        }
    }
    out(['error' => 'Sem permissão para salvar os dados'], 500);
}
function out($v, int $code = 200): never { http_response_code($code); echo json_encode($v, JSON_UNESCAPED_UNICODE); exit; }
function body(): array {
    $raw = file_get_contents('php://input') ?: '';
    if (strlen($raw) > 8_000_000) out(['error' => 'Muito grande'], 413);
    $j = json_decode($raw, true);
    return is_array($j) ? $j : [];
}
function jread(string $f): array {
    if (!file_exists($f)) return [];
    $j = json_decode((string)file_get_contents($f), true);
    return is_array($j) ? $j : [];
}
/* lê-modifica-grava com trava, para não corromper o arquivo */
function jmod(string $f, callable $fn) {
    $h = fopen($f, 'c+');
    if (!$h) out(['error' => 'Não consegui abrir os dados'], 500);
    flock($h, LOCK_EX);
    $txt = stream_get_contents($h);
    $data = $txt ? (json_decode($txt, true) ?: []) : [];
    $ret = $fn($data);
    ftruncate($h, 0); rewind($h);
    fwrite($h, json_encode($data, JSON_UNESCAPED_UNICODE));
    fflush($h); flock($h, LOCK_UN); fclose($h);
    return $ret;
}
function new_id(): string { return bin2hex(random_bytes(10)); }
function client_ip(): string { return $_SERVER['REMOTE_ADDR'] ?? '0'; }
/* limite de tentativas por IP (login e formulário do site) */
function throttle(string $kind, int $max, int $window): void {
    $f = data_dir() . "/throttle-$kind.json";
    $ip = hash('sha256', client_ip());
    $ok = jmod($f, function (&$d) use ($ip, $max, $window) {
        $now = time();
        foreach ($d as $k => $v) { $d[$k] = array_values(array_filter($v, fn($t) => $t > $now - $window)); if (!$d[$k]) unset($d[$k]); }
        $n = count($d[$ip] ?? []);
        if ($n >= $max) return false;
        $d[$ip][] = $now;
        return true;
    });
    if (!$ok) out(['error' => 'Muitas tentativas. Tente de novo mais tarde.'], 429);
}

/* ---------- e-mail (SMTP) ---------- */
function mail_cfg(): array { return jread(data_dir() . '/mail.json'); }
function smtp_send(array $cfg, array $to, string $subject, string $text, string $html, ?array $att = null): void {
    $host = $cfg['host'] ?? ''; $port = (int)($cfg['port'] ?? 465); $user = $cfg['user'] ?? ''; $pass = $cfg['pass'] ?? '';
    if (!$host || !$user || !$pass) throw new RuntimeException('E-mail ainda não configurado no painel');
    $from = $user; $name = $cfg['name'] ?? 'Pedro Design';
    $ssl = $port === 465;
    $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]]);
    $fp = @stream_socket_client(($ssl ? 'ssl://' : 'tcp://') . "$host:$port", $en, $es, 20, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) throw new RuntimeException("Não consegui conectar ao servidor de e-mail ($es)");
    stream_set_timeout($fp, 25);
    $read = function () use ($fp) { $r = ''; while (($l = fgets($fp, 1024)) !== false) { $r .= $l; if (strlen($l) < 4 || $l[3] === ' ') break; } return $r; };
    $cmd = function (string $c, array $ok) use ($fp, $read) { if ($c !== '') fwrite($fp, $c . "\r\n"); $r = $read(); if (!in_array((int)substr($r, 0, 3), $ok, true)) throw new RuntimeException('Servidor de e-mail recusou: ' . trim(preg_replace('/\s+/', ' ', $r))); return $r; };
    $cmd('', [220]);
    $ehlo = 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'localhost');
    $cmd($ehlo, [250]);
    if (!$ssl) { $cmd('STARTTLS', [220]); if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) throw new RuntimeException('Falha no TLS'); $cmd($ehlo, [250]); }
    $cmd('AUTH LOGIN', [334]); $cmd(base64_encode($user), [334]);
    try { $cmd(base64_encode($pass), [235]); } catch (RuntimeException $e) { throw new RuntimeException('E-mail ou senha do e-mail incorretos'); }
    $cmd("MAIL FROM:<$from>", [250]);
    foreach ($to as $t) $cmd("RCPT TO:<$t>", [250, 251]);
    $cmd('DATA', [354]);
    $enc = fn($s) => '=?UTF-8?B?' . base64_encode($s) . '?=';
    $b1 = 'b1_' . bin2hex(random_bytes(8)); $b2 = 'b2_' . bin2hex(random_bytes(8));
    $h = [
        'Date: ' . date(DATE_RFC2822),
        'From: ' . $enc($name) . " <$from>",
        'To: <' . $to[0] . '>',
        "Reply-To: <$from>",
        'Subject: ' . $enc($subject),
        'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . substr(strrchr($from, '@'), 1) . '>',
        'MIME-Version: 1.0',
        "Content-Type: multipart/mixed; boundary=\"$b1\"",
    ];
    $m = implode("\r\n", $h) . "\r\n\r\n--$b1\r\nContent-Type: multipart/alternative; boundary=\"$b2\"\r\n\r\n"
       . "--$b2\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($text))
       . "--$b2\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html))
       . "--$b2--\r\n";
    if ($att) {
        $fn = preg_replace('/[^\w .\-()]/u', '', $att['filename']) ?: 'proposta.pdf';
        $m .= "--$b1\r\nContent-Type: application/pdf; name=\"" . $enc($fn) . "\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"" . $enc($fn) . "\"\r\n\r\n" . chunk_split($att['b64']);
    }
    $m .= "--$b1--\r\n";
    $m = preg_replace('/^\./m', '..', $m);
    $cmd($m . "\r\n.", [250]);
    @fwrite($fp, "QUIT\r\n"); fclose($fp);
}
function mail_html(string $text): string {
    $body = nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
    return '<!doctype html><html><body style="margin:0;padding:24px;background:#F5F1E8;font-family:Arial,Helvetica,sans-serif;color:#0E0E10">'
        . '<div style="max-width:560px;margin:0 auto;background:#FFFFFF;border-radius:16px;padding:28px;font-size:15px;line-height:1.6">' . $body
        . '<div style="margin-top:24px;padding-top:16px;border-top:1px solid #E8E4DB;font-size:12px;color:#7F7A72">Pedro Design · CNPJ 63.543.207/0001-22 · <a href="https://pedrodesign.store" style="color:#7F7A72">pedrodesign.store</a> · WhatsApp +55 45 97400-0755</div>'
        . '</div></body></html>';
}

$dir = data_dir();
$authFile = "$dir/auth.json";
$a = $_GET['a'] ?? '';
$COLS = ['propostas', 'financeiro', 'contatos', 'projetos', 'config'];

/* ---------- público: lead do formulário do site ---------- */
if ($a === 'lead') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(['error' => 'Método'], 405);
    throttle('lead', 6, 3600);
    $b = body();
    $cut = fn($s, $n) => mb_substr(trim((string)($s ?? '')), 0, $n);
    $nome = $cut($b['nome'] ?? '', 120);
    $zap = $cut($b['zap'] ?? '', 40);
    $msg = $cut($b['msg'] ?? '', 1500);
    if ($nome === '' && $zap === '' && $msg === '') out(['ok' => true]);
    $svc = in_array($b['svc'] ?? '', ['lp', 'art', 'both'], true) ? $b['svc'] : 'lp';
    $id = new_id();
    jmod("$dir/contatos.json", function (&$d) use ($id, $nome, $zap, $msg, $svc, $b) {
        $d[$id] = ['nome' => $nome ?: '(sem nome)', 'zap' => $zap, 'msg' => $msg, 'svc' => $svc, 'status' => 'novo',
            'origem' => 'site', 'lang' => substr((string)($b['lang'] ?? ''), 0, 5), 'date' => gmdate('Y-m-d'), 'createdAt' => (int)(microtime(true) * 1000)];
    });
    $cfg = mail_cfg();
    if (!empty($cfg['pass']) && ($cfg['notifyLeads'] ?? true)) {
        $svcN = ['lp' => 'Página', 'art' => 'Artes', 'both' => 'Página + artes'][$svc];
        $txt = "Novo pedido pelo site pedrodesign.store\n\nNome: " . ($nome ?: '-') . "\nWhatsApp: " . ($zap ?: '-') . "\nInteresse: $svcN\nIdioma do site: " . ($b['lang'] ?? '-') . "\n\nMensagem:\n" . ($msg ?: '-') . "\n\nO contato já está no painel: https://pedrodesign.store/painel/";
        try { smtp_send($cfg, [$cfg['notify'] ?? $cfg['user']], 'Novo lead: ' . ($nome ?: 'sem nome') . " · $svcN", $txt, mail_html($txt)); } catch (Throwable $e) { /* o lead já foi salvo */ }
    }
    out(['ok' => true]);
}

/* ---------- login ---------- */
$hasPass = file_exists($authFile);
$logged = !empty($_SESSION['ok']);

if ($a === 'me') out(['logged' => $logged, 'setup' => !$hasPass]);

/* daqui para baixo só aceita POST com o cabeçalho do painel (proteção contra CSRF) */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SERVER['HTTP_X_PAINEL'] ?? '') !== '1') out(['error' => 'Requisição inválida'], 400);

if ($a === 'setup') {
    if ($hasPass) out(['error' => 'A senha já foi criada'], 403);
    $p = (string)(body()['pass'] ?? '');
    if (mb_strlen($p) < 8) out(['error' => 'A senha precisa ter pelo menos 8 caracteres'], 400);
    file_put_contents($authFile, json_encode(['hash' => password_hash($p, PASSWORD_DEFAULT)]), LOCK_EX);
    @chmod($authFile, 0600);
    session_regenerate_id(true); $_SESSION['ok'] = 1;
    out(['ok' => true]);
}
if ($a === 'login') {
    throttle('login', 8, 900);
    $p = (string)(body()['pass'] ?? '');
    $h = jread($authFile)['hash'] ?? '';
    if (!$h || !password_verify($p, $h)) { usleep(600000); out(['error' => 'Senha incorreta'], 401); }
    session_regenerate_id(true); $_SESSION['ok'] = 1;
    out(['ok' => true]);
}
if ($a === 'logout') { $_SESSION = []; session_destroy(); out(['ok' => true]); }

if (!$logged) out(['error' => 'Faça login'], 401);

/* ---------- e-mail ---------- */
if ($a === 'mailget') {
    $c = mail_cfg();
    out(['configured' => !empty($c['pass']), 'host' => $c['host'] ?? 'smtp.hostinger.com', 'port' => (int)($c['port'] ?? 465), 'user' => $c['user'] ?? 'equipe@pedrodesign.com', 'notify' => $c['notify'] ?? ($c['user'] ?? 'equipe@pedrodesign.com'), 'notifyLeads' => $c['notifyLeads'] ?? true]);
}
if ($a === 'mailset') {
    $b = body(); $c = mail_cfg();
    $c['host'] = preg_replace('/[^a-zA-Z0-9.\-]/', '', (string)($b['host'] ?? 'smtp.hostinger.com'));
    $c['port'] = in_array((int)($b['port'] ?? 465), [465, 587], true) ? (int)$b['port'] : 465;
    $u = trim((string)($b['user'] ?? '')); if (!filter_var($u, FILTER_VALIDATE_EMAIL)) out(['error' => 'E-mail inválido'], 400); $c['user'] = $u;
    $n = trim((string)($b['notify'] ?? $u)); $c['notify'] = filter_var($n, FILTER_VALIDATE_EMAIL) ? $n : $u;
    $c['notifyLeads'] = (bool)($b['notifyLeads'] ?? true);
    if (!empty($b['pass'])) $c['pass'] = (string)$b['pass'];
    /* só salva se o teste de envio funcionar */
    if (!empty($b['test'])) {
        try { $t = "Tudo certo! O painel do pedrodesign.store já consegue enviar e-mails por " . $c['user'] . "."; smtp_send($c, [$c['notify']], 'Teste do painel Pedro Design', $t, mail_html($t)); }
        catch (Throwable $e) { out(['error' => $e->getMessage()], 502); }
    }
    $f = data_dir() . '/mail.json';
    file_put_contents($f, json_encode($c, JSON_UNESCAPED_UNICODE), LOCK_EX); @chmod($f, 0600);
    out(['ok' => true]);
}
if ($a === 'mailsend') {
    $b = body(); $c = mail_cfg();
    $to = trim((string)($b['to'] ?? ''));
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) out(['error' => 'E-mail do cliente inválido'], 400);
    $subj = mb_substr(trim((string)($b['subject'] ?? 'Proposta')), 0, 200);
    $text = mb_substr((string)($b['text'] ?? ''), 0, 10000);
    $att = null;
    if (!empty($b['pdf'])) { $raw = base64_decode((string)$b['pdf'], true); if ($raw === false || substr($raw, 0, 4) !== '%PDF') out(['error' => 'PDF inválido'], 400); $att = ['b64' => base64_encode($raw), 'filename' => (string)($b['filename'] ?? 'Proposta.pdf')]; }
    $rcpt = [$to]; if (!empty($b['copy']) && !empty($c['user']) && strcasecmp($c['user'], $to) !== 0) $rcpt[] = $c['user'];
    try { smtp_send($c, $rcpt, $subj, $text, mail_html($text), $att); } catch (Throwable $e) { out(['error' => $e->getMessage()], 502); }
    out(['ok' => true]);
}

/* ---------- dados ---------- */
$b = body();
$c = (string)($b['col'] ?? '');
if (!in_array($c, $COLS, true)) out(['error' => 'Coleção inválida'], 400);
$f = "$dir/$c.json";
$id = isset($b['id']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$b['id']) : '';
$val = $b['data'] ?? null;

switch ($a) {
    case 'list':
        $d = jread($f); $res = [];
        foreach ($d as $k => $v) $res[] = ['id' => (string)$k, 'data' => $v];
        out(['docs' => $res]);
    case 'add':
        if (!is_array($val)) out(['error' => 'Dados inválidos'], 400);
        $nid = new_id();
        jmod($f, function (&$d) use ($nid, $val) { $d[$nid] = $val; });
        out(['id' => $nid]);
    case 'set':
        if (!$id || !is_array($val)) out(['error' => 'Dados inválidos'], 400);
        jmod($f, function (&$d) use ($id, $val) { $d[$id] = $val; });
        out(['ok' => true]);
    case 'update':
        if (!$id || !is_array($val)) out(['error' => 'Dados inválidos'], 400);
        jmod($f, function (&$d) use ($id, $val) { $d[$id] = array_merge(is_array($d[$id] ?? null) ? $d[$id] : [], $val); });
        out(['ok' => true]);
    case 'delete':
        if (!$id) out(['error' => 'Dados inválidos'], 400);
        jmod($f, function (&$d) use ($id) { unset($d[$id]); });
        out(['ok' => true]);
    case 'get':
        $d = jread($f);
        out(['exists' => isset($d[$id]), 'data' => $d[$id] ?? null]);
}
out(['error' => 'Ação desconhecida'], 400);
