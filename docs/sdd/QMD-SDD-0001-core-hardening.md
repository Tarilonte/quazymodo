# QMD-SDD-0001 — Core Hardening

Status: `accepted`
Prioridade: `alta`
Area: `core|http|observabilidade`

## Contexto

O Quazymodo ja possui um core funcional de componentes, rotas PSR-7 via League
Route, paginas base, plugins, rate limiting, Tracy em desenvolvimento e uma
estrategia inicial de excecoes de dominio.

Como projeto brownfield, a prioridade nao e reescrever o framework. A primeira
entrega desta spec deve endurecer os pontos de runtime de maior risco sem
quebrar a simplicidade atual:

- diagnostico de erros;
- compatibilidade PHP 8.4;
- respostas HTTP previsiveis;
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
- PHP 8.4 emite deprecations por parametro opcional antes de obrigatorio em
  construtores do componente;
- temas de escape, contratos de componente e CSRF existem como riscos futuros,
  mas ficam fora desta primeira entrega.

## Objetivo

Definir e implementar uma base mais previsivel para runtime do core, focando em
compatibilidade PHP 8.4, status HTTP seguros e logging de excecoes em producao,
sem alterar o modelo arquitetural do Quazymodo.

## Fora de escopo

- trocar League Route;
- adotar Laravel, Symfony full, Blade ou Twig;
- criar service container;
- reescrever o motor de componentes;
- implementar ORM novo;
- criar sistema completo de testes nesta spec;
- criar HTMX parcial nesta spec;
- criar politica de escape para inserts textuais;
- criar excecoes de dominio para contratos de componente;
- criar validacao declarativa de inserts obrigatorios;
- formalizar helper/component ou fluxo padrao de CSRF para formularios.

## Proposta

Implementar em recortes pequenos, nesta ordem:

1. corrigir compatibilidade PHP 8.4 nos construtores de componentes;
2. padronizar mapeamento de excecao para HTTP status;
3. garantir logging explicito de excecoes capturadas em producao;
4. preservar o comportamento atual das paginas existentes.

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

### Compatibilidade

As correcoes devem preservar o contrato publico atual de controllers,
componentes, rotas e paginas existentes.

## Criterios de aceite

- [ ] PHP 8.4 nao emite deprecation nos construtores de `BaseComponent` e
  `ComponentDebug`.
- [ ] Excecoes sem status HTTP valido resultam em resposta `500`.
- [ ] Excecoes HTTP conhecidas, especialmente `404` e `405`, preservam o status
  correto.
- [ ] Excecoes capturadas em producao sao logadas antes da pagina amigavel ser
  renderizada.
- [ ] Falha de logging nao quebra a resposta final ao usuario.
- [ ] Paginas existentes continuam renderizando sem mudanca de contrato publico.
- [ ] A spec registra explicitamente que escape, contratos de componente e CSRF
  foram adiados para specs futuras.

## Plano de migracao

1. Corrigir assinaturas sem alterar chamadas existentes.
2. Adicionar helper interno de status HTTP valido.
3. Adicionar logging no catch de producao.
4. Garantir fallback seguro quando o logging falhar.
5. Validar que as paginas existentes continuam renderizando.

## Validacao

Comandos minimos:

```bash
php -l quazymodo/App.php
php -l quazymodo/BaseComponent.php
php -l quazymodo/ComponentDebug.php
php -l quazymodo/Exceptions/*.php
```

Arquivos alterados que nao estejam na lista acima tambem devem passar por
`php -l`.

Smoke tests manuais:

- renderizar `/` em development;
- renderizar rota inexistente e confirmar `404`;
- forcar excecao comum e confirmar resposta final `500`, sem usar codigo
  arbitrario da excecao como HTTP status;
- forcar excecao em production e confirmar pagina amigavel `500` + log;
- confirmar que falha de logging nao impede a resposta amigavel.

Validacao assistida recomendada:

- usar Playwright para acompanhar renderizacao da pagina inicial;
- usar Playwright para acompanhar rota inexistente e pagina `404`;
- usar Playwright para acompanhar erro comum como `500`;
- usar Playwright para confirmar que a pagina amigavel de production nao expoe
  stack trace.

A validacao com Playwright e recomendada para acompanhamento visual e funcional,
mas nao bloqueia a conclusao da spec enquanto nao houver suite automatizada
formal.

## Riscos

- Risco: logging em producao falhar por permissao de escrita.
- Mitigacao: validar `app/writable/` e fallback seguro sem quebrar resposta.

- Risco: mapeamento de status HTTP alterar comportamento de excecoes existentes.
- Mitigacao: preservar `404` e `405` do router e transformar apenas status
  invalido em `500`.

## Decisoes

- O core deve evoluir por endurecimento incremental, nao por reescrita.
- Esta primeira entrega da spec foca em runtime: PHP 8.4, status HTTP e logging.
- Escape, contratos de componente e CSRF ficam adiados para specs futuras.
- Templates continuam passivos; loops e regras de composicao permanecem em PHP.

## Notas de implementacao

- Priorizar alteracoes pequenas e verificaveis.
- Nao modificar `app/components/plugins/**` salvo componente explicitamente no
  escopo da subtask.
- Consultar `docs/tracy.md` para logging/diagnostico.
- Consultar `docs/league-route.md` para comportamento de rotas/HTTP.
- Consultar `docs/componentes.md` antes de alterar assinaturas ou comportamento
  publico de componentes.
