"""Monta o site e o painel a partir de src/ e grava o resultado na raiz do repositório.
Uso (na raiz do repositório):  python3 src/build.py
Depois:                        bash src/zip.sh   (gera pedrodesign-site.zip para subir na Hostinger)"""
import os, re, sys, shutil, json, base64
import builtins
def open(f, mode='r', *a, **k):  # sempre UTF-8 (Windows/Mac/Linux)
    if 'b' not in mode: k.setdefault('encoding', 'utf-8')
    return builtins.open(f, mode, *a, **k)

HERE = os.path.dirname(os.path.abspath(__file__))           # src/
OUT = os.path.dirname(HERE)                                 # raiz do repositório
SRC = os.path.join(HERE, 'deploy')
DOMAIN = 'https://pedrodesign.store'
KEEP = {'.git', 'src', 'CLAUDE.md', 'pedrodesign-site.zip'}

# ---------- 1. página com os 6 idiomas ----------
L = ['pt', 'en', 'es', 'fr', 'it', 'de']
_g = {'L': L}
exec(open(os.path.join(HERE, 'site', 'strings.py'), encoding='utf-8').read(), _g)
T, A = _g['T'], _g['A']
D = {'t': {l: {k: T[k][l] for k in T} for l in L}, 'tabs': A['tabs'], 'listTitles': A['listTitles'], 'lists': A['lists'],
     'clients': {l: [list(q) for q in A['clients'][l]] for l in L}, 'vocab': A['vocab']}
SITE_DC = open(os.path.join(HERE, 'site', 'Main.tpl.html'), encoding='utf-8').read().replace('__I18N__', json.dumps(D, ensure_ascii=False))
os.makedirs(os.path.join(HERE, '.build'), exist_ok=True)
open(os.path.join(HERE, '.build', 'Main.dc.html'), 'w', encoding='utf-8').write(SITE_DC)   # prévia local (com src/support.js)

# ---------- 2. painel ----------
_logo = base64.b64encode(open(os.path.join(HERE, 'painel', 'logo.png'), 'rb').read()).decode()
PAINEL = open(os.path.join(HERE, 'painel', 'painel.tpl.html'), encoding='utf-8').read().replace('__LOGO__', _logo)

# limpa (mantém .git)
for n in os.listdir(OUT):
    if n in KEEP: continue
    p = os.path.join(OUT, n)
    shutil.rmtree(p) if os.path.isdir(p) else os.remove(p)
os.makedirs(os.path.join(OUT, 'assets'))
os.makedirs(os.path.join(OUT, 'painel'))

# ---------- mapa de arquivos ----------
M = {}
for l in open(os.path.join(HERE, 'media', 'map.txt')):
    if not l.strip(): continue
    i, p, t = l.split(); M[i] = os.path.join(HERE, 'media', p)
RENAME = {'fb-1.mp4': 'feedback-1.mp4', 'fb-2.mp4': 'feedback-2.mp4'}

site = SITE_DC
used = {}
def rep(m):
    i = m.group(1)
    name = os.path.basename(M[i]); name = RENAME.get(name, name)
    if name in used and used[name] != i:
        name = i[:6] + '-' + name
    used[name] = i
    return '/assets/' + name
site = re.sub(r'/_blob/([0-9a-f]{32})', rep, site)
for name, i in used.items():
    shutil.copyfile(M[i], os.path.join(OUT, 'assets', name))
site = site.replace('/_blob/...', '/assets/arquivo.webp')
assert '/_blob/' not in site

UR = 'U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2190-2199, U+2212, U+2215, U+2713, U+2715, U+2726, U+FEFF, U+FFFD'
FONTS = ("@font-face{font-family:'Montserrat';font-style:normal;font-weight:300 900;font-display:swap;src:url(/assets/fonts/montserrat.woff2) format('woff2');unicode-range:%s}" % UR) + ("@font-face{font-family:'Montserrat';font-style:italic;font-weight:300 900;font-display:swap;src:url(/assets/fonts/montserrat-italic.woff2) format('woff2');unicode-range:%s}" % UR)
GF = re.compile(r'<link rel="preconnect" href="https://fonts\.googleapis\.com">\s*<link href="https://fonts\.googleapis\.com/css2[^"]*" rel="stylesheet">')
assert GF.search(site)
site = GF.sub('', site)
desc = 'Páginas sob medida, de alto padrão, com a primeira versão em 24h. Atendo o mundo inteiro, em qualquer idioma e moeda, com nota fiscal.'
head = f'''<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Pedro Design · Páginas sob medida de alto padrão</title>
<meta name="description" content="{desc}">
<link rel="canonical" href="{DOMAIN}/">
<link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/assets/apple-touch-icon.png">
<meta name="theme-color" content="#07080D">
<meta property="og:type" content="website">
<meta property="og:url" content="{DOMAIN}/">
<meta property="og:title" content="Pedro Design · Páginas sob medida de alto padrão">
<meta property="og:description" content="{desc}">
<meta property="og:image" content="{DOMAIN}/assets/og.jpg">
<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<script type="application/ld+json">{json.dumps({"@context": "https://schema.org", "@type": "ProfessionalService", "name": "Pedro Design", "legalName": "Pedro Nascimento da Costa Design Gráfico Ltda.", "taxID": "63.543.207/0001-22", "url": DOMAIN, "telephone": "+55 45 97400-0755", "areaServed": "Worldwide", "address": {"@type": "PostalAddress", "addressLocality": "Rio de Janeiro", "addressRegion": "RJ", "addressCountry": "BR"}}, ensure_ascii=False)}</script>
<link rel="preload" href="/assets/fonts/montserrat.woff2" as="font" type="font/woff2" crossorigin>
<style>{FONTS}</style>
<script defer src="/support.js"></script>'''
site = re.sub(r'<meta charset="utf-8">\s*<title>.*?</title>\s*<script src="\./support\.js"></script>', lambda m: head, site, count=1, flags=re.S)
assert 'og:image' in site
assert 'fonts.googleapis' not in site
open(os.path.join(OUT, 'index.html'), 'w').write(site)
os.makedirs(os.path.join(OUT, 'assets/fonts'))
for n in ['montserrat.woff2', 'montserrat-italic.woff2']:
    shutil.copyfile(os.path.join(SRC, 'fonts', n), os.path.join(OUT, 'assets/fonts', n))
shutil.copyfile(os.path.join(HERE, 'support.js'), os.path.join(OUT, 'support.js'))

# ---------- painel ----------
pn = PAINEL
shim = open(os.path.join(SRC, 'shim.html')).read()
for a, b in [("toast('Abra no Claude para salvar')", "toast('Faça login para salvar')"),
             ("'Salvo na nuvem'", "'Salvo no servidor'"),
             ("'Baixar PDF só funciona com o painel aberto no Claude'", "'Não consegui preparar o download'"),
             ('<span class="sync" id="sync"><i></i><span id="syncTxt">Conectando…</span></span>',
              '<span class="sync" id="sync"><i></i><span id="syncTxt">Conectando…</span></span><button type="button" id="bOut" class="mini" onclick="__painelLogout()">Sair</button>')]:
    n = pn.count(a)
    assert n >= 1, a
    pn = pn.replace(a, b)
i = pn.index('</style>') + len('</style>')
headp, bodyp = pn[:i], pn[i:]
headp = headp.replace('<meta name="claude:viewport-fit-sides" content="cover">\n', '')
headp = re.sub(r'<link rel="preconnect" href="https://fonts\.googleapis\.com">\s*<link href="https://fonts\.googleapis\.com/css2[^"]*" rel="stylesheet">', lambda m: '<style>' + FONTS + '</style>', headp)
assert 'fonts.googleapis' not in headp
headp = headp.replace("--f-display:'Bricolage Grotesque'", "--f-display:'Montserrat'").replace("--f-body:'DM Sans'", "--f-body:'Montserrat'").replace("--f-serif:'Instrument Serif',Georgia,serif", "--f-serif:'Montserrat',system-ui,sans-serif")
j = bodyp.index('<script src="https://cdnjs')
bodyp = bodyp[:j] + shim + '\n' + bodyp[j:]
doc = ('<!doctype html>\n<html lang="pt-BR">\n<head>\n<meta charset="utf-8">\n'
       '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">\n'
       '<meta name="robots" content="noindex, nofollow">\n<link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">\n'
       + headp + '\n</head>\n<body>' + bodyp + '\n</body>\n</html>\n')
open(os.path.join(OUT, 'painel/index.html'), 'w').write(doc)
shutil.copyfile(os.path.join(SRC, 'api.php'), os.path.join(OUT, 'painel/api.php'))

# ---------- arquivos estáticos ----------
for n in ['favicon.svg', 'apple-touch-icon.png', 'og.jpg', 'px.gif']:
    shutil.copyfile(os.path.join(SRC, n), os.path.join(OUT, 'assets', n))
for n, dst in [('root.htaccess', '.htaccess'), ('painel.htaccess', 'painel/.htaccess'), ('robots.txt', 'robots.txt'),
               ('README.md', 'README.md'), ('gitignore', '.gitignore'), ('404.html', '404.html')]:
    shutil.copyfile(os.path.join(SRC, n), os.path.join(OUT, dst))
os.makedirs(os.path.join(OUT, 'links'), exist_ok=True)
shutil.copyfile(os.path.join(HERE, 'links', 'index.html'), os.path.join(OUT, 'links', 'index.html'))
open(os.path.join(OUT, 'sitemap.xml'), 'w').write(f'<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>{DOMAIN}/</loc></url><url><loc>{DOMAIN}/links/</loc></url></urlset>\n')
tot = sum(os.path.getsize(os.path.join(r, f)) for r, _, fs in os.walk(OUT) if '.git' not in r for f in fs)
print('ok', len(used), 'assets', round(tot / 1e6, 2), 'MB')
