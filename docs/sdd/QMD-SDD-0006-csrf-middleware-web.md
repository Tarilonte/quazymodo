# QMD-SDD-0006 — CSRF Middleware para Rotas Web

Status: `done`
Prioridade: `alta`
Area: `seguranca|http|core`

## Contexto

O Quazymodo ja possui uma base simples de CSRF em `Quazymodo\Csrf`, com token
armazenado em sessao e verificacao por comparacao segura. Hoje, a validacao do
token tende a cair na responsabilidade do controller, o que aumenta o risco de
duplicacao, esquecimento e contratos inconsistentes entre formularios.

Como projeto brownfield, o primeiro recorte deve centralizar a verificacao em um
middleware pequeno e previsivel, sem redesenhar o fluxo de formularios e sem
introduzir abstracoes adicionais antes da hora.

## Estado atual brownfield

- Arquivos afetados:
  - `quazymodo/Csrf.php`
  - `app/routes/web.php`
  - `app/middleware/*.php`
- Rotas afetadas:
  - rotas `web` que declararem `Middleware\CsrfMiddleware`
- Componentes afetados:
  - nenhum componente base novo nesta spec
- Dados persistidos afetados:
  - nenhum dado persistido novo; continua usando token em `$_SESSION`
- Comportamento existente que deve ser preservado:
  - rotas sem `CsrfMiddleware` continuam sem bloqueio de CSRF;
  - `api`, `dev` e `test` permanecem fora do escopo deste recorte;
  - controllers continuam retornando respostas PSR-7;
  - a geracao e verificacao base de token continuam centralizadas em
    `Quazymodo\Csrf`.

Observacao brownfield:

- a spec `QMD-SDD-0005-dev-csrf-test-page.md` registra uma pagina dev `/csrf`,
  mas esse fluxo nao esta presente no codigo atual. A divergencia deve ser
  tratada depois, fora do escopo desta entrega.

## Objetivo

Criar um middleware de CSRF aplicado de forma explicita por rota, responsavel
por validar o campo `csrf-token` ou o header `X-CSRF-Token` sempre que estiver
anexado a uma rota, antes de o controller ser executado.

## Fora de escopo

- criar helper global de formulario nesta spec;
- aplicar protecao de CSRF a `api`, `dev` ou `test`;
- desenhar fluxo dedicado para HTMX ou AJAX;
- criar resposta HTML customizada de falha;
- restaurar ou reimplementar a pagina dev `/csrf` nesta spec;
- substituir o armazenamento atual de token em sessao.

## Proposta

Implementar um `CsrfMiddleware` no projeto e aplica-lo apenas nas rotas que
devem exigir CSRF.

Regras do recorte:

1. o middleware e anexado explicitamente por rota;
2. toda rota que usar `CsrfMiddleware` exige token valido, independentemente do
   metodo HTTP;
3. o token deve ser lido de `parsed body` no campo `csrf-token` e pode usar o
   header `X-CSRF-Token` como fallback;
4. token ausente ou invalido interrompe a requisicao com `403`;
5. token valido permite a continuacao normal da pipeline.

## Contratos

### Middleware

- nome alvo: `Middleware\CsrfMiddleware`
- ponto de aplicacao: declaracao individual da rota que precisa de CSRF
- entrada: `ServerRequestInterface`
- dependencia funcional: `Quazymodo\Csrf::verifyToken()`

### Regras de validacao

- toda rota com `CsrfMiddleware` exige `csrf-token` ou `X-CSRF-Token` valido;
- o valor do token deve vir do corpo parseado da requisicao ou do header
  `X-CSRF-Token`;
- ausencia do campo deve ser tratada como falha de validacao;
- token invalido deve ser tratado como falha de validacao.

### Resposta de falha

- status HTTP: `403`
- o middleware deve encerrar a pipeline antes do controller

## Criterios de aceite

- [x] existe um middleware dedicado para validacao de CSRF no projeto;
- [x] o middleware pode ser aplicado individualmente por rota;
- [x] rotas sem `CsrfMiddleware` nao sao bloqueadas por CSRF por efeito lateral;
- [x] rotas com `CsrfMiddleware` exigem o campo `csrf-token` ou o header
  `X-CSRF-Token`, independentemente do metodo HTTP;
- [x] token valido permite a execucao normal do controller;
- [x] token ausente retorna `403`;
- [x] token invalido retorna `403`;
- [x] `api`, `dev` e `test` permanecem fora do escopo deste recorte;
- [x] controllers nao precisam validar CSRF manualmente nos fluxos cobertos por
  este middleware.

## Plano de migracao

1. criar `Middleware\CsrfMiddleware` sem alterar `Quazymodo\Csrf` alem do
   estritamente necessario;
2. aplicar o middleware apenas nas rotas que exigem CSRF;
3. validar que requisicoes seguras continuam funcionando sem regressao;
4. remover validacoes manuais de controller apenas quando houver cobertura clara
   pelo middleware.

## Validacao

Comandos minimos:

```bash
php -l app/middleware/CsrfMiddleware.php
php -l app/routes/web.php
php -l quazymodo/Csrf.php
```

Smoke tests manuais:

- confirmar que rota sem `CsrfMiddleware` continua respondendo normalmente;
- criar ou usar uma rota com `CsrfMiddleware` e token valido para confirmar
  passagem pelo middleware;
- repetir a mesma requisicao sem `csrf-token` e confirmar `403`;
- repetir com token invalido e confirmar `403`;
- confirmar que rotas `dev` e `api` nao passam a exigir CSRF por efeito lateral.

Validacao executada em 2026-06-20:

- `php -l app/middleware/CsrfMiddleware.php` passou;
- `php -l app/routes/web.php` passou;
- `php -l quazymodo/Csrf.php` passou;
- `php qzy check` passou sem falhas apos remocao da aplicacao global em
  `web.php`;
- harness PSR-7 executado a partir de `public/` confirmou rota sem
  `CsrfMiddleware` livre de CSRF;
- harness PSR-7 confirmou rota com `csrf-token` valido retornando fluxo normal;
- harness PSR-7 confirmou rota com `X-CSRF-Token` valido retornando fluxo
  normal;
- harness PSR-7 confirmou rota com middleware sem token retornando `403`;
- harness PSR-7 confirmou rota com middleware e token invalido retornando `403`.
- `GET /` respondeu `200` em servidor PHP temporario local sem exigir token.

## Riscos

- Risco: esquecer de anexar `CsrfMiddleware` em rota mutante navegada por
  browser.
- Mitigacao: manter declaracao explicita por rota e revisar endpoints mutantes
  como parte do fluxo normal de desenvolvimento.

- Risco: divergencia entre a spec `0005` e o codigo atual gerar interpretacao
  errada sobre cobertura de testes visuais de CSRF.
- Mitigacao: registrar explicitamente que a pagina dev fica fora do escopo desta
  spec e revisar `0005` em recorte separado.

## Decisoes

- O primeiro recorte sera limitado ao escopo `web`.
- A aplicacao sera opt-in por rota, nao global no conjunto `web`.
- Toda rota com `CsrfMiddleware` exigira token valido, independentemente do
  metodo HTTP.
- A leitura do token priorizara o campo `csrf-token` do corpo da requisicao,
  com fallback para o header `X-CSRF-Token`.
- A falha de validacao respondera com `403`.
- `api`, `dev`, `test` e helper de formulario ficam para specs futuras.

## Notas de implementacao

- Consultar `docs/league-route.md` para ordem e ponto de aplicacao do
  middleware.
- Manter a implementacao pequena, sem container e sem configuracao generica
  nesta primeira entrega.
- Se a estrategia escolhida para `403` exigir excecao HTTP do router, preservar
  o contrato atual de respostas PSR-7 do app.
