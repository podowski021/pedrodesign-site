<?php
/* Cron dos posts: a Hostinger roda este arquivo a cada 5 minutos e ele publica o que chegou na hora.
   Comando (Tarefas Cron → Personalizado): /usr/bin/php /home/<usuario>/domains/pedrodesign.store/public_html/painel/cron.php
   Pelo navegador ele não faz nada. */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
$_SERVER['HTTP_HOST'] = 'pedrodesign.store';
$_SERVER['HTTPS'] = 'on';
function out($v, int $code = 200): never { echo json_encode($v, JSON_UNESCAPED_UNICODE), "\n"; exit($code >= 400 ? 1 : 0); }
function data_dir(): string {
    $d = dirname($_SERVER['DOCUMENT_ROOT']) . '/painel-data';
    if (!is_dir($d)) @mkdir($d, 0700, true);
    return $d;
}
function jread(string $f): array { if (!file_exists($f)) return []; $j = json_decode((string)file_get_contents($f), true); return is_array($j) ? $j : []; }
function jmod(string $f, callable $fn) {
    $h = fopen($f, 'c+'); if (!$h) out(['error' => 'Não consegui abrir os dados'], 500);
    flock($h, LOCK_EX); $txt = stream_get_contents($h); $data = $txt ? (json_decode($txt, true) ?: []) : [];
    $ret = $fn($data); ftruncate($h, 0); rewind($h); fwrite($h, json_encode($data, JSON_UNESCAPED_UNICODE)); fflush($h); flock($h, LOCK_UN); fclose($h);
    return $ret;
}
function new_id(): string { return bin2hex(random_bytes(10)); }
require __DIR__ . '/posts.php';
$c = ps_cfg(); $c['cronCli'] = time(); ps_save_cfg($c);
out(ps_run());
