# Pedro Design — site pedrodesign.store

Portfólio do Pedro (páginas sob medida) + painel privado em `/painel`. Hospedagem: Hostinger.

## Onde editar (sempre em `src/`, nunca nos arquivos da raiz)
A raiz do repositório (`index.html`, `assets/`, `painel/`, `support.js`…) é **gerada** por `src/build.py`. Editar ali é perdido no próximo build.

- `src/site/Main.tpl.html` — estrutura, CSS e lógica da página (template; `{{t.chave}}` = texto traduzido).
- `src/site/strings.py` — todos os textos nos **6 idiomas** (pt, en, es, fr, it, de). Os blocos `T.update({...})` no fim sobrescrevem os de cima; para mudar um texto, acrescente/edite no último bloco. Toda chave nova precisa dos 6 idiomas (`x(pt, en, es, fr, it, de)`).
- `src/painel/painel.tpl.html` — painel: Visitas, Verificador, Nova proposta, Propostas, Financeiro (as abas Contatos e Projetos foram retiradas; o código ficou escondido). A proposta em PDF tem **1 página só**, no estilo do site (papel/preto/laranja, Mona Sans, logo `apresentacao/assets/logo-pd-preto-pdf.png`), sem etapas (o campo fica escondido) e sem 24h.
- `src/deploy/` — PHP do painel (`api.php`), login (`shim.html`), `.htaccess`, fontes, favicon, imagem de compartilhamento (`og.jpg`), 404, README.
- `src/media/` — imagens, áudios e logos. No template elas aparecem como `/_blob/<id>`; `src/media/map.txt` liga cada id ao arquivo (`id nome tipo`). Para usar uma imagem nova: copie para `src/media/`, invente um id de 32 caracteres hexadecimais, adicione a linha no `map.txt` e use `/_blob/<id>` no template.

## Comandos
```bash
python3 src/build.py   # monta o site e o painel na raiz
bash src/zip.sh        # gera pedrodesign-site.zip
```
Publicar: subir o zip na `public_html` da Hostinger (substituindo) e clicar em **Limpar cache**. Os dados do painel ficam em `../painel-data/` (fora da public_html) e não são apagados.

Prévia local: `php -S 127.0.0.1:8790` na raiz (o painel precisa de PHP).

## Páginas extras
`src/extra/<pasta>/` é publicado como está em `/<pasta>/` (ex.: `src/extra/meupet/` → pedrodesign.store/meupet/, com `/meupet/spa/` e `/meupet/vet/`). Continua no ar mesmo no modo em desenvolvimento. Use caminhos relativos dentro dessas páginas.

## Página inicial atual (apresentação)
Enquanto existir `src/EM_DESENVOLVIMENTO`, a página inicial (`/`) é a **página de apresentação** de `src/extra/apresentacao/index.html`: o build copia esse arquivo para a raiz trocando `assets/` por `/apresentacao/assets/`. `/apresentacao/` redireciona (301) para `/`. O painel continua; o site antigo (navy/dourado) fica guardado em `src/` sem ser publicado (`src/soon/` não é mais usado). Para voltar ao site antigo completo: apagar `src/EM_DESENVOLVIMENTO` e rodar o build.

Apresentação: fundo papel #EFEDE7, preto #0D0D0D, laranja #FF4F1A; fonte **Mona Sans** (títulos Black 900 em caixa alta, destaque em itálico Light 300 laranja); logo animada `assets/logo-animada-v5.js` (`data-logo-animada`, `data-loop`, `data-intervalo`, `data-globo`); 6 idiomas automáticos pelo idioma do aparelho (dicionário `T` no script). Arquivos de imagem/fonte ficam em cache por 1 ano: ao trocar um arquivo, salve com **nome novo**. Preço: páginas a partir de R$ 1.000.

## Visitas (analytics próprio)
Contador anônimo `apresentacao/assets/pd-visitas-v1.js` (incluído na home, /links, bio e 404; `data-pagina` no `<html>` dá o nome da página) → `painel/api.php?a=t` → `analytics.php` grava em `../painel-data/visitas/AAAA-MM-DD.jsonl` (guarda 400 dias). Aba **Visitas** do painel lê `?a=visitas`. `?naocontar=1` (ou abrir o painel) para de contar o aparelho do Pedro. Links com `?utm_source=` aparecem com esse nome em "De onde vieram".

## Verificador de informações
Não fica mais no site. É um Artifact do Claude (https://claude.ai/artifact/D17XNUcp8yVdjPSNfdxVCY), privado do Pedro, que usa o próprio Claude da conta dele (capacidade `sample` com imagens) e pesquisa na web pelo navegador do app do Claude no computador (`host:claude_browser`: navigate + get_page_text, buscas no DuckDuckGo). Histórico no `db` em `data/users/<id>`. A aba Verificador do painel é só um link para ele. O Pedro não quer pagar API nem usar Google.

## Regras do Pedro (obrigatórias)
- Textos sobre o trabalho dele: sempre **primeira pessoa do singular** ("trabalhou comigo", "eu entrego"), nunca "nós".
- Usar **"páginas"**, nunca "landing pages".
- Regra das 24h: a **primeira versão** (não a final) é entregue em 24h, contadas só quando **pagamento e briefing** estiverem ambos enviados.
- A cada atualização: revisar o site inteiro (computador e celular, os 6 idiomas) procurando erros e inconsistências e corrigir antes de entregar.
- Estilo do site antigo (guardado em `src/site/`): muito alto padrão, editorial, que transmite autoridade. Fundo azul-marinho (#06101F) com aurora animada discreta e granulado. Títulos em **Newsreader** (serifada, peso 400, destaque em itálico 300 dourado/marfim); textos em **Inter**. Acento **champanhe/dourado** (#D2B77E) nos botões e detalhes; texto marfim #EEEAE1. Nada de cores vibrantes/degradê colorido na interface (o logo usa a versão monocromática dourada `pedro-design-logo-mono.svg`). Seções numeradas "§ 01".
- Não falar de nota fiscal, empresa, CNPJ ou razão social no site nem na árvore de links.
- Logos dos clientes ficam nas cores originais.
- Árvore de links atual em `src/extra/links/index.html` (publicada em `/links/`, para a bio do Instagram), no estilo da apresentação; usa os arquivos de `/apresentacao/assets/`. (`src/links/` é a versão antiga, do site navy.)
- Contato: WhatsApp +55 45 97400-0755 · equipe@pedrodesign.com.
