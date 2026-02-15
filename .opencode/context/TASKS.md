# ACTIVE TASKS

- Legenda: `🔳` em aberto, `⏳` em execucao, `✅` finalizada, `🚫` rejeitada
- Prioridade: `[Alta]`, `[Media]`, `[Baixa]`
- Padrao das tasks principais:
  - titulo curto com status e prioridade
  - apresentar em arvore com linhas ASCII (`|--`, `+--`)
  - bloco `Resumo:` (objetivo da task)
  - bloco `Decisoes:` (somente decisoes vigentes, sem emoji)
  - bloco `Subtasks:` (lista operacional)
- Padrao das subtasks:
  - usar identificador curto (`S1`, `S2`, ...)
  - incluir uma linha em branco antes de cada subtask
  - ao marcar `✅` ou `🚫`, registrar `feito:` na linha seguinte

- 🔳 [Alta] Consolidar estrategia de erros por dominio:
  |-- Resumo: padronizar excecoes e mensagens por contexto para
  |   diagnostico rapido e comportamento consistente em dev/prod.
  |-- Decisoes:
  |   +-- nenhuma decisao registrada ate o momento.
  +-- Subtasks:
      |
      |-- 🔳 [S1] mapear excecoes atuais e lacunas
      |
      |-- 🔳 [S2] padronizar excecoes por contexto
      |   (controller/component/db/security)
      |
      |-- 🔳 [S3] revisar mensagens para diagnostico rapido em dev
      |
      +-- 🔳 [S4] validar comportamento em `APP_ENV=production`

- ✅ [Alta] Planejar CLI utilitario para produtividade do framework:
  |-- Resumo: definir um MVP de CLI pequeno e util para acelerar
  |   tarefas recorrentes sem aumentar escopo.
  |-- Decisoes:
  |   |-- manter o MVP focado em `make`, `route` e `check`.
  |   +-- manter fora de escopo atual `dev`, `assets` e `db`.
  |   +-- comando de geracao no v0 sera apenas `make:component`.
  |   +-- `make:component` sera interativo e perguntara o tipo.
  |   +-- sem aliases de comandos no v0.
  +-- Subtasks:
      |
      |-- ✅ [S1] levantamento de CLIs de referencia
      |   (Laravel, Rails, Django, Next, Nuxt, Symfony, Tempest)
      |   +-- feito: consultas em docs oficiais e consolidacao dos
      |       comandos-base por categoria.
      |
      |-- ✅ [S2] catalogar comandos mais comuns entre frameworks
      |   +-- feito: mapeamento de familias recorrentes:
      |       `new/create`, `dev/serve`, `build`,
      |       `make/generate`, `route/list`, `check/test`.
      |
      |-- ✅ [S3] catalogar comandos mais uteis para DX em
      |   projeto PHP web
      |   +-- feito: priorizacao preliminar orientada a
      |       produtividade local e previsibilidade de operacao.
      |
      |-- ✅ [S4] definir escopo MVP da CLI Quazymodo (v0):
      |   focar em `make`, `route` e `check`
      |   (fora de escopo atual: `dev`, `assets`, `db`)
      |   +-- feito: recorte formal do MVP e registro explicito de
      |       nao-escopo.
      |
      |-- ✅ [S5] priorizar comandos iniciais
      |   (geracao, introspeccao, runtime, qualidade)
      |   +-- feito: ordem inicial definida para v0:
      |       `make:component`, `route:list`, `check`.
      |
      |-- ✅ [S6] desenhar assinatura dos comandos com
      |   argumentos/opcoes (sem aliases no v0)
      |   +-- feito: assinaturas v0 definidas:
      |       `make:component [nome] [--type=page|plugin|controller]`
      |       `[--no-interaction]`, `route:list`,
      |       `check [--only=routes|components|security|config]`
      |       `[--strict] [--format=text|json]`.
      |
      +-- ✅ [S7] validar encaixe com estrutura atual
      |   (routes, components, assets, container)
      |   +-- feito: estrutura atual confirma bom encaixe para
      |       `route:list` (`app/routes/*.php`) e `make:component`
      |       (`app/components/pages|plugins`). `assets` e `container`
      |       permanecem fora de escopo no v0.

- ⏳ [Alta] Programar a CLI Quazymodo:
  |-- Resumo: implementar a CLI v0.1 com foco em produtividade
  |   imediata para geracao, introspeccao e checks basicos.
  |-- Decisoes:
  |   |-- manter o escopo v0.1 em `make:component`, `route:list`
  |   |   e `check`.
  |   |-- `make:component` sera interativo por padrao.
  |   +-- sem aliases de comandos no v0.1.
  +-- Subtasks:
      |
      |-- 🔳 [S1] criar estrutura base da CLI e dispatcher
      |
      |-- 🔳 [S2] implementar `make:component` com fluxo interativo
      |
      |-- 🔳 [S3] implementar `route:list` com leitura de rotas atuais
      |
      |-- 🔳 [S4] implementar `check` com escopo inicial definido
      |
      +-- 🔳 [S5] validar execucao local e ajustar mensagens de erro

- 🔳 [Baixa] Definir roadmap de releases do Quazymodo:
  |-- Resumo: organizar entregas v0.x com milestones curtos e
  |   criterio de prontidao por release.
  |-- Decisoes:
  |   +-- nenhuma decisao registrada ate o momento.
  +-- Subtasks:
      |
      |-- 🔳 [S1] estabelecer milestones curtos (v0.x)
      |
      |-- 🔳 [S2] organizar backlog por impacto tecnico
      |
      +-- 🔳 [S3] publicar criterio de prontidao por release
