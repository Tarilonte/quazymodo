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

- 🔳 Implementar exclusao via HTMX em `/test/redbean/lista` com feedback por toast:
  - 🔳 manter controller sem injetar HTML (form de delete deve ser template)
  - 🔳 enviar delete com HTMX para `#hack` (`hx-target`) e validar CSRF no POST
  - 🔳 responder com `jsComponent` chamando `ToastComponent.newToast(...)` sem alterar `jsComponent` core
  - 🔳 remover a linha da tabela no sucesso com jQuery `slideUp` seguido de `remove`
  - 🔳 validar fallback sem HTMX (refresh completo) mantendo o comportamento atual
