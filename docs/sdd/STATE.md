# Estado SDD — Quazymodo

Este arquivo registra decisoes operacionais, andamento e pendencias globais do
SDD. Ele substitui o antigo backlog operacional mantido em `.opencode/context/`.

## Decisoes vigentes

- `docs/sdd/` e a pasta oficial para gerenciamento do projeto, incluindo specs,
  roadmap, estado e progresso operacional.
- `.opencode/` deve conter apenas contexto e regras para agentes, nao backlog de
  projeto.
- Mudancas relevantes em core, seguranca, componentes, HTTP, DB ou CLI devem ter
  spec em `docs/sdd/` antes da implementacao.
- O projeto e tratado como brownfield: specs devem registrar estado atual,
  compatibilidade esperada, plano de migracao e validacao.
- O core deve evoluir por endurecimento incremental, nao por reescrita.

## Historico migrado

### Planejamento da CLI Quazymodo

Status: `done`

Conclusao: o MVP da CLI foi planejado com foco em `make`, `route` e `check`.

Resultados:

- levantamento de CLIs de referencia foi realizado;
- familias recorrentes foram catalogadas: `new/create`, `dev/serve`, `build`,
  `make/generate`, `route/list`, `check/test`;
- comandos mais uteis para DX em projeto PHP web foram priorizados;
- escopo MVP definido: `make:component`, `route:list`, `check`;
- fora de escopo atual: `dev`, `assets`, `db`;
- assinaturas v0.1 definidas:
  `make:component [nome] [--type=page|plugin|controller] [--no-interaction]`,
  `route:list`,
  `check [--only=routes|components|security|config] [--strict] [--format=text|json]`;
- estrutura atual confirma bom encaixe para `route:list` em `app/routes/*.php`
  e `make:component` em `app/components/pages|plugins`.

### Programacao da CLI Quazymodo

Status: `pending-spec`

Proximo passo: criar uma spec propria para CLI v0.1 antes de implementar.

### Core hardening

Status: `draft`

Spec ativa: `QMD-SDD-0001-core-hardening.md`.

Proximo passo: aceitar ou ajustar a spec antes de executar o primeiro recorte.
