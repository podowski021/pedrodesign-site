# pedrodesign.store

Site e painel do Pedro Design.

- `index.html`: portfólio (6 idiomas). `support.js` é o motor que monta a página.
- `assets/`: imagens, áudios, logos e ícones.
- `painel/`: painel privado (propostas em PDF, financeiro, contatos e projetos).
  - Os dados ficam em `../painel-data/`, **fora** do `public_html`. Não estão no GitHub e não são apagados quando o site é atualizado.
  - No primeiro acesso a `/painel`, você cria a senha.
  - O formulário do site também cria um contato novo no painel (origem: site).

## Publicação
A Hostinger puxa este repositório automaticamente (hPanel → Avançado → Git). Cada push na branch `main` atualiza o site.
