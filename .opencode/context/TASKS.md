# ACTIVE TASKS

- Legenda: `🔳` em aberto, `⏳` em execucao, `✅` finalizada, `🚫` rejeitada

- 🔳 Mover politicas especificas de rate limiting para declaracao inline em `app/Routes.php`:
  - 🔳 ajustar `RateLimitMiddleware` para aceitar override inline (`requests`, `period`)
  - 🔳 remover `RATE_LIMIT_POLICIES` de `app/Config.php`
  - 🔳 declarar limites por rota diretamente em `app/Routes.php`
  - 🔳 atualizar `docs/rate-limiting.md` com o novo padrao

- 🔳 Aprimorar UX do painel de componentes no Tracy:
  - 🔳 destacar melhor os slots exibidos por componente
  - 🔳 manter visibilidade de `cache hits` e tempo agregado
  - 🔳 facilitar leitura de componentes com alto numero de instancias
