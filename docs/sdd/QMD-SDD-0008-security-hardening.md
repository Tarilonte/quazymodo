# QMD-SDD-0008 — Security Hardening

Status: `done`
Prioridade: `alta`
Area: `seguranca|core|componentes`

## Contexto

O Quazymodo ja concluiu um primeiro endurecimento de runtime em
`QMD-SDD-0001-core-hardening.md` e um primeiro recorte de CSRF em
`QMD-SDD-0006-csrf-middleware-web.md`.

A revisao atual do core mostrou duas superficies reais e reutilizaveis que
podem ser exploradas por sistemas construidos sobre o framework:

- resolucao de blueprint por nome de componente sem validacao forte de caminho;
- escrita de atributo HTML com valor cru no nome do componente.

Como projeto brownfield, objetivo desta spec nao e reescrever motor de
componentes, mas fechar essas superficies com menor recorte correto e
verificavel.

## Estado atual brownfield

- Arquivos afetados:
  - `quazymodo/BaseComponent.php`
  - `quazymodo/Blueprint.php`
- Rotas afetadas:
  - nenhuma rota publica nova obrigatoria nesta spec
- Componentes afetados:
  - factories `Page`, `Plugin` e `Template`
  - resolucao base de blueprint e template
- Dados persistidos afetados:
  - nenhum
- Comportamento existente que deve ser preservado:
  - controllers continuam retornando `ResponseInterface`
  - componentes continuam sendo carregados por `ComponentFactory`
  - templates continuam usando slots `{{ slot }}`
  - `Page`, `Plugin` e `Template` continuam como tipos publicos do framework
  - paginas existentes continuam renderizando sem mudanca de contrato publico

Riscos observados no estado atual:

- `Blueprint::load_blueprint()` aceita nome de componente sem normalizacao e sem
  bloqueio explicito de path traversal;
- `BaseComponent::write_componentName()` injeta `component-name` sem escape.

## Objetivo

Endurecer core do Quazymodo contra inclusao indevida de arquivos e HTML
injection em markup gerado pelo proprio framework, preservando contrato publico
atual de componentes e paginas existentes.

## Fora de escopo

- reescrever motor de componentes;
- adotar template engine externa;
- criar service container;
- criar sistema completo de sanitizacao contextual para todo insert textual;
- alterar contrato publico de `Page`, `Plugin` e `Template` sem necessidade;
- revisar ferramentas dev-only nao expostas em producao;
- mudar fluxo de CSRF nesta spec.

## Proposta

Implementar endurecimento em dois recortes pequenos:

1. validar e normalizar nomes de componente e blueprint antes de resolver
   caminhos de arquivo;
2. escapar toda escrita de atributo HTML gerada pelo core para metadados de
   componente.

## Contratos

### Resolucao de componentes

- nomes de componente aceitos pelo core nao podem conter sequencias de
  traversal como `..`;
- nomes de componente nao podem escapar da raiz esperada em
  `app/components/`;
- falha de validacao deve resultar em excecao clara e previsivel;
- componentes validos atuais devem continuar funcionando sem mudanca de
  chamada.

### Atributos HTML do core

- qualquer valor injetado pelo core em atributo HTML deve ser escapado antes de
  compor string final;
- `component-name` deve preservar valor legivel sem permitir quebra de markup.

## Criterios de aceite

- [x] nomes de componente invalidos ou com tentativa de traversal sao rejeitados
  antes de qualquer leitura de arquivo;
- [x] componentes validos existentes continuam resolvendo blueprints e templates
  sem regressao de contrato;
- [x] `component-name` e qualquer atributo equivalente gerado pelo core saem
  escapados no HTML final;
- [x] endurecimento nao altera comportamento publico de paginas existentes alem
  da protecao;
- [x] todos os arquivos alterados passam por `php -l`.

## Plano de migracao

1. mapear pontos de entrada de nome de componente no core;
2. adicionar validacao e normalizacao minima antes da resolucao de caminho;
3. adicionar escape pontual nas saidas de atributo HTML do core;
4. validar renderizacao das paginas existentes em ambiente local.

## Validacao

Comandos minimos:

```bash
php -l quazymodo/BaseComponent.php
php -l quazymodo/Blueprint.php
```

Arquivos alterados fora da lista acima tambem devem passar por `php -l`.

Smoke tests manuais:

- renderizar `GET /` e confirmar resposta `200`;
- renderizar pagina com componentes compostos e confirmar ausencia de regressao;
- tentar resolver nome de componente invalido e confirmar falha previsivel;
- inspecionar HTML final e confirmar escape de `component-name`.

Validacao assistida recomendada:

- usar Playwright para acompanhar renderizacao da home e de pagina com
  componentes;
- usar Playwright para inspecionar DOM final e confirmar escape de atributo.

Validacao executada em 2026-06-20:

- `php -l quazymodo/BaseComponent.php` passou;
- `php -l quazymodo/Blueprint.php` passou;
- `php qzy check` confirmou resolucao dos componentes atuais apos endurecimento;
- `php qzy check` ainda reporta `Rota invalida em web.php:13`, divergencia
  pre-existente e fora do escopo desta spec.
- `GET /` respondeu `200` em servidor PHP temporario local, com renderizacao da
  home preservada apos endurecimento;
- harness via `php -r` confirmou rejeicao previsivel de componente invalido com
  `../etc/passwd`;
- harness via `php -r` confirmou escape de `component-name` para payload com
  aspas em atributo HTML.

## Riscos

- Risco: validacao de nome de componente bloquear componentes brownfield com
  convencao hoje aceita implicitamente.
- Mitigacao: implementar whitelist compativel com nomes atuais do projeto e
  validar contra componentes reais existentes antes de fechar regra.

- Risco: escape em atributo mudar HTML esperado em integracoes que inspecionem
  markup cru.
- Mitigacao: limitar endurecimento aos valores dinamicos e preservar nome do
  atributo e estrutura atual.

## Decisoes

- endurecimento sera incremental e localizado, sem reescrita do motor de
  componentes;
- core passara a rejeitar nomes de componente fora do contrato esperado em vez
  de tentar resolver caminhos permissivos;
- saidas HTML geradas pelo core devem seguir escape defensivo por padrao para
  atributos dinamicos.

## Notas de implementacao

- manter alteracoes pequenas, sem introduzir novas abstracoes genericas;
- se surgir necessidade de excecao especifica para componente invalido,
  registrar contrato e impacto brownfield antes de expandir escopo;
- revisar `docs/sdd/ROADMAP.md` e `docs/sdd/STATE.md` ao promover esta spec.
