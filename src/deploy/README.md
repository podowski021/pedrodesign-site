# pedrodesign.store

Site e painel do Pedro Design.

- `index.html`: portfólio (6 idiomas). `support.js` é o motor que monta a página.
- `assets/`: imagens, áudios, logos e ícones.
- `painel/`: painel privado (propostas em PDF, financeiro, contatos e projetos).
  - Os dados ficam em `../painel-data/`, **fora** do `public_html`. Não estão no GitHub e não são apagados quando o site é atualizado.
  - No primeiro acesso a `/painel`, você cria a senha.
  - O formulário do site também cria um contato novo no painel (origem: site).

## Como editar
Tudo é editado em `src/` (veja `CLAUDE.md`). Depois:

```bash
python3 src/build.py   # monta o site e o painel
bash src/zip.sh        # gera pedrodesign-site.zip
```

## Publicação
Suba o `pedrodesign-site.zip` na `public_html` da Hostinger (substituindo os arquivos) e clique em **Limpar cache**.

## Cache
Imagens, fontes e áudios ficam em cache por 1 ano. Para trocar um arquivo, use um nome novo (ex.: `foto-v2.webp`).
