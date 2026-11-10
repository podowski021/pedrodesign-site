<?php
/* Visitas do site (pedrodesign.store, /links, bio.pedrodesign.store).
   Anônimo: não guarda IP, nome nem cookie de terceiros. Cada aparelho recebe um código aleatório (localStorage).
   Um arquivo por dia em painel-data/visitas/AAAA-MM-DD.jsonl, apagado depois de 400 dias. */
declare(strict_types=1);

const PD_TZ = 'America/Sao_Paulo';

function an_dir(): string {
    $d = data_dir() . '/visitas';
    if (!is_dir($d)) @mkdir($d, 0700, true);
    return $d;
}
function an_today(): string { return (new DateTime('now', new DateTimeZone(PD_TZ)))->format('Y-m-d'); }

function an_ua(string $ua): array {
    $m = preg_match('/Mobi|Android|iPhone|iPod/i', $ua) ? 'celular' : (preg_match('/iPad|Tablet/i', $ua) ? 'tablet' : 'computador');
    $os = preg_match('/iPhone|iPad|iPod/i', $ua) ? 'iOS' : (preg_match('/Android/i', $ua) ? 'Android' : (preg_match('/Mac OS X|Macintosh/i', $ua) ? 'Mac' : (preg_match('/Windows/i', $ua) ? 'Windows' : (preg_match('/Linux/i', $ua) ? 'Linux' : 'Outro'))));
    $app = preg_match('/Instagram/i', $ua) ? 'Instagram' : (preg_match('/FBAN|FBAV|FB_IAB/i', $ua) ? 'Facebook' : (preg_match('/WhatsApp/i', $ua) ? 'WhatsApp' : (preg_match('/LinkedInApp/i', $ua) ? 'LinkedIn' : (preg_match('/musical_ly|TikTok|BytedanceWebview/i', $ua) ? 'TikTok' : ''))));
    return [$m, $os, $app];
}
function an_bot(string $ua): bool {
    return $ua === '' || (bool)preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp\/|headless|lighthouse|pingdom|uptime|curl|wget|python|httpclient|monitor/i', $ua);
}

/* ---------- recebe eventos (público) ---------- */
function an_track(): never {
    $ua = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
    if (an_bot($ua)) out(['ok' => true]);
    throttle('visitas', 900, 3600);
    $raw = file_get_contents('php://input') ?: '';
    if (strlen($raw) > 20000) out(['ok' => true]);
    $b = json_decode($raw, true);
    if (!is_array($b) || !is_array($b['ev'] ?? null)) out(['ok' => true]);
    $id = fn($s) => substr(preg_replace('/[^a-zA-Z0-9]/', '', (string)$s), 0, 24);
    $txt = fn($s, $n) => mb_substr(trim(preg_replace('/[\x00-\x1F]/u', ' ', (string)$s)), 0, $n);
    [$dev, $os, $app] = an_ua($ua);
    $base = ['v' => $id($b['v'] ?? ''), 's' => $id($b['s'] ?? '')];
    if ($base['v'] === '' || $base['s'] === '') out(['ok' => true]);
    $lines = '';
    $now = time();
    foreach (array_slice($b['ev'], 0, 20) as $e) {
        if (!is_array($e)) continue;
        $t = (string)($e['e'] ?? '');
        if (!in_array($t, ['v', 'l', 'c'], true)) continue;
        $r = $base + ['t' => $now, 'e' => $t, 'pv' => $id($e['pv'] ?? ''), 'p' => $txt($e['p'] ?? '', 20)];
        if ($t === 'v') {
            $r += ['r' => $txt($e['r'] ?? '', 120), 'us' => $txt($e['us'] ?? '', 40), 'um' => $txt($e['um'] ?? '', 40), 'uc' => $txt($e['uc'] ?? '', 60),
                'lg' => $txt($e['lg'] ?? '', 10), 'tz' => $txt($e['tz'] ?? '', 40), 'w' => (int)($e['w'] ?? 0), 'n' => !empty($e['n']) ? 1 : 0,
                'd' => $dev, 'os' => $os, 'app' => $app];
        } elseif ($t === 'l') {
            $r += ['du' => max(0, min(7200, (int)($e['du'] ?? 0))), 'sc' => max(0, min(100, (int)($e['sc'] ?? 0)))];
        } else {
            $r += ['k' => $txt($e['k'] ?? '', 60)];
        }
        $lines .= json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
    }
    if ($lines !== '') {
        $dir = an_dir();
        file_put_contents("$dir/" . an_today() . '.jsonl', $lines, FILE_APPEND | LOCK_EX);
        /* limpeza: 1 vez por dia apaga arquivos com mais de 400 dias */
        $flag = "$dir/.limpeza";
        if (!file_exists($flag) || filemtime($flag) < $now - 86400) {
            @touch($flag);
            foreach (glob("$dir/*.jsonl") ?: [] as $f) if (filemtime($f) < $now - 400 * 86400) @unlink($f);
        }
    }
    out(['ok' => true]);
}

/* ---------- origem da visita ---------- */
function an_source(array $v): string {
    $us = strtolower($v['us'] ?? '');
    $map = ['ig' => 'Instagram', 'instagram' => 'Instagram', 'fb' => 'Facebook', 'facebook' => 'Facebook', 'wa' => 'WhatsApp', 'whatsapp' => 'WhatsApp',
        'google' => 'Google', 'linkedin' => 'LinkedIn', 'tiktok' => 'TikTok', 'youtube' => 'YouTube', 'email' => 'E-mail', 'qr' => 'QR code'];
    if ($us !== '') return $map[$us] ?? ucfirst($us);
    if (!empty($v['app'])) return $v['app'];
    $h = strtolower(parse_url((string)($v['r'] ?? ''), PHP_URL_HOST) ?: (string)($v['r'] ?? ''));
    if ($h === '') return ($v['p'] ?? '') === 'bio' ? 'Instagram (bio)' : 'Direto ou WhatsApp';
    $h = preg_replace('/^(www|m|l|lm|mobile)\./', '', $h);
    $rules = ['instagram.com' => 'Instagram', 'facebook.com' => 'Facebook', 'fb.com' => 'Facebook', 'whatsapp.com' => 'WhatsApp', 'wa.me' => 'WhatsApp',
        'google.' => 'Google', 'bing.com' => 'Bing', 'duckduckgo' => 'DuckDuckGo', 'yahoo.' => 'Yahoo', 'linkedin.com' => 'LinkedIn', 'lnkd.in' => 'LinkedIn',
        't.co' => 'X / Twitter', 'twitter.com' => 'X / Twitter', 'x.com' => 'X / Twitter', 'tiktok.com' => 'TikTok', 'youtube.com' => 'YouTube',
        'pinterest.' => 'Pinterest', 'chatgpt.com' => 'ChatGPT', 'openai.com' => 'ChatGPT', 'claude.ai' => 'Claude', 'perplexity' => 'Perplexity', 'pedrodesign.store' => 'Seu próprio site', 'movex' => 'Movex'];
    foreach ($rules as $k => $n) if (str_contains($h, $k)) return $n;
    return $h;
}
function an_country(string $tz, string $lg): string {
    $m = ['America/Sao_Paulo' => 'Brasil', 'America/Bahia' => 'Brasil', 'America/Fortaleza' => 'Brasil', 'America/Recife' => 'Brasil', 'America/Manaus' => 'Brasil', 'America/Belem' => 'Brasil',
        'America/Cuiaba' => 'Brasil', 'America/Campo_Grande' => 'Brasil', 'America/Porto_Velho' => 'Brasil', 'America/Boa_Vista' => 'Brasil', 'America/Rio_Branco' => 'Brasil', 'America/Maceio' => 'Brasil',
        'America/Araguaina' => 'Brasil', 'America/Santarem' => 'Brasil', 'America/Noronha' => 'Brasil', 'America/Asuncion' => 'Paraguai', 'America/Argentina/Buenos_Aires' => 'Argentina',
        'America/Montevideo' => 'Uruguai', 'America/Santiago' => 'Chile', 'America/Bogota' => 'Colômbia', 'America/Lima' => 'Peru', 'America/Mexico_City' => 'México',
        'America/New_York' => 'Estados Unidos', 'America/Chicago' => 'Estados Unidos', 'America/Denver' => 'Estados Unidos', 'America/Los_Angeles' => 'Estados Unidos', 'America/Phoenix' => 'Estados Unidos',
        'America/Toronto' => 'Canadá', 'America/Vancouver' => 'Canadá', 'Europe/Lisbon' => 'Portugal', 'Europe/Dublin' => 'Irlanda', 'Europe/London' => 'Reino Unido', 'Europe/Madrid' => 'Espanha',
        'Europe/Paris' => 'França', 'Europe/Rome' => 'Itália', 'Europe/Berlin' => 'Alemanha', 'Europe/Amsterdam' => 'Holanda', 'Europe/Brussels' => 'Bélgica', 'Europe/Zurich' => 'Suíça',
        'Europe/Vienna' => 'Áustria', 'Asia/Tokyo' => 'Japão', 'Australia/Sydney' => 'Austrália', 'Atlantic/Azores' => 'Portugal', 'Atlantic/Madeira' => 'Portugal'];
    if (isset($m[$tz])) return $m[$tz];
    if ($tz !== '') { $p = explode('/', $tz); return str_replace('_', ' ', end($p)) . ' (fuso)'; }
    return 'Não informado';
}

/* ---------- números para o painel ---------- */
function an_stats(array $q): never {
    $tz = new DateTimeZone(PD_TZ);
    $days = max(1, min(365, (int)($q['dias'] ?? 30)));
    $end = new DateTime('today', $tz);
    $start = (clone $end)->modify('-' . ($days - 1) . ' days');
    $prevStart = (clone $start)->modify("-$days days");
    $dir = an_dir();
    $views = []; $leave = []; $clicks = [];
    $prev = ['v' => [], 'wa' => 0];
    for ($d = clone $prevStart; $d <= $end; $d->modify('+1 day')) {
        $f = "$dir/" . $d->format('Y-m-d') . '.jsonl';
        if (!file_exists($f)) continue;
        $isPrev = $d < $start;
        $h = fopen($f, 'r');
        while (($ln = fgets($h)) !== false) {
            $r = json_decode($ln, true); if (!is_array($r)) continue;
            if ($isPrev) {
                if ($r['e'] === 'v') $prev['v'][$r['v']] = 1;
                elseif ($r['e'] === 'c' && str_starts_with((string)($r['k'] ?? ''), 'WhatsApp')) $prev['wa']++;
                continue;
            }
            if ($r['e'] === 'v') $views[] = $r;
            elseif ($r['e'] === 'l') { $k = $r['pv'] ?: $r['s']; $o = $leave[$k] ?? ['du' => 0, 'sc' => 0]; $leave[$k] = ['du' => max($o['du'], $r['du'] ?? 0), 'sc' => max($o['sc'], $r['sc'] ?? 0)]; }
            else $clicks[] = $r;
        }
        fclose($h);
    }
    $dayKey = fn($t) => (new DateTime('@' . $t))->setTimezone($tz)->format('Y-m-d');
    $hour = fn($t) => (int)(new DateTime('@' . $t))->setTimezone($tz)->format('G');
    $inc = function (array &$a, string $k, int $n = 1) { $a[$k] = ($a[$k] ?? 0) + $n; };
    $top = function (array $a, int $n = 12) { arsort($a); return array_map(fn($k, $v) => [$k, $v], array_slice(array_keys($a), 0, $n), array_slice(array_values($a), 0, $n)); };

    $visitors = []; $sessions = []; $daily = [];
    for ($d = clone $start; $d <= $end; $d->modify('+1 day')) $daily[$d->format('Y-m-d')] = ['vis' => [], 'pv' => 0, 'wa' => 0];
    $src = []; $pages = []; $dev = []; $os = []; $country = []; $lang = []; $hours = array_fill(0, 24, 0); $newv = 0; $retv = [];
    foreach ($views as $r) {
        $visitors[$r['v']] = 1;
        $dk = $dayKey($r['t']); if (isset($daily[$dk])) { $daily[$dk]['vis'][$r['v']] = 1; $daily[$dk]['pv']++; }
        $pn = ['home' => 'Site (página inicial)', 'links' => 'Árvore de links', 'bio' => 'bio.pedrodesign.store', '404' => 'Página não encontrada'][$r['p']] ?? $r['p'];
        $inc($pages, $pn);
        $hours[$hour($r['t'])]++;
        if (!isset($sessions[$r['s']])) {
            $sessions[$r['s']] = ['t' => $r['t'], 'src' => an_source($r), 'p' => $pn, 'd' => $r['d'] ?? '', 'os' => $r['os'] ?? '', 'c' => an_country((string)($r['tz'] ?? ''), (string)($r['lg'] ?? '')), 'pvs' => [], 'wa' => false, 'k' => [], 'last' => $r['t']];
            $inc($src, $sessions[$r['s']]['src']);
            $inc($dev, ucfirst($r['d'] ?? 'outro'));
            $inc($os, $r['os'] ?? 'Outro');
            $inc($country, $sessions[$r['s']]['c']);
            $lgn = ['pt' => 'Português', 'en' => 'Inglês', 'es' => 'Espanhol', 'fr' => 'Francês', 'it' => 'Italiano', 'de' => 'Alemão'][strtolower(substr((string)($r['lg'] ?? ''), 0, 2))] ?? 'Outro';
            $inc($lang, $lgn);
            if (!empty($r['n'])) $newv++; else $retv[$r['v']] = 1;
        }
        $sessions[$r['s']]['pvs'][] = $r['pv'];
        $sessions[$r['s']]['last'] = max($sessions[$r['s']]['last'], $r['t']);
    }
    $ck = []; $proj = []; $waBy = []; $waTotal = 0;
    foreach ($clicks as $r) {
        $k = (string)($r['k'] ?? ''); if ($k === '') continue;
        $inc($ck, $k);
        if (str_starts_with($k, 'Projeto: ')) $inc($proj, substr($k, 9));
        if (str_starts_with($k, 'WhatsApp')) {
            $waTotal++; $inc($waBy, trim(substr($k, 8), ' ·-') ?: 'WhatsApp');
            $dk = $dayKey($r['t']); if (isset($daily[$dk])) $daily[$dk]['wa']++;
            if (isset($sessions[$r['s']])) $sessions[$r['s']]['wa'] = true;
        }
        if (isset($sessions[$r['s']])) { $sessions[$r['s']]['k'][] = $k; $sessions[$r['s']]['last'] = max($sessions[$r['s']]['last'], $r['t']); }
    }
    /* tempo e rolagem */
    $durB = ['Menos de 10 s' => 0, '10 a 30 s' => 0, '30 s a 1 min' => 0, '1 a 3 min' => 0, 'Mais de 3 min' => 0];
    $scB = ['Até 25%' => 0, '25 a 50%' => 0, '50 a 75%' => 0, '75 a 100%' => 0];
    $totDur = 0; $nDur = 0; $waS = 0;
    foreach ($sessions as $sid => &$s) {
        $du = 0; $sc = 0;
        foreach (array_unique($s['pvs']) as $pv) { $l = $leave[$pv] ?? null; if ($l) { $du += $l['du']; $sc = max($sc, $l['sc']); } }
        $s['du'] = $du; $s['sc'] = $sc;
        if ($du > 0) { $totDur += $du; $nDur++; }
        $durB[$du < 10 ? 'Menos de 10 s' : ($du < 30 ? '10 a 30 s' : ($du < 60 ? '30 s a 1 min' : ($du < 180 ? '1 a 3 min' : 'Mais de 3 min')))]++;
        if ($s['p'] === 'Site (página inicial)' && $sc > 0) $scB[$sc <= 25 ? 'Até 25%' : ($sc <= 50 ? '25 a 50%' : ($sc <= 75 ? '50 a 75%' : '75 a 100%'))]++;
        if ($s['wa']) $waS++;
    }
    unset($s);
    $live = 0; $now = time();
    foreach ($sessions as $s) if ($s['last'] > $now - 300) $live++;
    foreach ($leave as $l) {}
    uasort($sessions, fn($a, $b) => $b['t'] <=> $a['t']);
    $recent = [];
    foreach (array_slice($sessions, 0, 40) as $s) {
        $recent[] = ['t' => $s['t'], 'src' => $s['src'], 'p' => $s['p'], 'd' => $s['d'], 'os' => $s['os'], 'c' => $s['c'], 'du' => $s['du'], 'sc' => $s['sc'], 'wa' => $s['wa'], 'k' => array_values(array_unique($s['k']))];
    }
    $nS = count($sessions);
    out([
        'dias' => $days, 'de' => $start->format('Y-m-d'), 'ate' => $end->format('Y-m-d'),
        'kpi' => ['visitantes' => count($visitors), 'visitas' => $nS, 'paginas' => count($views), 'tempo' => $nDur ? (int)round($totDur / $nDur) : 0,
            'whatsapp' => $waTotal, 'taxa' => $nS ? round(100 * $waS / $nS, 1) : 0, 'agora' => $live, 'novos' => $newv, 'voltaram' => count($retv),
            'antes' => ['visitantes' => count($prev['v']), 'whatsapp' => $prev['wa']]],
        'diario' => array_map(fn($k, $v) => [$k, count($v['vis']), $v['pv'], $v['wa']], array_keys($daily), array_values($daily)),
        'origem' => $top($src), 'paginas' => $top($pages), 'cliques' => $top($ck, 20), 'whatsapp' => $top($waBy), 'projetos' => $top($proj),
        'aparelhos' => $top($dev), 'sistemas' => $top($os), 'paises' => $top($country), 'idiomas' => $top($lang),
        'tempo' => array_map(fn($k, $v) => [$k, $v], array_keys($durB), array_values($durB)),
        'rolagem' => array_map(fn($k, $v) => [$k, $v], array_keys($scB), array_values($scB)),
        'horas' => $hours, 'recentes' => $recent,
    ]);
}
