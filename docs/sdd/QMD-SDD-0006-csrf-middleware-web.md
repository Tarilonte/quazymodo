# QMD-SDD-0006 — CSRF Middleware para Rotas Web

Status: `accepted`
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
  - `app/routes/index.php`
  - `app/routes/web.php`
  - `app/middleware/*.php`
- Rotas afetadas:
  - rotas do escopo `web` com metodos `POST`, `PUT`, `PATCH` e `DELETE`
- Componentes afetados:
  - nenhum componente base novo nesta spec
- Dados persistidos afetados:
  - nenhum dado persistido novo; continua usando token em `$_SESSION`
- Comportamento existente que deve ser preservado:
  - rotas `GET` do escopo `web` continuam sem bloqueio de CSRF;
  - `api`, `dev` e `test` permanecem fora do escopo deste recorte;
  - controllers continuam retornando respostas PSR-7;
  - a geracao e verificacao base de token continuam centralizadas em
    `Quazymodo\Csrf`.

Observacao brownfield:

- a spec `QMD-SDD-0005-dev-csrf-test-page.md` registra uma pagina dev `/csrf`,
  mas esse fluxo nao esta presente no codigo atual. A divergencia deve ser
  tratada depois, fora do escopo desta entrega.

## Objetivo

Criar um middleware de CSRF aplicado globalmente ao escopo `web`, responsavel
por validar o campo `csrf-token` em requisicoes `POST`, `PUT`, `PATCH` e
`DELETE` antes de o controller ser executado.

## Fora de escopo

- criar helper global de formulario nesta spec;
- aceitar token por header neste primeiro recorte;
- aplicar protecao de CSRF a `api`, `dev` ou `test`;
- desenhar fluxo dedicado para HTMX ou AJAX;
- criar resposta HTML customizada de falha;
- restaurar ou reimplementar a pagina dev `/csrf` nesta spec;
- substituir o armazenamento atual de token em sessao.

## Proposta

Implementar um `CsrfMiddleware` no projeto e aplica-lo globalmente ao conjunto
de rotas `web`.

Regras do recorte:

1. o middleware roda apenas no escopo `web`;
2. o middleware ignora `GET`, `HEAD` e demais metodos nao mutantes;
3. o middleware valida `POST`, `PUT`, `PATCH` e `DELETE`;
4. o token deve ser lido de `parsed body` no campo `csrf-token`;
5. token ausente ou invalido interrompe a requisicao com `403`;
6. token valido permite a continuacao normal da pipeline.

## Contratos

### Middleware

- nome alvo: `Middleware\CsrfMiddleware`
- ponto de aplicacao: bootstrap de rotas do escopo `web`
- entrada: `ServerRequestInterface`
- dependencia funcional: `Quazymodo\Csrf::verifyToken()`

### Regras de validacao

- `POST`, `PUT`, `PATCH` e `DELETE` no escopo `web` exigem `csrf-token`;
- o valor do token deve vir do corpo parseado da requisicao;
- ausencia do campo deve ser tratada como falha de validacao;
- token invalido deve ser tratado como falha de validacao.

### Resposta de falha

- status HTTP: `403`
- o middleware deve encerrar a pipeline antes do controller

## Criterios de aceite

- [ ] existe um middleware dedicado para validacao de CSRF no projeto;
- [ ] o middleware e aplicado globalmente ao escopo `web`;
- [ ] requisicoes `GET` do escopo `web` nao sao bloqueadas por CSRF;
- [ ] requisicoes `POST`, `PUT`, `PATCH` e `DELETE` do escopo `web` exigem o
  campo `csrf-token`;
- [ ] token valido permite a execucao normal do controller;
- [ ] token ausente retorna `403`;
- [ ] token invalido retorna `403`;
- [ ] `api`, `dev` e `test` permanecem fora do escopo deste recorte;
- [ ] controllers nao precisam validar CSRF manualmente nos fluxos cobertos por
  este middleware.

## Plano de migracao

1. criar `Middleware\CsrfMiddleware` sem alterar `Quazymodo\Csrf` alem do
   estritamente necessario;
2. aplicar o middleware apenas ao conjunto `web` no bootstrap de rotas;
3. validar que requisicoes seguras continuam funcionando sem regressao;
4. remover validacoes manuais de controller apenas quando houver cobertura clara
   pelo middleware.

## Validacao

Comandos minimos:

```bash
php -l app/middleware/CsrfMiddleware.php
php -l app/routes/index.php
php -l quazymodo/Csrf.php
```

Smoke tests manuais:

- confirmar que `GET /` continua respondendo normalmente;
- criar ou usar uma rota `web` mutante de teste com token valido e confirmar
  passagem pelo middleware;
- repetir a mesma submissao sem `csrf-token` e confirmar `403`;
- repetir com token invalido e confirmar `403`;
- confirmar que rotas `dev` e `api` nao passam a exigir CSRF por efeito lateral.

## Riscos

- Risco: aplicar CSRF globalmente no `web` bloquear futuras rotas mutantes sem
  formulario preparado.
- Mitigacao: documentar o contrato do campo `csrf-token` e manter falha
  previsivel com `403`.

- Risco: divergencia entre a spec `0005` e o codigo atual gerar interpretacao
  errada sobre cobertura de testes visuais de CSRF.
- Mitigacao: registrar explicitamente que a pagina dev fica fora do escopo desta
  spec e revisar `0005` em recorte separado.

## Decisoes

- O primeiro recorte sera limitado ao escopo `web`.
- A aplicacao sera global no conjunto `web`, nao opt-in por rota.
- Os metodos protegidos neste recorte serao `POST`, `PUT`, `PATCH` e `DELETE`.
- A leitura do token sera apenas pelo campo `csrf-token` do corpo da requisicao.
- A falha de validacao respondera com `403`.
- `api`, `dev`, `test`, HTMX por header e helper de formulario ficam para specs
  futuras.

## Notas de implementacao

- Consultar `docs/league-route.md` para ordem e ponto de aplicacao do
  middleware.
- Manter a implementacao pequena, sem container e sem configuracao generica
  nesta primeira entrega.
- Se a estrategia escolhida para `403` exigir excecao HTTP do router, preservar
  o contrato atual de respostas PSR-7 do app.
