<?php
/* Verificador de informações do painel.
   Recebe imagens (e quadros de vídeo), um link e/ou um texto, manda para a IA com pesquisa na web
   e devolve, ao vivo, o que ela está pensando/pesquisando e o resultado final. Usa o plano GRATUITO do
   Google Gemini (gemini-2.5-flash + Pesquisa Google + leitura de links). Cada verificação fica salva
   em ../painel-data/verificacoes.json. A chave fica em ../painel-data/ia.json (nunca volta para o navegador). */
declare(strict_types=1);

/* modo => orçamento de raciocínio */
const VF_MODELS = ['rapido' => 'Rápido', 'profundo' => 'Mais profundo'];
const VF_MODEL = 'gemini-2.5-flash';

function vf_cfg(): array { return jread(data_dir() . '/ia.json'); }

function vf_status(): never {
    $c = vf_cfg();
    out(['configured' => !empty($c['key']), 'last4' => !empty($c['key']) ? substr($c['key'], -4) : '', 'model' => isset(VF_MODELS[$c['model'] ?? '']) ? $c['model'] : 'rapido', 'models' => VF_MODELS]);
}

function vf_set(array $b): never {
    $c = vf_cfg();
    $k = trim((string)($b['key'] ?? ''));
    if ($k !== '') {
        if (!preg_match('/^AIza[A-Za-z0-9_\-]{30,}$/', $k)) out(['error' => 'Essa chave não parece uma chave do Google AI Studio (começa com AIza).'], 400);
        $c['key'] = $k;
    }
    if (!empty($b['remove'])) unset($c['key']);
    if (isset($b['model']) && isset(VF_MODELS[$b['model']])) $c['model'] = $b['model'];
    $f = data_dir() . '/ia.json';
    file_put_contents($f, json_encode($c, JSON_UNESCAPED_UNICODE), LOCK_EX); @chmod($f, 0600);
    out(['ok' => true]);
}

/* só busca endereços públicos (evita que o servidor seja usado para acessar a rede interna) */
function vf_public_url(string $u): ?string {
    $p = parse_url($u);
    if (!$p || !in_array(strtolower($p['scheme'] ?? ''), ['http', 'https'], true) || empty($p['host'])) return null;
    $ips = @gethostbynamel($p['host']) ?: [];
    if (!$ips) return null;
    foreach ($ips as $ip) if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return null;
    return $u;
}
function vf_get(string $u, int $max, int $timeout = 12, int $hop = 0): array {
    if (!vf_public_url($u)) return ['', ''];
    $ch = curl_init($u);
    $buf = '';
    curl_setopt_array($ch, [CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => 6,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; facebookexternalhit/1.1; +http://www.facebook.com/externalhit_uatext.php)',
        CURLOPT_HTTPHEADER => ['Accept-Language: pt-BR,pt;q=0.9,en;q=0.8'],
        CURLOPT_WRITEFUNCTION => function ($c, $d) use (&$buf, $max) { $buf .= $d; return strlen($buf) > $max ? 0 : strlen($d); }]);
    curl_exec($ch);
    $type = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $loc = (string)curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    if ($code >= 300 && $code < 400 && $loc && $hop < 4) return vf_get($loc, $max, $timeout, $hop + 1);
    return [$code >= 200 && $code < 300 ? $buf : '', $type];
}
/* título, descrição e imagem de prévia do link (Instagram, TikTok, notícias…) */
function vf_preview(string $u): array {
    [$html] = vf_get($u, 1_500_000);
    if ($html === '') return [];
    $m = function (string $prop) use ($html) {
        if (preg_match('/<meta[^>]+(?:property|name)=["\']' . preg_quote($prop, '/') . '["\'][^>]*content=["\']([^"\']*)["\']/i', $html, $r)
         || preg_match('/<meta[^>]+content=["\']([^"\']*)["\'][^>]*(?:property|name)=["\']' . preg_quote($prop, '/') . '["\']/i', $html, $r))
            return html_entity_decode($r[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return '';
    };
    $t = $m('og:title') ?: (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $r) ? trim(html_entity_decode($r[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')) : '');
    return ['titulo' => mb_substr($t, 0, 300), 'descricao' => mb_substr($m('og:description') ?: $m('description'), 0, 2000),
            'site' => mb_substr($m('og:site_name'), 0, 100), 'data' => mb_substr($m('article:published_time'), 0, 40), 'imagem' => $m('og:image')];
}

function vf_prompt(array $b, array $prev, int $nImg, int $nFrames): string {
    $hoje = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('d/m/Y');
    $p = "Hoje é $hoje. Você é um verificador de fatos profissional, criterioso e imparcial, trabalhando para o Pedro.\n"
       . "Ele quer saber se o conteúdo abaixo é verdadeiro, se os dados estão corretos e se as fontes citadas existem e são confiáveis.\n\n";
    if ($nImg) $p .= "- Anexei $nImg imagem(ns) enviada(s) por ele.\n";
    if ($nFrames) $p .= "- Anexei $nFrames quadro(s) tirados de um vídeo, em ordem. O áudio não foi incluído; se a fala importar, diga isso.\n";
    if (!empty($b['link'])) $p .= "- Link: " . $b['link'] . "\n";
    if ($prev) {
        $p .= "- Prévia do link lida pelo servidor: " . json_encode(array_diff_key($prev, ['imagem' => 1]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
        if (!empty($prev['_img'])) $p .= "  (a imagem de prévia do link também está anexada, por último)\n";
    }
    if (!empty($b['meta'])) $p .= "- Metadados dos arquivos (EXIF, data, aparelho, programa): " . mb_substr((string)$b['meta'], 0, 3000) . "\n";
    if (!empty($b['texto'])) $p .= "- Texto/legenda ou pergunta dele:\n\"\"\"\n" . mb_substr((string)$b['texto'], 0, 6000) . "\n\"\"\"\n";
    $p .= <<<TXT

Como trabalhar:
1. Leia com cuidado tudo o que aparece (textos na imagem, números, nomes, datas, logos, marcas d'água, @ de perfis, legendas). Separe as afirmações que podem ser checadas.
2. Pesquise na web cada afirmação importante. Prefira fontes primárias (órgãos oficiais, dados públicos, artigos científicos, o próprio autor) e agências de checagem (Lupa, Aos Fatos, Estadão Verifica, Fato ou Fake, AFP Checamos, Reuters Fact Check, Snopes, Full Fact). Abra as páginas quando precisar confirmar números.
3. Confira as fontes citadas no próprio material: elas existem? dizem mesmo aquilo? são confiáveis? O dado está atualizado ou é antigo?
4. Procure sinais de manipulação: montagem, corte que muda o sentido, imagem gerada por IA, foto antiga reaproveitada, data/local trocados, print falso de notícia ou de rede social.
5. Seja honesto sobre o que não deu para confirmar. Não invente fontes nem links: use apenas endereços de páginas que você realmente encontrou na pesquisa (o endereço da própria página, não de redirecionamento).

Quando terminar a pesquisa, responda SOMENTE com o resultado neste formato, entre as tags, em português do Brasil:
<resultado>
{
  "veredito": "verdadeiro | quase_verdadeiro | enganoso | fora_de_contexto | falso | sem_provas | satira | opiniao",
  "titulo": "frase curta e direta com a conclusão",
  "resumo": "2 a 4 frases explicando a conclusão",
  "confianca": "alta | media | baixa",
  "o_que_mostra": "o que o material mostra/afirma, em 1 ou 2 frases",
  "afirmacoes": [
    {"texto": "afirmação checada", "veredito": "(mesmos valores)", "explicacao": "o que a pesquisa mostrou", "fontes": [{"titulo": "...", "url": "https://...", "veiculo": "nome do site/órgão"}]}
  ],
  "fontes_do_material": [
    {"nome": "fonte citada no material", "url": "https://... ou vazio", "existe": true, "diz_isso": "sim | nao | em_parte | nao_encontrado", "confiabilidade": "alta | media | baixa | desconhecida", "comentario": "..."}
  ],
  "sinais": [{"tipo": "montagem | ia | contexto | data | local | numero | print | outro", "descricao": "...", "gravidade": "alta | media | baixa"}],
  "origem": "de onde o conteúdo parece ter saído, quando apareceu pela primeira vez (se achou)",
  "dicas": ["o que o Pedro pode conferir por conta própria"]
}
</resultado>
Listas sem itens ficam vazias ([]). Nada fora das tags.
TXT;
    return $p;
}

/* manda um evento simples para o navegador */
function vf_emit(array $e): void { echo 'data: ' . json_encode($e, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\n"; @ob_flush(); @flush(); }

/* os links da Pesquisa Google vêm como redirecionamento: descobre o endereço real (em paralelo) */
function vf_resolve(array $urls): array {
    $mh = curl_multi_init(); $hs = []; $out = [];
    foreach (array_slice(array_unique($urls), 0, 30) as $u) {
        if (!str_contains($u, 'grounding-api-redirect')) { $out[$u] = $u; continue; }
        $h = curl_init($u);
        curl_setopt_array($h, [CURLOPT_NOBODY => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 8, CURLOPT_RETURNTRANSFER => true]);
        curl_multi_add_handle($mh, $h); $hs[$u] = $h;
    }
    do { $st = curl_multi_exec($mh, $run); if ($run) curl_multi_select($mh, 1); } while ($run && $st === CURLM_OK);
    foreach ($hs as $u => $h) { $loc = (string)curl_getinfo($h, CURLINFO_REDIRECT_URL); $out[$u] = $loc ?: $u; curl_multi_remove_handle($mh, $h); curl_close($h); }
    curl_multi_close($mh);
    return $out;
}

function vf_run(array $b): never {
    $cfg = vf_cfg();
    if (empty($cfg['key'])) out(['error' => 'Configure a chave da IA primeiro.'], 400);
    $modo = isset(VF_MODELS[$b['model'] ?? '']) ? $b['model'] : ($cfg['model'] ?? 'rapido');
    $imgs = array_slice(is_array($b['imagens'] ?? null) ? $b['imagens'] : [], 0, 10);
    $frames = array_slice(is_array($b['quadros'] ?? null) ? $b['quadros'] : [], 0, 10);
    $video = is_array($b['video'] ?? null) ? $b['video'] : null;
    $link = trim((string)($b['link'] ?? ''));
    if ($link !== '' && !preg_match('~^https?://~i', $link)) $link = 'https://' . $link;
    $b['link'] = $link;
    if (!$imgs && !$frames && !$video && $link === '' && trim((string)($b['texto'] ?? '')) === '') out(['error' => 'Envie uma imagem, um vídeo, um link ou um texto.'], 400);

    @set_time_limit(0); ignore_user_abort(true);
    while (ob_get_level()) @ob_end_clean();
    header('Content-Type: text/event-stream; charset=utf-8');
    header('Cache-Control: no-store, no-transform');
    header('X-Accel-Buffering: no');
    header('X-LiteSpeed-Cache-Control: no-cache');
    @ini_set('zlib.output_compression', '0');
    echo ':' . str_repeat(' ', 2048) . "\n\n"; @flush();

    $parts = []; $nVid = 0;
    $add = function (array $im) use (&$parts) {
        $mt = (string)($im['mt'] ?? 'image/jpeg'); $d = (string)($im['b64'] ?? '');
        if (!in_array($mt, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true) || $d === '' || strlen($d) > 7_000_000) return;
        $parts[] = ['inline_data' => ['mime_type' => $mt, 'data' => $d]];
    };
    foreach ($imgs as $im) $add($im);
    if ($video && preg_match('~^video/(mp4|mpeg|quicktime|webm|x-msvideo|x-flv|3gpp|mov)$~', (string)($video['mt'] ?? '')) && strlen((string)($video['b64'] ?? '')) < 20_000_000) {
        $parts[] = ['inline_data' => ['mime_type' => $video['mt'] === 'video/mov' ? 'video/quicktime' : $video['mt'], 'data' => (string)$video['b64']]]; $nVid = 1; $frames = [];
    }
    foreach ($frames as $im) $add($im);

    $prev = [];
    $yt = $link !== '' && preg_match('~^https?://(www\.|m\.)?(youtube\.com/(watch|shorts/)|youtu\.be/)~i', $link);
    if ($yt) $parts[] = ['file_data' => ['file_uri' => $link]];
    if ($link !== '') {
        vf_emit(['t' => 'passo', 'm' => $yt ? 'Assistindo ao vídeo do YouTube…' : 'Lendo a prévia do link…']);
        $prev = vf_preview($link);
        if (!empty($prev['imagem']) && !$yt) {
            $iu = $prev['imagem']; if (str_starts_with($iu, '//')) $iu = 'https:' . $iu;
            [$raw, $type] = vf_get($iu, 4_500_000);
            $type = strtolower(trim(explode(';', $type)[0]));
            if ($raw !== '' && in_array($type, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) { $add(['mt' => $type, 'b64' => base64_encode($raw)]); $prev['_img'] = 1; }
        }
        if ($prev) vf_emit(['t' => 'previa', 'p' => array_diff_key($prev, ['_img' => 1])]);
    }
    $prompt = vf_prompt($b, $prev, count($imgs), count($frames));
    if ($nVid) $prompt = str_replace('Hoje é ', "(Anexei o vídeo inteiro, com áudio: considere também o que é falado.)\nHoje é ", $prompt);
    if ($yt) $prompt = str_replace('Hoje é ', "(O vídeo do YouTube do link está anexado, com áudio: considere também o que é falado.)\nHoje é ", $prompt);
    $parts[] = ['text' => $prompt];

    $req = ['contents' => [['role' => 'user', 'parts' => $parts]],
            'tools' => [['google_search' => new stdClass()], ['url_context' => new stdClass()]],
            'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 20000,
                'thinkingConfig' => ['thinkingBudget' => $modo === 'profundo' ? 12000 : 3000, 'includeThoughts' => true]]];
    $model = (string)($cfg['gemini_model'] ?? VF_MODEL);
    $base = rtrim((string)($cfg['base'] ?? 'https://generativelanguage.googleapis.com'), '/');
    $final = ''; $chunks = []; $queries = []; $usage = ['in' => 0, 'out' => 0, 'buscas' => 0]; $err = ''; $sse = ''; $seenQ = []; $seenU = [];
    vf_emit(['t' => 'passo', 'm' => 'Analisando o material…']);

    $handle = function (array $d) use (&$final, &$chunks, &$queries, &$usage, &$err, &$seenQ, &$seenU) {
        if (isset($d['error'])) { $err = $d['error']['message'] ?? 'Erro da IA'; return; }
        $c = $d['candidates'][0] ?? [];
        foreach ($c['content']['parts'] ?? [] as $p) {
            if (!isset($p['text'])) continue;
            if (!empty($p['thought'])) {
                /* resumo do raciocínio: mostra só o título de cada etapa */
                if (preg_match_all('/\*\*([^*\n]{4,90})\*\*/', $p['text'], $m)) foreach ($m[1] as $t) vf_emit(['t' => 'pensa', 'm' => trim($t)]);
            } else { $final .= $p['text']; vf_emit(['t' => 'txt', 'd' => $p['text']]); }
        }
        $g = $c['groundingMetadata'] ?? [];
        foreach ($g['webSearchQueries'] ?? [] as $q) if (!isset($seenQ[$q])) { $seenQ[$q] = 1; $queries[] = $q; $usage['buscas']++; vf_emit(['t' => 'busca', 'q' => $q]); }
        foreach ($g['groundingChunks'] ?? [] as $gc) if (!empty($gc['web']['uri'])) $chunks[$gc['web']['uri']] = $gc['web']['title'] ?? '';
        foreach ($c['urlContextMetadata']['urlMetadata'] ?? [] as $um) { $u = $um['retrievedUrl'] ?? ''; if ($u && !isset($seenU[$u])) { $seenU[$u] = 1; vf_emit(['t' => 'abre', 'u' => $u]); if (($um['urlRetrievalStatus'] ?? '') === 'URL_RETRIEVAL_STATUS_SUCCESS') $chunks[$u] = $chunks[$u] ?? ''; } }
        if (isset($d['usageMetadata'])) { $usage['in'] = (int)($d['usageMetadata']['promptTokenCount'] ?? 0); $usage['out'] = (int)($d['usageMetadata']['candidatesTokenCount'] ?? 0) + (int)($d['usageMetadata']['thoughtsTokenCount'] ?? 0); }
        if (($c['finishReason'] ?? '') === 'SAFETY') $err = 'O Google bloqueou a análise desse conteúdo por segurança.';
    };
    $ch = curl_init($base . '/v1beta/models/' . rawurlencode($model) . ':streamGenerateContent?alt=sse');
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($req, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), CURLOPT_TIMEOUT => 600, CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_HTTPHEADER => ['content-type: application/json', 'x-goog-api-key: ' . $cfg['key']],
        CURLOPT_WRITEFUNCTION => function ($c, $chunk) use (&$sse, $handle) {
            $sse .= str_replace("\r\n", "\n", $chunk);
            while (($p = strpos($sse, "\n\n")) !== false) {
                $raw = substr($sse, 0, $p); $sse = substr($sse, $p + 2); $data = '';
                foreach (explode("\n", $raw) as $l) if (str_starts_with($l, 'data:')) $data .= substr($l, 5);
                $j = json_decode(trim($data), true);
                if (is_array($j)) $handle($j);
            }
            return strlen($chunk);
        }]);
    curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE); $cerr = curl_error($ch); curl_close($ch);
    if ($code !== 200) {
        $j = json_decode($sse, true); if (isset($j[0])) $j = $j[0];
        $m = $j['error']['message'] ?? ($cerr ?: "Erro $code");
        if ($code === 400 && stripos($m, 'API key') !== false) $m = 'A chave do Google está errada. Gere outra em aistudio.google.com/apikey.';
        elseif ($code === 403) $m = 'O Google recusou a chave (permissão). Gere outra em aistudio.google.com/apikey.';
        elseif ($code === 429) $m = 'Chegou no limite gratuito do Google por agora (por minuto ou por dia). Espere um pouco e tente de novo; o limite renova sozinho.';
        elseif ($code === 404) $m = 'O modelo gratuito mudou de nome no Google. Me avise para eu atualizar o verificador.';
        elseif ($code === 413 || stripos($m, 'size') !== false) $m = 'O arquivo ficou grande demais. Tente um vídeo mais curto ou prints.';
        elseif ($code >= 500) $m = 'O Google está instável agora. Tente de novo em alguns minutos.';
        $err = $m;
    }
    if ($err) { vf_emit(['t' => 'erro', 'm' => $err]); exit; }

    $res = null;
    if (preg_match('/<resultado>\s*(?:```(?:json)?)?\s*(\{.*\})\s*(?:```)?\s*<\/resultado>/s', $final, $r) || preg_match('/(\{.*"veredito".*\})/s', $final, $r)) $res = json_decode($r[1], true);
    if (!is_array($res)) { vf_emit(['t' => 'erro', 'm' => 'A IA não devolveu o resultado no formato esperado. Tente de novo.', 'bruto' => mb_substr($final, 0, 4000)]); exit; }

    /* fontes consultadas com o endereço real; marca links da resposta que não apareceram na pesquisa */
    vf_emit(['t' => 'passo', 'm' => 'Conferindo os links das fontes…']);
    $real = vf_resolve(array_keys($chunks));
    $sources = []; $hosts = [];
    foreach ($chunks as $u => $t) { $ru = $real[$u] ?? $u; $h = strtolower(preg_replace('/^www\./', '', (string)parse_url($ru, PHP_URL_HOST))); if ($h) $hosts[$h] = 1; $sources[$ru] = ['titulo' => $t, 'url' => $ru, 'idade' => '']; }
    $chk = function (&$lst) use ($hosts) {
        if (!is_array($lst)) { $lst = []; return; }
        foreach ($lst as &$f) { if (!is_array($f) || empty($f['url'])) continue; $h = strtolower(preg_replace('/^www\./', '', (string)parse_url((string)$f['url'], PHP_URL_HOST)));
            $ok = false; foreach ($hosts as $k => $_) if ($h === $k || str_ends_with($h, '.' . $k) || str_ends_with($k, '.' . $h)) { $ok = true; break; }
            $f['conferido'] = $ok; }
    };
    if (is_array($res['afirmacoes'] ?? null)) foreach ($res['afirmacoes'] as &$a) $chk($a['fontes']);
    unset($a);

    $rec = ['quando' => date('c'), 'modelo' => $modo, 'link' => $link, 'texto' => mb_substr((string)($b['texto'] ?? ''), 0, 2000),
            'nome' => mb_substr((string)($b['nome'] ?? ''), 0, 200), 'miniaturas' => array_slice(array_values(array_filter(array_map(fn($x) => is_string($x) && strlen($x) < 60000 && str_starts_with($x, 'data:image/') ? $x : null, (array)($b['miniaturas'] ?? [])))), 0, 4),
            'previa' => array_diff_key($prev, ['_img' => 1]), 'resultado' => $res, 'fontes' => array_values($sources), 'buscas' => $queries, 'uso' => $usage];
    $id = new_id();
    jmod(data_dir() . '/verificacoes.json', function (&$d) use ($id, $rec) { $d[$id] = $rec; if (count($d) > 300) $d = array_slice($d, -300, null, true); });
    vf_emit(['t' => 'fim', 'id' => $id, 'r' => $rec]);
    exit;
}

function vf_list(): never {
    $d = jread(data_dir() . '/verificacoes.json'); $o = [];
    foreach (array_reverse($d, true) as $k => $v) $o[] = ['id' => $k] + $v;
    out(['itens' => $o]);
}
function vf_del(array $b): never {
    $id = preg_replace('/[^a-f0-9]/', '', (string)($b['id'] ?? ''));
    jmod(data_dir() . '/verificacoes.json', function (&$d) use ($id) { unset($d[$id]); });
    out(['ok' => true]);
}
