<?php
/* Verificador de informações do painel.
   Recebe imagens (e quadros de vídeo), um link e/ou um texto, manda para a IA com pesquisa na web
   e devolve, ao vivo, o que ela está pesquisando e o resultado final. Cada verificação fica salva
   em ../painel-data/verificacoes.json. A chave da IA fica em ../painel-data/ia.json (nunca volta para o navegador). */
declare(strict_types=1);

const VF_MODELS = ['claude-sonnet-5-5' => 'Rápido', 'claude-opus-5-5' => 'Mais profundo'];

function vf_cfg(): array { return jread(data_dir() . '/ia.json'); }

function vf_status(): never {
    $c = vf_cfg();
    out(['configured' => !empty($c['key']), 'last4' => !empty($c['key']) ? substr($c['key'], -4) : '', 'model' => $c['model'] ?? 'claude-sonnet-5-5', 'models' => VF_MODELS]);
}

function vf_set(array $b): never {
    $c = vf_cfg();
    $k = trim((string)($b['key'] ?? ''));
    if ($k !== '') {
        if (!preg_match('/^sk-ant-[A-Za-z0-9_\-]{20,}$/', $k)) out(['error' => 'Essa chave não parece uma chave da Anthropic (começa com sk-ant-).'], 400);
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
5. Seja honesto sobre o que não deu para confirmar. Não invente fontes nem links: use apenas URLs que você realmente encontrou na pesquisa.

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

function vf_run(array $b): never {
    $cfg = vf_cfg();
    if (empty($cfg['key'])) out(['error' => 'Configure a chave da IA primeiro.'], 400);
    $model = isset(VF_MODELS[$b['model'] ?? '']) ? $b['model'] : ($cfg['model'] ?? 'claude-sonnet-5-5');
    $imgs = array_slice(is_array($b['imagens'] ?? null) ? $b['imagens'] : [], 0, 10);
    $frames = array_slice(is_array($b['quadros'] ?? null) ? $b['quadros'] : [], 0, 10);
    $link = trim((string)($b['link'] ?? ''));
    if ($link !== '' && !preg_match('~^https?://~i', $link)) $link = 'https://' . $link;
    $b['link'] = $link;
    if (!$imgs && !$frames && $link === '' && trim((string)($b['texto'] ?? '')) === '') out(['error' => 'Envie uma imagem, um vídeo, um link ou um texto.'], 400);

    @set_time_limit(0); ignore_user_abort(true);
    while (ob_get_level()) @ob_end_clean();
    header('Content-Type: text/event-stream; charset=utf-8');
    header('Cache-Control: no-store, no-transform');
    header('X-Accel-Buffering: no');
    header('X-LiteSpeed-Cache-Control: no-cache');
    @ini_set('zlib.output_compression', '0');
    echo ':' . str_repeat(' ', 2048) . "\n\n"; @flush();

    $content = [];
    $add = function (array $im) use (&$content) {
        $mt = (string)($im['mt'] ?? 'image/jpeg'); $d = (string)($im['b64'] ?? '');
        if (!in_array($mt, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true) || $d === '' || strlen($d) > 7_000_000) return;
        $content[] = ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => $mt, 'data' => $d]];
    };
    foreach ($imgs as $im) $add($im);
    foreach ($frames as $im) $add($im);

    $prev = [];
    if ($link !== '') {
        vf_emit(['t' => 'passo', 'm' => 'Lendo a prévia do link…']);
        $prev = vf_preview($link);
        if (!empty($prev['imagem'])) {
            $iu = $prev['imagem']; if (str_starts_with($iu, '//')) $iu = 'https:' . $iu;
            [$raw, $type] = vf_get($iu, 4_500_000);
            $type = strtolower(trim(explode(';', $type)[0]));
            if ($raw !== '' && in_array($type, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) { $add(['mt' => $type, 'b64' => base64_encode($raw)]); $prev['_img'] = 1; }
        }
        if ($prev) vf_emit(['t' => 'previa', 'p' => array_diff_key($prev, ['_img' => 1])]);
    }
    $content[] = ['type' => 'text', 'text' => vf_prompt($b, $prev, count($imgs), count($frames))];

    $messages = [['role' => 'user', 'content' => $content]];
    $tools = [
        ['type' => 'web_search_20250305', 'name' => 'web_search', 'max_uses' => $model === 'claude-opus-5-5' ? 14 : 9],
        ['type' => 'web_fetch_20250910', 'name' => 'web_fetch', 'max_uses' => 6, 'max_content_tokens' => 12000],
    ];
    $base = rtrim((string)($cfg['base'] ?? 'https://api.anthropic.com'), '/');
    $final = ''; $sources = []; $usage = ['in' => 0, 'out' => 0, 'buscas' => 0]; $err = '';

    for ($turn = 0; $turn < 4; $turn++) {
        $blocks = []; $stop = ''; $sse = '';
        $payload = json_encode(['model' => $model, 'max_tokens' => 8000, 'stream' => true, 'tools' => $tools, 'messages' => $messages], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $ch = curl_init($base . '/v1/messages');
        $handle = function (string $ev, array $d) use (&$blocks, &$stop, &$usage, &$sources, &$err) {
            switch ($d['type'] ?? $ev) {
                case 'message_start': $usage['in'] += (int)($d['message']['usage']['input_tokens'] ?? 0); break;
                case 'content_block_start':
                    $cb = $d['content_block']; $i = (int)$d['index'];
                    if (($cb['type'] ?? '') === 'server_tool_use') $cb['_json'] = '';
                    if (($cb['type'] ?? '') === 'web_search_tool_result' && is_array($cb['content'] ?? null)) {
                        $n = 0;
                        foreach ($cb['content'] as $r) if (($r['type'] ?? '') === 'web_search_result') { $sources[$r['url']] = ['titulo' => $r['title'] ?? '', 'url' => $r['url'], 'idade' => $r['page_age'] ?? '']; $n++; }
                        vf_emit(['t' => 'achou', 'n' => $n]);
                    }
                    if (($cb['type'] ?? '') === 'web_fetch_tool_result') {
                        $u = $cb['content']['url'] ?? ''; if ($u) $sources[$u] = $sources[$u] ?? ['titulo' => $cb['content']['content']['title'] ?? '', 'url' => $u, 'idade' => ''];
                    }
                    $blocks[$i] = $cb; break;
                case 'content_block_delta':
                    $i = (int)$d['index']; $dl = $d['delta'];
                    if (($dl['type'] ?? '') === 'text_delta') { $blocks[$i]['text'] = ($blocks[$i]['text'] ?? '') . $dl['text']; vf_emit(['t' => 'txt', 'd' => $dl['text']]); }
                    elseif (($dl['type'] ?? '') === 'input_json_delta') $blocks[$i]['_json'] = ($blocks[$i]['_json'] ?? '') . $dl['partial_json'];
                    elseif (($dl['type'] ?? '') === 'citations_delta') $blocks[$i]['citations'][] = $dl['citation'];
                    break;
                case 'content_block_stop':
                    $i = (int)$d['index'];
                    if (($blocks[$i]['type'] ?? '') === 'server_tool_use') {
                        $in = json_decode($blocks[$i]['_json'] ?: '{}', true) ?: new stdClass(); $blocks[$i]['input'] = $in; unset($blocks[$i]['_json']);
                        if ($blocks[$i]['name'] === 'web_search') { $usage['buscas']++; vf_emit(['t' => 'busca', 'q' => $in['query'] ?? '']); }
                        else vf_emit(['t' => 'abre', 'u' => $in['url'] ?? '']);
                    }
                    break;
                case 'message_delta': $stop = $d['delta']['stop_reason'] ?? $stop; $usage['out'] += (int)($d['usage']['output_tokens'] ?? 0); break;
                case 'error': $err = $d['error']['message'] ?? 'Erro da IA'; break;
            }
        };
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_TIMEOUT => 600, CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_HTTPHEADER => ['content-type: application/json', 'x-api-key: ' . $cfg['key'], 'anthropic-version: 2023-06-01', 'anthropic-beta: web-fetch-2025-09-10'],
            CURLOPT_WRITEFUNCTION => function ($c, $chunk) use (&$sse, $handle) {
                $sse .= $chunk;
                while (($p = strpos($sse, "\n\n")) !== false) {
                    $raw = substr($sse, 0, $p); $sse = substr($sse, $p + 2);
                    $ev = ''; $data = '';
                    foreach (explode("\n", $raw) as $l) { if (str_starts_with($l, 'event:')) $ev = trim(substr($l, 6)); elseif (str_starts_with($l, 'data:')) $data .= substr($l, 5); }
                    $j = json_decode(trim($data), true);
                    if (is_array($j)) $handle($ev, $j);
                }
                return strlen($chunk);
            }]);
        curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE); $cerr = curl_error($ch); curl_close($ch);
        if ($code !== 200) {
            $j = json_decode($sse, true);
            $m = $j['error']['message'] ?? ($cerr ?: "Erro $code");
            if ($code === 401) $m = 'A chave da IA está errada ou foi desativada.';
            if ($code === 400 && stripos($m, 'credit') !== false) $m = 'Acabaram os créditos da conta da IA. Coloque crédito em console.anthropic.com.';
            if ($code === 429) $m = 'Muitas verificações seguidas. Espere um minuto e tente de novo.';
            if ($code === 529 || $code === 503) $m = 'A IA está sobrecarregada agora. Tente de novo em alguns minutos.';
            $err = $m; break;
        }
        if ($err) break;
        ksort($blocks);
        foreach ($blocks as $bl) if (($bl['type'] ?? '') === 'text') $final .= $bl['text'];
        if ($stop !== 'pause_turn') break;
        /* a pesquisa foi longa: continua de onde parou */
        $clean = array_map(function ($bl) { unset($bl['_json']); if (($bl['type'] ?? '') === 'text' && empty($bl['citations'])) unset($bl['citations']); return $bl; }, array_values($blocks));
        $messages[] = ['role' => 'assistant', 'content' => $clean];
        vf_emit(['t' => 'passo', 'm' => 'Continuando a pesquisa…']);
    }

    if ($err) { vf_emit(['t' => 'erro', 'm' => $err]); exit; }
    $res = null;
    if (preg_match('/<resultado>\s*(\{.*\})\s*<\/resultado>/s', $final, $r) || preg_match('/(\{.*"veredito".*\})/s', $final, $r)) $res = json_decode($r[1], true);
    if (!is_array($res)) { vf_emit(['t' => 'erro', 'm' => 'A IA não devolveu o resultado no formato esperado. Tente de novo.', 'bruto' => mb_substr($final, 0, 4000)]); exit; }

    $rec = ['quando' => date('c'), 'modelo' => $model, 'link' => $link, 'texto' => mb_substr((string)($b['texto'] ?? ''), 0, 2000),
            'nome' => mb_substr((string)($b['nome'] ?? ''), 0, 200), 'miniaturas' => array_slice(array_filter(array_map(fn($x) => is_string($x) && strlen($x) < 60000 && str_starts_with($x, 'data:image/') ? $x : null, (array)($b['miniaturas'] ?? []))), 0, 4),
            'previa' => array_diff_key($prev, ['_img' => 1]), 'resultado' => $res, 'fontes' => array_values($sources), 'uso' => $usage];
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
