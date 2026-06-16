# QMD-SDD-0001 — Core Hardening

Status: `draft`
Prioridade: `alta`
Area: `core|seguranca|componentes|http`

## Contexto

O Quazymodo ja possui um core funcional de componentes, rotas PSR-7 via League
Route, paginas base, plugins, rate limiting, Tracy em desenvolvimento e uma
estrategia inicial de excecoes de dominio.

Como projeto brownfield, a prioridade nao e reescrever o framework. A prioridade
e endurecer os pontos de maior risco sem quebrar a simplicidade atual:

- diagnostico de erros;
- escape seguro de saida;
- contratos de componentes;
- compatibilidade PHP 8.4;
- validacao de formularios/CSRF;
- observabilidade minima em producao.

## Estado atual brownfield

Arquivos centrais:

- `quazymodo/App.php`
- `quazymodo/BaseComponent.php`
- `quazymodo/ComponentData.php`
- `quazymodo/Blueprint.php`
- `quazymodo/Exceptions/*`
- `quazymodo/AbstractController.php`
- `quazymodo/Csrf.php`
- `app/controllers/ErrorController.php`
- `app/config/tracy.php`

Comportamento existente que deve ser preservado:

- controllers retornam `ResponseInterface`;
- HTML e montado por componentes;
- templates usam slots `{{ slot }}`;
- blueprints definem `template`, `inserts`, `css` e `js`;
- `Page`, `Plugin` e `Template` continuam sendo os tipos de factory;
- componentes podem compor outros componentes;
- assets de componentes sobem para o render final;
- Tracy continua sendo ferramenta de diagnostico em desenvolvimento.

Riscos ja observados:

- excecoes capturadas em producao podem perder contexto diagnostico;
- `getCode()` de excecao pode virar status HTTP invalido;
- inserts textuais parecem entrar no HTML sem politica formal de escape;
- parsing de slots pre-preenchidos usa `InvalidArgumentException` generica;
- PHP 8.4 emite deprecations por parametro opcional antes de obrigatorio em
  construtores do componente;
- CSRF existe como helper, mas ainda nao ha contrato padrao de formulario.

## Objetivo

Definir e implementar uma base segura e diagnosticavel para o core, sem alterar
o modelo arquitetural do Quazymodo.

## Fora de escopo

- trocar League Route;
- adotar Laravel, Symfony full, Blade ou Twig;
- criar service container;
- reescrever o motor de componentes;
- implementar ORM novo;
- criar sistema completo de testes nesta spec;
- criar HTMX parcial nesta spec.

## Proposta

Implementar em recortes pequenos, nesta ordem:

1. corrigir compatibilidade PHP 8.4 nos construtores de componentes;
2. padronizar mapeamento de excecao para HTTP status;
3. garantir logging explicito de excecoes capturadas em producao;
4. criar politica de escape para inserts textuais;
5. criar excecoes de dominio para contrato de componente;
6. criar validacao declarativa minima de inserts obrigatorios;
7. formalizar helper/component de CSRF para formularios.

## Contratos

### Excecoes HTTP

O core deve aceitar somente status HTTP valido na resposta final.

Regras:

- excecao com `getStatusCode()` valido usa esse status;
- excecao sem status HTTP valido vira `500`;
- `404` e `405` vindos do router devem ser preservados;
- codigo numerico arbitrario de excecao comum nao deve virar status HTTP.

### Logging

Em producao, toda excecao capturada pelo app deve ser registrada antes da pagina
amigavel ser renderizada.

### Escape

O framework deve distinguir:

- texto comum escapado;
- HTML/component seguro;
- atributo HTML;
- URL.

O comportamento legado pode ser preservado por migracao, mas qualquer novo
contrato deve favorecer escape seguro por padrao.

### Componentes

Componentes devem poder declarar, no minimo:

- inserts obrigatorios;
- inserts opcionais;
- tipo esperado simples (`string`, `array`, `component`, `list`);
- mensagem de erro com nome do componente e chave invalida.

## Criterios de aceite

- [ ] PHP 8.4 nao emite deprecation nos construtores de `BaseComponent` e
  `ComponentDebug`.
- [ ] excecoes sem status HTTP valido resultam em `500`.
- [ ] excecoes capturadas em producao sao logadas.
- [ ] existe politica documentada de escape para inserts.
- [ ] existe mecanismo inicial para validar inserts obrigatorios em componente.
- [ ] erros de contrato de componente citam componente, chave e expectativa.
- [ ] CSRF possui fluxo padrao documentado para formularios.
- [ ] comportamento atual de paginas existentes continua renderizando.

## Plano de migracao

1. Corrigir assinaturas sem alterar chamadas existentes.
2. Adicionar helper interno de status HTTP valido.
3. Adicionar logging no catch de producao.
4. Documentar escape antes de alterar comportamento global.
5. Implementar contratos de componente de forma opt-in.
6. Migrar componentes novos para contratos opt-in.
7. Avaliar migracao gradual de componentes antigos.

## Validacao

Comandos minimos:

```bash
php -l quazymodo/App.php
php -l quazymodo/BaseComponent.php
php -l quazymodo/ComponentDebug.php
php -l quazymodo/ComponentData.php
php -l quazymodo/Blueprint.php
```

Smoke tests manuais:

- renderizar `/` em development;
- renderizar rota inexistente e confirmar `404`;
- forcar excecao em development e confirmar Tracy;
- forcar excecao em production e confirmar pagina `500` + log;
- renderizar componente com insert obrigatorio ausente e confirmar mensagem.

## Riscos

- Risco: escape seguro quebrar componentes que hoje passam HTML cru.
- Mitigacao: introduzir escape como contrato opt-in primeiro e documentar
  mecanismo explicito para HTML confiavel.

- Risco: validacao de contrato gerar falso positivo em componentes montadores.
- Mitigacao: iniciar com declaracao opt-in por componente, sem inferencia global.

- Risco: logging em producao falhar por permissao de escrita.
- Mitigacao: validar `app/writable/` e fallback seguro sem quebrar resposta.

## Decisoes

- O core deve evoluir por endurecimento incremental, nao por reescrita.
- Contratos novos devem ser opt-in ate haver migracao dos componentes antigos.
- Templates continuam passivos; loops e regras de composicao permanecem em PHP.

## Notas de implementacao

- Priorizar alteracoes pequenas e verificaveis.
- Nao modificar `app/components/plugins/**` salvo componente explicitamente no
  escopo da subtask.
- Consultar `docs/tracy.md` para logging/diagnostico.
- Consultar `docs/league-route.md` para comportamento de rotas/HTTP.
- Consultar `docs/componentes.md` antes de alterar contratos de componente.
