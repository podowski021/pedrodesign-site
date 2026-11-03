# Pedro Design — site pedrodesign.store

Portfólio do Pedro (páginas sob medida) + painel privado em `/painel`. Hospedagem: Hostinger.

## Onde editar (sempre em `src/`, nunca nos arquivos da raiz)
A raiz do repositório (`index.html`, `assets/`, `painel/`, `support.js`…) é **gerada** por `src/build.py`. Editar ali é perdido no próximo build.

- `src/site/Main.tpl.html` — estrutura, CSS e lógica da página (template; `{{t.chave}}` = texto traduzido).
- `src/site/strings.py` — todos os textos nos **6 idiomas** (pt, en, es, fr, it, de). Os blocos `T.update({...})` no fim sobrescrevem os de cima; para mudar um texto, acrescente/edite no último bloco. Toda chave nova precisa dos 6 idiomas (`x(pt, en, es, fr, it, de)`).
- `src/painel/painel.tpl.html` — painel (propostas em PDF, financeiro, contatos, projetos). `__LOGO__` vira o `src/painel/logo.png`.
- `src/deploy/` — PHP do painel (`api.php`), login (`shim.html`), `.htaccess`, fontes, favicon, imagem de compartilhamento (`og.jpg`), 404, README.
- `src/media/` — imagens, áudios e logos. No template elas aparecem como `/_blob/<id>`; `src/media/map.txt` liga cada id ao arquivo (`id nome tipo`). Para usar uma imagem nova: copie para `src/media/`, invente um id de 32 caracteres hexadecimais, adicione a linha no `map.txt` e use `/_blob/<id>` no template.

## Comandos
```bash
python3 src/build.py   # monta o site e o painel na raiz
bash src/zip.sh        # gera pedrodesign-site.zip
```
Publicar: subir o zip na `public_html` da Hostinger (substituindo) e clicar em **Limpar cache**. Os dados do painel ficam em `../painel-data/` (fora da public_html) e não são apagados.

Prévia local: `php -S 127.0.0.1:8790` na raiz (o painel precisa de PHP).

## Modo "em desenvolvimento"
Enquanto existir `src/EM_DESENVOLVIMENTO`, o build publica só `src/soon/index.html` (página "em desenvolvimento"), mantém o painel e remove o resto (o site completo continua em `src/`). Para voltar ao site completo: apagar `src/EM_DESENVOLVIMENTO` e rodar o build.

## Regras do Pedro (obrigatórias)
- Textos sobre o trabalho dele: sempre **primeira pessoa do singular** ("trabalhou comigo", "eu entrego"), nunca "nós".
- Usar **"páginas"**, nunca "landing pages".
- Regra das 24h: a **primeira versão** (não a final) é entregue em 24h, contadas só quando **pagamento e briefing** estiverem ambos enviados.
- A cada atualização: revisar o site inteiro (computador e celular, os 6 idiomas) procurando erros e inconsistências e corrigir antes de entregar.
- Estilo: muito alto padrão, editorial, que transmite autoridade. Fundo azul-marinho (#06101F) com aurora animada discreta e granulado. Títulos em **Newsreader** (serifada, peso 400, destaque em itálico 300 dourado/marfim); textos em **Inter**. Acento **champanhe/dourado** (#D2B77E) nos botões e detalhes; texto marfim #EEEAE1. Nada de cores vibrantes/degradê colorido na interface (o logo usa a versão monocromática dourada `pedro-design-logo-mono.svg`). Seções numeradas "§ 01".
- Não falar de nota fiscal, empresa, CNPJ ou razão social no site nem na árvore de links.
- Logos dos clientes ficam nas cores originais.
- Árvore de links em `src/links/index.html` (publicada em `/links/`), mesmo estilo do site.
- Contato: WhatsApp +55 45 97400-0755 · equipe@pedrodesign.com.
