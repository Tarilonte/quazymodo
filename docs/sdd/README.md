# SDD — Quazymodo

## Objetivo

Este diretorio define o fluxo de SDD (Specification Driven Development) do
Quazymodo.

`docs/sdd/` e a pasta oficial de gerenciamento do projeto. Specs, roadmap,
estado e progresso operacional devem viver aqui para evitar duplicacao com o
contexto dos agentes.

O projeto e brownfield: ja existe codigo em producao de desenvolvimento,
decisoes arquiteturais registradas e componentes funcionando. Por isso, toda
spec deve preservar compatibilidade operacional ou explicitar a migracao.

## Fontes primarias

- `docs/sdd/ROADMAP.md`
- `docs/sdd/STATE.md`
- `.opencode/context/BASE.md`
- `.opencode/context/MEMORY.md`
- `AGENTS.md`
- `docs/*.md`
- codigo existente em `quazymodo/` e `app/`

## Estrutura oficial

- `README.md`: regras do fluxo SDD;
- `ROADMAP.md`: prioridades e proximos recortes;
- `STATE.md`: decisoes operacionais, andamento e pendencias globais;
- `TEMPLATE.md`: modelo para novas specs;
- `QMD-SDD-*.md`: specs formais, com criterios e validacao proprios.

`.opencode/` deve conter apenas regras e contexto para agentes. Backlog,
roadmap, estado de execucao e tarefas de projeto nao devem ser mantidos em
`.opencode/`.

## Regra central

Nenhuma implementacao relevante deve comecar por codigo.

Fluxo minimo:

1. escrever ou atualizar uma spec;
2. registrar estado atual afetado;
3. definir comportamento desejado;
4. listar restricoes brownfield;
5. definir criterios de aceite;
6. implementar o menor recorte validavel;
7. validar e registrar desvios.

## Quando criar spec

Criar spec para:

- mudancas no core do framework;
- mudancas em componentes base;
- seguranca, escape, CSRF, CSP e sessoes;
- roteamento, middleware e lifecycle HTTP;
- persistencia e repositories;
- CLI;
- qualquer convencao nova de componente;
- refatoracoes que alterem comportamento.

Nao precisa de spec para:

- copy simples em template;
- ajuste visual isolado;
- correcao obvia e local sem mudanca de contrato;
- documentacao pequena que nao muda comportamento.

## Formato de IDs

Specs usam o formato:

`QMD-SDD-0001-nome-curto.md`

Estados:

- `draft`: em desenho;
- `accepted`: aprovada para implementacao;
- `in-progress`: em implementacao;
- `done`: implementada e validada;
- `superseded`: substituida por outra spec;
- `rejected`: descartada.

## Ordem de prioridade inicial

A ordem atual de prioridades deve ser mantida em `ROADMAP.md`.

## Politica brownfield

Toda spec deve responder:

- o que ja existe?
- quem depende disso?
- qual comportamento nao pode quebrar?
- existe dado persistido afetado?
- ha rota, template ou componente publico afetado?
- qual e o plano de migracao?
- como validar que nada essencial regrediu?

## Definicao de pronto

Uma spec so pode virar `done` quando:

- criterios de aceite foram atendidos;
- validacoes manuais ou automatizadas foram registradas;
- riscos residuais foram anotados;
- docs relacionadas foram atualizadas ou a nao atualizacao foi justificada;
- se houver decisao persistivel, houve proposta de `Memory candidate`.
