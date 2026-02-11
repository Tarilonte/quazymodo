# ACTIVE TASKS

- Legenda: `🔳` em aberto, `⏳` em execucao, `✅` finalizada, `🚫` rejeitada
- Prioridade: `[Alta]`, `[Media]`, `[Baixa]`

- 🔳 [Alta] Criar base de testes automatizados do core:
  - 🔳 adicionar configuracao inicial de PHPUnit
  - 🔳 criar estrutura `tests/` com bootstrap
  - 🔳 cobrir fluxos criticos de `BaseComponent` e `Blueprint`
  - 🔳 cobrir validacoes de `Csrf` e `CSPManager`

- 🔳 [Alta] Padronizar validacao de entrada em controllers:
  - 🔳 definir abordagem simples de validacao (sem overengineering)
  - 🔳 criar helper/utilitario reutilizavel para regras comuns
  - 🔳 aplicar em rotas criticas (`login`, `register`, `redbean`)
  - 🔳 documentar padrao para novos endpoints

- 🔳 [Alta] Consolidar estrategia de erros por dominio:
  - 🔳 mapear excecoes atuais e lacunas
  - 🔳 padronizar excecoes por contexto (controller/component/db/security)
  - 🔳 revisar mensagens para diagnostico rapido em dev
  - 🔳 validar comportamento em `APP_ENV=production`

- 🔳 [Media] Criar documentacao oficial de contratos internos do Quazymodo:
  - 🔳 guia de componentes (blueprint/template/inserts/slots)
  - 🔳 guia de controllers (HTML em template, CSRF, respostas)
  - 🔳 guia de DB (`listAsArray` vs `findAll`/`exportAll`)
  - 🔳 guia de assets (versionamento e CSP nonce)

- 🔳 [Media] Melhorar observabilidade na Tracy:
  - 🔳 evoluir painel RedBean com destaque de operacoes lentas
  - 🔳 criar painel resumo por request (componentes + db + tempo total)
  - 🔳 adicionar thresholds visuais configuraveis para dev

- 🔳 [Media] Reduzir custo de render em listas grandes:
  - 🔳 implementar paginacao em `/test/redbean/lista`
  - 🔳 manter UX HTMX + toast no fluxo paginado
  - 🔳 reavaliar metricas no painel de componentes apos mudanca

- 🔳 [Baixa] Criar CLI utilitario para produtividade do framework:
  - 🔳 comando para gerar page/plugin/controller
  - 🔳 comando para listar rotas
  - 🔳 comando para build de assets e checks basicos

- 🔳 [Baixa] Definir roadmap de releases do Quazymodo:
  - 🔳 estabelecer milestones curtos (v0.x)
  - 🔳 organizar backlog por impacto tecnico
  - 🔳 publicar criterio de prontidao por release
