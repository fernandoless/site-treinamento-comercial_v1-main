# Site Treinamento Comercial — Bootstrap 5.3

Inclui:
- Bootstrap 5.3 (CDN) — Navbar fixa com hamburger, brand **news treinamento comercial** e **get_search_form()** (form padrão WP).
- **Carrossel 1 (Hero)**: imagem à direita, conteúdo à esquerda, com indicadores e controles.
- **Carrossel 2 (Giro pelos canais)**: cards dos últimos posts (3 por slide).
- Práticas básicas de segurança/performance (XML-RPC off, limpeza de emojis, restrição de REST /users, headers).

## Instalação
Envie a pasta para `wp-content/themes/` ou faça upload do `.zip` em Aparência → Temas.

## Notas
- O segundo carrossel agrupa posts em slides de 3. Ajuste o chunk no `front-page.php` se quiser 4 por slide etc.
- A busca usa o formulário padrão (get_search_form), sem sobrescrever `searchform.php`.
