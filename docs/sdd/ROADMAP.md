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

### 2. Middleware de CSRF para rotas web

Status: `accepted`
Prioridade: `alta`
Spec: `QMD-SDD-0006-csrf-middleware-web.md`

Objetivo: centralizar a validacao de CSRF em middleware para rotas `web`
mutantes, removendo a responsabilidade dos controllers nos fluxos cobertos.

Recortes previstos:

- criar `Middleware\CsrfMiddleware`;
- aplicar o middleware globalmente ao escopo `web`;
- validar `POST`, `PUT`, `PATCH` e `DELETE` com base no campo `csrf-token`;
- responder `403` em caso de token ausente ou invalido;
- preservar `api`, `dev` e `test` fora do escopo neste primeiro recorte.

### 3. Change Runtime Endpoint

Status: `done`
Prioridade: `media`
Spec: `QMD-SDD-0003-change-runtime-endpoint.md`

Objetivo: endpoint local `/changeRuntime` implementado para alternar
persistentemente `APP_ENV` em `app/config/app.php` entre `development` e
`production`.

Conclusao: recorte concluido e absorvido pela spec finalizada.

Recortes executados:

- criar rota `GET /changeRuntime`;
- validar host local antes de alterar arquivo;
- alternar `APP_ENV` diretamente em `app/config/app.php`;
- responder com `HX-Refresh: true` para recarregar a pagina apos sucesso.

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

### CLI Quazymodo v0.1

Status: `done`
Prioridade original: `alta`
Spec: `QMD-SDD-0004-cli-quazymodo-v0-1.md`

Objetivo original: implementar uma CLI pequena para produtividade imediata.

Conclusao: o v0.1 foi entregue com `qzy`, `make:component`,
`make:controller`, `route:list` e `check`, com documentacao operacional em
`docs/cli.md`.

### Estrategia de erros por dominio

Status: `absorbed`
Prioridade original: `alta`
Spec: `QMD-SDD-0001-core-hardening.md`

Objetivo original: padronizar excecoes e mensagens por contexto para diagnostico
rapido e comportamento consistente em dev/prod.

Motivo: o tema ja faz parte do hardening do core nos contratos de excecoes HTTP
e logging. Excecoes de dominio para componentes foram adiadas para spec futura.
