# Roadmap SDD — Quazymodo

Este arquivo concentra a visao operacional do projeto. Specs detalhadas vivem
em `docs/sdd/QMD-SDD-*.md`; este roadmap apenas ordena prioridades e aponta o
proximo recorte esperado.

## Prioridades atuais

### 1. Core hardening

Status: `accepted`
Prioridade: `alta`
Spec: `QMD-SDD-0001-core-hardening.md`

Objetivo: endurecer os pontos de maior risco do core sem reescrever o
framework.

Recortes previstos:

- corrigir compatibilidade PHP 8.4 nos construtores de componentes;
- padronizar mapeamento de excecao para HTTP status;
- garantir logging explicito de excecoes capturadas em producao;
- preservar o comportamento atual das paginas existentes.

Recortes adiados para specs futuras:

- criar politica de escape para inserts textuais;
- criar excecoes de dominio para contrato de componente;
- criar validacao declarativa minima de inserts obrigatorios;
- formalizar helper/component de CSRF para formularios.

### 2. Environment Selector

Status: `implemented-pending-smoke`
Prioridade: `media`
Spec: `QMD-SDD-0002-environment-selector.md`

Objetivo: criar um plugin no navbar para alternancia temporaria por sessao entre
`development` e `production`, usando toggle do daisyUI, HTMX, CSRF e reload apos
sucesso.

Recortes previstos:

- override temporario antes da definicao do ambiente efetivo implementado;
- plugin `environmentSelector` com toggle do daisyUI implementado;
- componente incluido no navbar por slot pre-preenchido;
- rota HTMX com validacao CSRF implementada;
- smoke visual no navegador ainda recomendado.

### 3. CLI Quazymodo v0.1

Status: `pending-spec`
Prioridade: `alta`
Spec: `a criar`

Objetivo: implementar uma CLI pequena para produtividade imediata.

Decisoes ja tomadas:

- manter o escopo v0.1 em `make:component`, `route:list` e `check`;
- `make:component` sera interativo por padrao;
- `make:component` perguntara o tipo do componente;
- o comando de geracao no v0.1 sera apenas `make:component`;
- manter fora de escopo atual `dev`, `assets` e `db`;
- nao criar aliases de comandos no v0.1.

Recortes previstos:

- criar estrutura base da CLI e dispatcher;
- implementar `make:component` com fluxo interativo;
- implementar `route:list` com leitura de rotas atuais;
- implementar `check` com escopo inicial definido;
- validar execucao local e ajustar mensagens de erro.

### 4. Roadmap de releases v0.x

Status: `pending-spec`
Prioridade: `baixa`
Spec: `a criar se a decisao exigir detalhamento`

Objetivo: organizar milestones curtos e criterio de prontidao por release.

Recortes previstos:

- estabelecer milestones curtos para v0.x;
- organizar backlog por impacto tecnico;
- publicar criterio de prontidao por release.

## Itens absorvidos por specs existentes

### Estrategia de erros por dominio

Status: `absorbed`
Prioridade original: `alta`
Spec: `QMD-SDD-0001-core-hardening.md`

Objetivo original: padronizar excecoes e mensagens por contexto para diagnostico
rapido e comportamento consistente em dev/prod.

Motivo: o tema ja faz parte do hardening do core nos contratos de excecoes HTTP
e logging. Excecoes de dominio para componentes foram adiadas para spec futura.
