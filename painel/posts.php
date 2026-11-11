<?php
/* Agendador de posts do painel (Instagram e Facebook dos clientes, pela API oficial da Meta).
   - Conexão: token de um "usuário do sistema" do Meta Business Suite do Pedro (não expira).
   - Cada cliente = uma Página do Facebook (com o Instagram ligado a ela).
   - As artes ficam em ../painel-data/posts-midia (privado) e são servidas por api.php?a=pm&f=… (nome aleatório),
     porque a Meta precisa baixar a imagem por um endereço público.
   - Na hora marcada o próprio servidor publica (cron da Hostinger, visitas do site ou o painel aberto).
   Dados: ../painel-data/posts-config.json (token, clientes) e ../painel-data/posts.json (agendamentos). */
declare(strict_types=1);

const PS_GRAPH_V = 'v25.0';

function ps_cfg(): array { return jread(data_dir() . '/posts-config.json'); }
function ps_save_cfg(array $c): void { $f = data_dir() . '/posts-config.json'; file_put_contents($f, json_encode($c, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX); @chmod($f, 0600); }
function ps_file(): string { return data_dir() . '/posts.json'; }
function ps_mdir(): string { $d = data_dir() . '/posts-midia'; if (!is_dir($d)) @mkdir($d, 0700, true); return $d; }
function ps_slug(string $s): string {
    $s = mb_strtolower(trim($s));
    $s = strtr($s, ['á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a','é'=>'e','ê'=>'e','è'=>'e','í'=>'i','ì'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ò'=>'o','ú'=>'u','ü'=>'u','ç'=>'c','ñ'=>'n']);
    return trim(preg_replace('/[^a-z0-9]+/', '-', $s), '-');
}
function ps_base(): string { $c = ps_cfg(); return rtrim((string)($c['graph'] ?? 'https://graph.facebook.com'), '/') . '/' . PS_GRAPH_V; }
function ps_site(): string {
    $c = ps_cfg(); if (!empty($c['site'])) return rtrim($c['site'], '/');
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $h = $_SERVER['HTTP_HOST'] ?? 'pedrodesign.store';
    return ($https || $h === 'pedrodesign.store' ? 'https://' : 'http://') . $h;
}

/* chamada à Graph API; devolve [json, erro] */
function ps_api(string $method, string $path, array $params = [], int $timeout = 60): array {
    $url = ps_base() . '/' . ltrim($path, '/');
    $ch = curl_init();
    if ($method === 'GET') { $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($params); curl_setopt($ch, CURLOPT_HTTPGET, true); }
    else { curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)); }
    curl_setopt_array($ch, [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => 15]);
    $raw = (string)curl_exec($ch); $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE); $cerr = curl_error($ch); curl_close($ch);
    $j = json_decode($raw, true);
    if ($code >= 200 && $code < 300 && is_array($j) && !isset($j['error'])) return [$j, ''];
    $m = $j['error']['error_user_msg'] ?? $j['error']['message'] ?? ($cerr ?: "Erro $code");
    return [is_array($j) ? $j : [], ps_err((string)$m, (int)($j['error']['code'] ?? 0))];
}
function ps_err(string $m, int $code): string {
    if ($code === 190) return 'A conexão com a Meta expirou ou foi removida. Gere um token novo e conecte de novo.';
    if ($code === 10 || $code === 200) return 'Falta permissão para essa conta na Meta (' . $m . '). Confira se ela está liberada para o usuário do sistema.';
    if ($code === 4 || $code === 17 || $code === 32 || $code === 613) return 'A Meta pediu para esperar (limite de chamadas). Vou tentar de novo daqui a pouco.';
    if ($code === 9004 || stripos($m, 'media') !== false && stripos($m, 'download') !== false) return 'A Meta não conseguiu baixar a imagem. ' . $m;
    return $m;
}

/* ---------- conexão e clientes ---------- */
function ps_status(): never {
    $c = ps_cfg();
    $cl = array_values(array_map(fn($x) => array_diff_key($x, ['token' => 1]), $c['clientes'] ?? []));
    usort($cl, fn($a, $b) => strcmp($a['nome'], $b['nome']));
    if (empty($c['cronKey'])) { $c['cronKey'] = bin2hex(random_bytes(12)); ps_save_cfg($c); }
    out(['conectado' => !empty($c['token']), 'conta' => $c['conta'] ?? '', 'clientes' => $cl,
        'cron' => ps_site() . '/painel/api.php?a=pcron&k=' . $c['cronKey'], 'cronCmd' => '/usr/bin/php ' . __DIR__ . '/cron.php', 'ultimoCron' => (int)($c['cronCli'] ?? 0)]);
}
function ps_connect(array $b): never {
    $t = preg_replace('/\s+/', '', (string)($b['token'] ?? ''));
    if (!empty($b['remover'])) { $c = ps_cfg(); unset($c['token'], $c['conta']); ps_save_cfg($c); out(['ok' => true]); }
    if (strlen($t) < 40) out(['error' => 'Esse token parece incompleto. Copie de novo o token inteiro.'], 400);
    $c = ps_cfg(); $c['token'] = $t; ps_save_cfg($c);
    [$me, $e] = ps_api('GET', 'me', ['fields' => 'id,name', 'access_token' => $t]);
    if ($e) { unset($c['token']); ps_save_cfg($c); out(['error' => 'A Meta recusou o token: ' . $e], 400); }
    $c['conta'] = (string)($me['name'] ?? '');
    ps_save_cfg($c);
    ps_sync_clients();
    ps_status();
}
/* busca as Páginas (e o Instagram de cada uma) liberadas para o token */
function ps_sync_clients(): void {
    $c = ps_cfg(); $old = $c['clientes'] ?? []; $new = [];
    $after = ''; $guard = 0;
    do {
        $q = ['fields' => 'id,name,access_token,picture{url},instagram_business_account{id,username,profile_picture_url}', 'limit' => 100, 'access_token' => $c['token']];
        if ($after) $q['after'] = $after;
        [$j, $e] = ps_api('GET', 'me/accounts', $q);
        if ($e) out(['error' => 'Não consegui ler as contas dos clientes: ' . $e], 400);
        foreach ($j['data'] ?? [] as $p) {
            $id = (string)$p['id']; $o = $old[$id] ?? [];
            $ig = $p['instagram_business_account'] ?? null;
            $new[$id] = ['id' => $id, 'nome' => (string)$p['name'], 'token' => (string)($p['access_token'] ?? ''),
                'ig' => $ig['id'] ?? '', 'igUser' => $ig['username'] ?? '', 'foto' => $ig['profile_picture_url'] ?? ($p['picture']['data']['url'] ?? ''),
                'slug' => $o['slug'] ?? ps_slug((string)$p['name']), 'apelidos' => $o['apelidos'] ?? ($ig ? [ps_slug((string)$ig['username'])] : [])];
        }
        $after = $j['paging']['cursors']['after'] ?? ''; $more = !empty($j['paging']['next']);
    } while ($more && $after && ++$guard < 20);
    /* nome curto automático: a primeira palavra do nome, quando só um cliente começa com ela (ex.: "capanema") */
    $fw = []; foreach ($new as $id => $x) { $w = explode('-', $x['slug'])[0]; if (strlen($w) >= 4) $fw[$w][] = $id; }
    foreach ($fw as $w => $ids) if (count($ids) === 1 && !isset($old[$ids[0]]['apelidos'])) { $x = &$new[$ids[0]]; if ($w !== $x['slug'] && !in_array($w, $x['apelidos'], true)) $x['apelidos'][] = $w; unset($x); }
    $c['clientes'] = $new; ps_save_cfg($c);
}
function ps_sync(): never { ps_sync_clients(); ps_status(); }
function ps_client(array $b): never {
    $c = ps_cfg(); $id = (string)($b['id'] ?? '');
    if (!isset($c['clientes'][$id])) out(['error' => 'Cliente não encontrado'], 404);
    if (isset($b['slug'])) $c['clientes'][$id]['slug'] = ps_slug((string)$b['slug']) ?: ps_slug($c['clientes'][$id]['nome']);
    if (isset($b['apelidos'])) $c['clientes'][$id]['apelidos'] = array_values(array_filter(array_map(fn($x) => ps_slug((string)$x), (array)$b['apelidos'])));
    ps_save_cfg($c); out(['ok' => true]);
}

/* ---------- artes ---------- */
function ps_upload(array $b): never {
    $d = (string)($b['b64'] ?? '');
    if ($d === '' || strlen($d) > 11_000_000) out(['error' => 'Imagem grande demais (máx. 8 MB).'], 400);
    $raw = base64_decode($d, true);
    if ($raw === false || substr($raw, 0, 3) !== "\xFF\xD8\xFF") out(['error' => 'A arte precisa estar em JPG.'], 400);
    $f = bin2hex(random_bytes(16)) . '.jpg';
    file_put_contents(ps_mdir() . '/' . $f, $raw, LOCK_EX);
    out(['f' => $f, 'url' => ps_site() . '/painel/api.php?a=pm&f=' . $f]);
}
/* público: a Meta baixa a arte por aqui */
function ps_media(): never {
    $f = (string)($_GET['f'] ?? '');
    if (!preg_match('/^[a-f0-9]{32}\.jpg$/', $f) || !is_file(ps_mdir() . "/$f")) { http_response_code(404); exit; }
    header('Content-Type: image/jpeg'); header('Cache-Control: public, max-age=86400'); header('Content-Length: ' . filesize(ps_mdir() . "/$f"));
    readfile(ps_mdir() . "/$f"); exit;
}

/* ---------- agendamentos ---------- */
function ps_add(array $b): never {
    $c = ps_cfg(); $items = is_array($b['posts'] ?? null) ? $b['posts'] : [];
    if (!$items) out(['error' => 'Nenhum post para agendar.'], 400);
    $now = time(); $add = []; $errs = [];
    foreach (array_slice($items, 0, 300) as $i => $p) {
        $cid = (string)($p['cliente'] ?? ''); $cl = $c['clientes'][$cid] ?? null;
        $quando = (int)($p['quando'] ?? 0);
        $feed = preg_match('/^[a-f0-9]{32}\.jpg$/', (string)($p['feed'] ?? '')) ? $p['feed'] : '';
        $story = preg_match('/^[a-f0-9]{32}\.jpg$/', (string)($p['story'] ?? '')) ? $p['story'] : '';
        $al = (array)($p['alvos'] ?? []);
        $alvos = [];
        if ($feed && !empty($al['igf'])) $alvos['igf'] = ['st' => 'agendado', 'em' => $quando];
        $qs = (int)($p['quandoStory'] ?? 0) ?: $quando;
        if ($story && !empty($al['igs'])) $alvos['igs'] = ['st' => 'agendado', 'em' => $qs];
        if ($feed && !empty($al['fbf'])) $alvos['fbf'] = ['st' => 'agendado', 'em' => $quando];
        if ($story && !empty($al['fbs'])) $alvos['fbs'] = ['st' => 'agendado', 'em' => $qs];
        if (!$cl) { $errs[] = "Post " . ($i + 1) . ": cliente não encontrado."; continue; }
        if (!$alvos) { $errs[] = $cl['nome'] . ': nada para publicar (sem arte ou sem destino marcado).'; continue; }
        if ((isset($alvos['igf']) || isset($alvos['igs'])) && empty($cl['ig'])) { $errs[] = $cl['nome'] . ': essa Página não tem Instagram ligado.'; continue; }
        if (($feed && $quando < $now - 60) || ($story && $qs < $now - 60)) { $errs[] = $cl['nome'] . ': o horário já passou.'; continue; }
        $leg = mb_substr((string)($p['legenda'] ?? ''), 0, 2200);
        if (preg_match_all('/(^|\s)#\w/u', $leg) > 30) { $errs[] = $cl['nome'] . ': a legenda tem mais de 30 hashtags (o Instagram recusa).'; continue; }
        $add[] = ['id' => new_id(), 'cliente' => $cid, 'nome' => $cl['nome'], 'quando' => $quando, 'legenda' => $leg, 'feed' => $feed, 'story' => $story,
            'campanha' => mb_substr((string)($p['campanha'] ?? ''), 0, 80), 'alvos' => $alvos, 'criado' => $now, 'st' => 'agendado'];
    }
    if ($errs && !$add) out(['error' => implode("\n", $errs)], 400);
    jmod(ps_file(), function (&$d) use ($add) { foreach ($add as $x) $d[$x['id']] = $x; });
    ps_next();
    out(['ok' => true, 'agendados' => count($add), 'avisos' => $errs]);
}
function ps_list(): never {
    $d = jread(ps_file()); $o = array_values($d);
    usort($o, fn($a, $b) => $a['quando'] <=> $b['quando']);
    $c = ps_cfg();
    out(['itens' => $o, 'ultimoCron' => (int)($c['cronCli'] ?? 0)]);
}
function ps_del(array $b): never {
    $id = preg_replace('/[^a-f0-9]/', '', (string)($b['id'] ?? ''));
    jmod(ps_file(), function (&$d) use ($id) {
        if (!isset($d[$id])) return;
        foreach ($d[$id]['alvos'] as $a) if (($a['st'] ?? '') === 'publicado') { $d[$id]['oculto'] = true; return; }
        foreach (['feed', 'story'] as $k) if (!empty($d[$id][$k])) @unlink(ps_mdir() . '/' . $d[$id][$k]);
        unset($d[$id]);
    });
    ps_next(); out(['ok' => true]);
}
function ps_edit(array $b): never {
    $id = preg_replace('/[^a-f0-9]/', '', (string)($b['id'] ?? ''));
    jmod(ps_file(), function (&$d) use ($id, $b) {
        if (!isset($d[$id])) return;
        if (isset($b['quando'])) { $d[$id]['quando'] = (int)$b['quando']; foreach ($d[$id]['alvos'] as $k => &$a0) if (($k === 'igf' || $k === 'fbf') && ($a0['st'] ?? '') !== 'publicado') $a0['em'] = (int)$b['quando']; unset($a0); }
        if (isset($b['quandoStory'])) { foreach ($d[$id]['alvos'] as $k => &$a1) if (($k === 'igs' || $k === 'fbs') && ($a1['st'] ?? '') !== 'publicado') $a1['em'] = (int)$b['quandoStory']; unset($a1); }
        if (isset($b['legenda'])) $d[$id]['legenda'] = mb_substr((string)$b['legenda'], 0, 2200);
        foreach ($d[$id]['alvos'] as &$a) if (($a['st'] ?? '') === 'erro') { $a = ['st' => 'agendado', 'em' => max(time(), (int)($a['em'] ?? $d[$id]['quando']))]; }
        unset($a); $d[$id]['st'] = 'agendado';
    });
    ps_next(); out(['ok' => true]);
}
/* guarda quando é o próximo post, para as visitas do site dispararem a publicação sem custo */
function ps_next(): void {
    $n = PHP_INT_MAX;
    foreach (jread(ps_file()) as $x) foreach ($x['alvos'] as $a) if (in_array($a['st'] ?? '', ['agendado', 'tentar'], true)) $n = min($n, (int)($a['em'] ?? $x['quando']));
    @file_put_contents(data_dir() . '/posts-proximo.txt', $n === PHP_INT_MAX ? '0' : (string)$n, LOCK_EX);
}
function ps_due(): bool { $n = (int)@file_get_contents(data_dir() . '/posts-proximo.txt'); return $n > 0 && $n <= time(); }

/* ---------- publicação ---------- */
function ps_run(): array {
    $lock = fopen(data_dir() . '/posts.lock', 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) return ['ocupado' => true];
    @set_time_limit(300); ignore_user_abort(true);
    $c = ps_cfg(); $c['ultimoCron'] = time(); ps_save_cfg($c);
    $done = 0; $t0 = time();
    $d = jread(ps_file());
    foreach ($d as $id => $x) {
        if (time() - $t0 > 240) break;
        foreach ($x['alvos'] as $k => $a) {
            $st = $a['st'] ?? '';
            if (!in_array($st, ['agendado', 'tentar'], true) || (int)($a['em'] ?? $x['quando']) > time()) continue;
            $r = ps_publish($x, $k, $c);
            jmod(ps_file(), function (&$all) use ($id, $k, $r) { if (isset($all[$id])) { $all[$id]['alvos'][$k] = $r; $all[$id]['st'] = ps_overall($all[$id]); } });
            $done++;
        }
    }
    ps_next(); flock($lock, LOCK_UN); fclose($lock);
    return ['publicados' => $done];
}
function ps_overall(array $x): string {
    $s = array_column($x['alvos'], 'st');
    if (in_array('erro', $s, true)) return 'erro';
    if (in_array('agendado', $s, true) || in_array('tentar', $s, true)) return in_array('publicado', $s, true) ? 'parcial' : 'agendado';
    return 'publicado';
}
function ps_publish(array $x, string $k, array $c): array {
    $cl = $c['clientes'][$x['cliente']] ?? null;
    $tries = (int)(($x['alvos'][$k]['n'] ?? 0)) + 1;
    $fail = function (string $m, bool $retry = false) use ($tries) {
        if ($retry && $tries < 4) return ['st' => 'tentar', 'n' => $tries, 'em' => time() + 300 * $tries, 'msg' => $m];
        return ['st' => 'erro', 'n' => $tries, 'msg' => $m, 'quando' => time()];
    };
    if (!$cl || empty($cl['token'])) return $fail('Cliente sem conexão com a Meta. Clique em "Atualizar clientes" no painel.');
    $tok = $cl['token'];
    $img = ps_site() . '/painel/api.php?a=pm&f=' . ($k === 'igf' || $k === 'fbf' ? $x['feed'] : $x['story']);
    $retryable = fn($e) => str_contains($e, 'limite de chamadas') || str_contains($e, 'baixar a imagem') || str_contains($e, 'Erro 5') || str_contains($e, 'timed out');
    if ($k === 'igf' || $k === 'igs') {
        $p = ['image_url' => $img, 'access_token' => $tok];
        if ($k === 'igf') $p['caption'] = $x['legenda']; else $p['media_type'] = 'STORIES';
        [$j, $e] = ps_api('POST', $cl['ig'] . '/media', $p);
        if ($e) return $fail($e, $retryable($e));
        $cid = (string)$j['id'];
        for ($i = 0; $i < 10; $i++) {
            [$s] = ps_api('GET', $cid, ['fields' => 'status_code,status', 'access_token' => $tok]);
            $sc = $s['status_code'] ?? 'FINISHED';
            if ($sc === 'FINISHED') break;
            if ($sc === 'ERROR' || $sc === 'EXPIRED') return $fail('O Instagram recusou a arte: ' . ($s['status'] ?? $sc));
            sleep(3);
        }
        [$j, $e] = ps_api('POST', $cl['ig'] . '/media_publish', ['creation_id' => $cid, 'access_token' => $tok]);
        if ($e) return $fail($e, $retryable($e));
        $mid = (string)$j['id'];
        [$m] = ps_api('GET', $mid, ['fields' => 'permalink', 'access_token' => $tok]);
        return ['st' => 'publicado', 'id' => $mid, 'link' => $m['permalink'] ?? ('https://instagram.com/' . $cl['igUser']), 'quando' => time()];
    }
    if ($k === 'fbf') {
        [$j, $e] = ps_api('POST', $cl['id'] . '/photos', ['url' => $img, 'caption' => $x['legenda'], 'access_token' => $tok]);
        if ($e) return $fail($e, $retryable($e));
        $pid = (string)($j['post_id'] ?? $j['id']);
        return ['st' => 'publicado', 'id' => $pid, 'link' => 'https://www.facebook.com/' . $pid, 'quando' => time()];
    }
    if ($k === 'fbs') {
        [$j, $e] = ps_api('POST', $cl['id'] . '/photos', ['url' => $img, 'published' => 'false', 'access_token' => $tok]);
        if ($e) return $fail($e, $retryable($e));
        [$s, $e] = ps_api('POST', $cl['id'] . '/photo_stories', ['photo_id' => (string)$j['id'], 'access_token' => $tok]);
        if ($e) return $fail($e, $retryable($e));
        $sid = (string)($s['post_id'] ?? $s['id'] ?? '');
        return ['st' => 'publicado', 'id' => $sid, 'link' => 'https://www.facebook.com/' . $cl['id'], 'quando' => time()];
    }
    return $fail('Destino desconhecido');
}

/* ---------- conferir ---------- */
function ps_verify(array $b): never {
    $c = ps_cfg(); $d = jread(ps_file()); $now = time(); $res = [];
    $ids = array_filter((array)($b['ids'] ?? []));
    foreach ($d as $id => $x) {
        if ($ids && !in_array($id, $ids, true)) continue;
        if (!empty($x['oculto'])) continue;
        $cl = $c['clientes'][$x['cliente']] ?? null;
        foreach ($x['alvos'] as $k => $a) {
            $st = $a['st'] ?? '';
            if ($st === 'publicado') {
                if (!$cl) { $v = ['ok' => false, 'msg' => 'Cliente não está mais conectado']; }
                elseif ($k === 'igs' && $now - (int)($a['quando'] ?? 0) > 86400) { $v = ['ok' => true, 'msg' => 'Story publicado (já saiu do ar, durou 24 h)']; }
                elseif ($k === 'igf' || $k === 'igs') {
                    [$m, $e] = ps_api('GET', (string)$a['id'], ['fields' => 'id,permalink,timestamp', 'access_token' => $cl['token']]);
                    $v = $e ? ['ok' => false, 'msg' => 'Não encontrei no Instagram: ' . $e] : ['ok' => true, 'msg' => 'Está no ar no Instagram', 'link' => $m['permalink'] ?? ($a['link'] ?? '')];
                } elseif ($k === 'fbf') {
                    [$m, $e] = ps_api('GET', (string)$a['id'], ['fields' => 'id,permalink_url,is_published', 'access_token' => $cl['token']]);
                    $v = $e ? ['ok' => false, 'msg' => 'Não encontrei no Facebook: ' . $e] : ['ok' => !empty($m['is_published']) || !isset($m['is_published']), 'msg' => 'Está no ar no Facebook', 'link' => $m['permalink_url'] ?? ($a['link'] ?? '')];
                } else { $v = ['ok' => true, 'msg' => 'Story publicado no Facebook']; }
            } elseif ($st === 'agendado' || $st === 'tentar') {
                $p = [];
                if (!$cl) $p[] = 'cliente não está conectado';
                if (($k === 'igf' || $k === 'igs') && $cl && empty($cl['ig'])) $p[] = 'Página sem Instagram ligado';
                $f = ($k === 'igf' || $k === 'fbf') ? $x['feed'] : $x['story'];
                if (!$f || !is_file(ps_mdir() . '/' . $f)) $p[] = 'a arte sumiu do servidor';
                if ((int)($a['em'] ?? $x['quando']) < $now - 900) $p[] = 'o horário passou e ainda não publicou (confira o cron)';
                $v = $p ? ['ok' => false, 'msg' => 'Problema: ' . implode('; ', $p)] : ['ok' => true, 'msg' => 'Pronto para publicar em ' . (new DateTime('@' . (int)($a['em'] ?? $x['quando'])))->setTimezone(new DateTimeZone('America/Sao_Paulo'))->format('d/m H:i')];
            } else { $v = ['ok' => false, 'msg' => $a['msg'] ?? 'Erro']; }
            $res[$id][$k] = $v + ['em' => $now];
        }
    }
    jmod(ps_file(), function (&$all) use ($res) { foreach ($res as $id => $r) if (isset($all[$id])) foreach ($r as $k => $v) $all[$id]['alvos'][$k]['conf'] = $v; });
    /* limpa artes de posts publicados há mais de 7 dias */
    foreach ($d as $x) if (($x['st'] ?? '') === 'publicado' && $x['quando'] < $now - 7 * 86400) foreach (['feed', 'story'] as $kk) if (!empty($x[$kk])) @unlink(ps_mdir() . '/' . $x[$kk]);
    out(['ok' => true, 'resultado' => $res]);
}

/* ---------- importar do Google Drive (pasta ou arquivo compartilhado com "qualquer pessoa com o link") ---------- */
function ps_drive_get(string $url, int $max = 15_000_000): array {
    $ch = curl_init($url); $buf = '';
    curl_setopt_array($ch, [CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 5, CURLOPT_TIMEOUT => 40, CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130 Safari/537.36',
        CURLOPT_WRITEFUNCTION => function ($c, $d) use (&$buf, $max) { $buf .= $d; return strlen($buf) > $max ? 0 : strlen($d); }]);
    curl_exec($ch); $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE); $type = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE); curl_close($ch);
    return [$code, $type, $buf];
}
function ps_drive_list(string $folder, string $path, int $depth, array &$out): void {
    [$code, , $html] = ps_drive_get('https://drive.google.com/embeddedfolderview?id=' . rawurlencode($folder) . '&hl=pt-BR', 3_000_000);
    if ($code !== 200 || $html === '') return;
    preg_match_all('~<div class="flip-entry" id="entry-([\w-]+)"[\s\S]*?<a href="([^"]+)"[\s\S]*?<div class="flip-entry-title">([^<]*)</div>~', $html, $m, PREG_SET_ORDER);
    foreach ($m as $e) {
        $id = $e[1]; $name = html_entity_decode($e[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (str_contains($e[2], '/folders/')) { if ($depth < 2 && count($out) < 120) ps_drive_list($id, $path === '' ? $name : "$path/$name", $depth + 1, $out); }
        elseif (preg_match('/\.(jpe?g|png|webp|heic)$/i', $name)) $out[] = ['id' => $id, 'nome' => $name, 'pasta' => $path];
    }
}
function ps_drive(array $b): never {
    @set_time_limit(300);
    $u = trim((string)($b['url'] ?? ''));
    $folder = preg_match('~/folders/([\w-]{10,})~', $u, $m) ? $m[1] : (preg_match('~[?&]id=([\w-]{10,})~', $u, $m) && str_contains($u, 'folder') ? $m[1] : '');
    $file = !$folder && (preg_match('~/file/d/([\w-]{10,})~', $u, $m) || preg_match('~[?&]id=([\w-]{10,})~', $u, $m)) ? $m[1] : '';
    if (!$folder && !$file) out(['error' => 'Cole o link de uma pasta ou de um arquivo do Google Drive.'], 400);
    $list = [];
    if ($folder) { ps_drive_list($folder, '', 0, $list); if (!$list) out(['error' => 'Não achei imagens na pasta. Confira se ela está compartilhada como "Qualquer pessoa com o link" e se tem arquivos JPG ou PNG.'], 400); }
    else $list[] = ['id' => $file, 'nome' => (string)($b['nome'] ?? 'arte.jpg'), 'pasta' => ''];
    $list = array_slice($list, 0, 120);
    $res = []; $errs = [];
    foreach ($list as $it) {
        [$code, $type, $raw] = ps_drive_get('https://drive.usercontent.google.com/download?id=' . rawurlencode($it['id']) . '&export=download&confirm=t');
        if ($code !== 200 || strlen($raw) < 100 || str_starts_with(ltrim($raw), '<')) { $errs[] = $it['nome'] . ': não consegui baixar (confira o compartilhamento).'; continue; }
        $info = @getimagesizefromstring($raw);
        if (!$info) { $errs[] = $it['nome'] . ': não é uma imagem que eu consiga ler.'; continue; }
        if ($info[2] !== IMAGETYPE_JPEG) {
            if (!function_exists('imagecreatefromstring')) { $errs[] = $it['nome'] . ': converta para JPG.'; continue; }
            $im = @imagecreatefromstring($raw); if (!$im) { $errs[] = $it['nome'] . ': não consegui converter para JPG.'; continue; }
            $bg = imagecreatetruecolor(imagesx($im), imagesy($im)); imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255)); imagecopy($bg, $im, 0, 0, 0, 0, imagesx($im), imagesy($im));
            ob_start(); imagejpeg($bg, null, 92); $raw = (string)ob_get_clean(); imagedestroy($im); imagedestroy($bg);
        }
        $f = bin2hex(random_bytes(16)) . '.jpg';
        file_put_contents(ps_mdir() . '/' . $f, $raw, LOCK_EX);
        $res[] = ['f' => $f, 'nome' => $it['nome'], 'pasta' => $it['pasta'], 'w' => (int)$info[0], 'h' => (int)$info[1]];
    }
    out(['arquivos' => $res, 'avisos' => $errs]);
}
