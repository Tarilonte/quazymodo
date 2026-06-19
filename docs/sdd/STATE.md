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
- escopo MVP inicial definido: `make:component`, `route:list`, `check`;
- fora de escopo atual: `dev`, `assets`, `db`;
- a especificacao final separou `make:controller` de `make:component` para nao
  misturar componente com camada HTTP.
- estrutura atual confirma bom encaixe para `route:list` em `app/routes/*.php`
  e `make:component` em `app/components/pages|plugins`.

### Programacao da CLI Quazymodo

Status: `done`

Spec ativa: `QMD-SDD-0004-cli-quazymodo-v0-1.md`.

Decisoes consolidadas:

- entrada inicial via `php qzy ...` com script raiz `qzy`;
- escopo v0.1: `make:component`, `make:controller`, `route:list`, `check`;
- `make:component` cobre apenas `page` e `plugin`;
- `make:controller` gera controller + rota, com perguntas interativas de
  arquivo de rota e verbo HTTP;
- `route:list` lista `method`, `path`, `handler`, `middleware` best effort e
  `scope`;
- `check` cobre `routes`, `components` e `config` com `exit code 0/1`.

Validacao executada em 2026-06-19:

- `php qzy` exibiu a ajuda principal da CLI v0.1;
- `php qzy make:component --help` confirmou a assinatura esperada;
- `php qzy make:controller --help` confirmou a assinatura esperada;
- `php qzy route:list` listou as rotas atuais com `method`, `path`, `handler`,
  `middleware` e `scope`;
- `php qzy check` concluiu com sucesso no estado atual do projeto.

Conclusao: a CLI v0.1 foi implementada com entrypoint `qzy`, aplicacao central
em `quazymodo/CliApplication.php` e documentacao operacional em `docs/cli.md`.

### Core hardening

Status: `accepted`

Spec ativa: `QMD-SDD-0001-core-hardening.md`.

Andamento:

- Recorte 1 concluido: compatibilidade PHP 8.4 nos construtores de
  `BaseComponent` e `ComponentDebug`.
- Recorte 2 concluido: mapeamento seguro de excecao para HTTP status, com
  fallback `500` para status invalido e preservacao de `404`/`405` do router.
- Recorte 3 concluido: excecoes capturadas em producao sao logadas
  explicitamente via Tracy antes da pagina amigavel, com fallback seguro se o
  logging falhar. O logger padrao do Tracy em arquivo foi mantido; SQLite foi
  descartado para esta entrega.

Escopo aceito:

- corrigir compatibilidade PHP 8.4 nos construtores de componentes;
- padronizar mapeamento de excecao para HTTP status;
- garantir logging explicito de excecoes capturadas em producao;
- preservar o comportamento atual das paginas existentes.

Escopo adiado para specs futuras:

- politica de escape para inserts textuais;
- excecoes de dominio para contratos de componente;
- validacao declarativa minima de inserts obrigatorios;
- helper/component ou fluxo padrao de CSRF para formularios.

Validacao: Playwright pode ser usado como acompanhamento recomendado e nao
bloqueante para renderizacao, `404` e `500`.

Validacao executada em 2026-06-18:

- `php -l` passou nos arquivos centrais do recorte;
- `GET /` respondeu `200` em development;
- `GET /` respondeu `200` em production;
- rota inexistente respondeu `404` em production;
- simulacao de `handleException()` em production confirmou pagina amigavel
  `500` para excecao comum.

Pendencia residual:

- a escrita do Tracy em `app/writable/tracy` nao foi confirmada no contexto
  local de CLI por falha de permissao; o fallback seguro da resposta amigavel
  seguiu funcionando.

Proximo passo: validar e ajustar permissoes de `app/writable/tracy` no ambiente
alvo antes de promover a spec para `done`.

### Middleware de CSRF para rotas web

Status: `accepted`

Spec ativa: `QMD-SDD-0006-csrf-middleware-web.md`.

Decisoes consolidadas:

- o recorte inicial cobre apenas o escopo `web`;
- a aplicacao sera global nas rotas `web`, nao opt-in por rota;
- os metodos protegidos serao `POST`, `PUT`, `PATCH` e `DELETE`;
- o middleware lera o token apenas do campo `csrf-token` no corpo da
  requisicao;
- token ausente ou invalido respondera com `403`;
- `api`, `dev`, `test`, helper de formulario e header alternativo ficam fora do
  escopo inicial.

Proximo passo: implementar `Middleware\CsrfMiddleware` e aplicar a validacao ao
bootstrap do escopo `web`.

### Change Runtime Endpoint

Status: `done`

Spec registrada: `QMD-SDD-0003-change-runtime-endpoint.md`.

Objetivo: endpoint local `/changeRuntime` implementado para alternar
persistentemente `APP_ENV` em `app/config/app.php` entre `development` e
`production`.

Conclusao: implementacao concluida e validacao registrada na spec.

Decisoes:

- a rota sera `GET /changeRuntime`;
- a alternancia sera automatica, sem parametros;
- somente hosts locais poderao executar a alteracao;
- hosts locais aceitos: `localhost`, `127.0.0.1`, `::1` e `quazymodo`;
- sucesso responde com `HX-Refresh: true` para o HTMX recarregar a pagina.

### Pagina Dev de Teste CSRF

Status: `done`

Spec registrada: `QMD-SDD-0005-dev-csrf-test-page.md`.

Resultado:

- rota dev `GET /csrf` criada para exibir o formulario de teste;
- rota dev `POST /csrf` criada para receber e validar o token;
- o mesmo `CsrfController` trata `GET` e `POST`;
- a pagina mostra resultado visual simples de sucesso ou falha;
- o recorte ficou explicitamente limitado a desenvolvimento e nao formaliza
  ainda o fluxo oficial de CSRF do framework.
