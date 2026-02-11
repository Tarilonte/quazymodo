# Manual de Rate Limiting — Implementacao Padrao do Quazymodo

## 1) Visao geral

Esta e a implementacao padrao de rate limiting do Quazymodo.

Objetivo:
- limitar abuso de requisicoes por cliente
- proteger rotas sensiveis
- manter resposta de bloqueio consistente com status `429` e header `Retry-After`

A implementacao padrao usa:
- middleware de rate limiting
- store persistente em SQLite dedicado
- fast path com APCu (quando disponivel)

## 2) Arquitetura padrao

### Componentes principais

- `app/middleware/RateLimitMiddleware.php`
  - decide se a requisicao passa ou bloqueia
  - aplica politica global da aplicacao
  - retorna `429` quando necessario

- `app/Services/RateLimitStore.php`
  - persiste contadores no banco dedicado de rate limiting
  - calcula janela, hits e `retry_after`
  - aplica blacklist progressiva por IP abusador

- `app/Services/RedBeanService.php`
  - suporta multiplos bancos por alias
  - usado pelo `RateLimitStore` para operar no alias `rate_limit`

- `quazymodo/Helper.php`
  - resolve IP do cliente via `REMOTE_ADDR`

- `app/controllers/ErrorController.php`
  - renderiza a resposta visual de erro para `429`

## 3) Configuracao padrao

Configuracoes em `app/config/rate-limit.php` (carregado por `app/config/index.php`):

- `RATE_LIMIT_REQUESTS`
  - limite padrao de requests por janela

- `RATE_LIMIT_PERIOD`
  - duracao da janela padrao, em segundos

- `RATE_LIMIT_DB_PATH`
  - caminho do SQLite dedicado ao rate limiting

Nao ha configuracao de override por rota no padrao atual.

## 4) Banco dedicado

O rate limiting usa banco separado do banco funcional da aplicacao:

- padrao: `app/writable/db/rate_limit.sqlite`

Beneficios:
- isolamento operacional
- manutencao independente
- sem poluicao de tabelas de dominio

### Tabelas usadas

- `ratelimitentry`
  - contador de hits por janela de tempo

- `ratelimitabuse`
  - blacklist progressiva por IP
  - `ip` (TEXT) e a chave primaria
  - sem campo `id`
  - sem indices extras alem da PK

## 5) Fluxo de decisao

1. Middleware usa a politica global (`RATE_LIMIT_REQUESTS` e `RATE_LIMIT_PERIOD`).
2. Monta chave de limite:
   - metodo + path + client key
3. Se APCu estiver disponivel, usa fast path em memoria.
4. Verifica se o IP esta suspenso na blacklist progressiva.
5. Se estiver suspenso:
   - retorna `429` com `Retry-After` restante da suspensao atual
6. Se nao estiver suspenso, valida/sincroniza no store persistente quando necessario.
7. Se exceder limite:
    - retorna `429`
    - inclui `Retry-After`
    - registra violacao na blacklist progressiva
    - renderiza via `ErrorController`
8. Se nao exceder:
    - request segue normalmente

## 6) Blacklist progressiva

Comportamento:
- toda violacao de rate limit gera/atualiza registro de abuso por IP
- durante suspensao ativa, novas tentativas nao aumentam strikes
- nao ha decaimento automatico de strikes

Escalonamento de suspensao por strike:
- 1: 5 minutos
- 2: 15 minutos
- 3: 1 hora
- 4: 6 horas
- 5: 24 horas
- 6: 48 horas
- 7: 72 horas
- 8 ou mais: 120 horas (teto)

Regra de extensao:
- `novo_suspended_until = max(agora, suspended_until_atual) + duracao_do_strike`
- `Retry-After` sempre reflete o bloqueio atualizado
- tentativas durante suspensao nao extendem bloqueio

## 7) Resposta 429 (padrao)

Quando excede limite:
- status: `429`
- header: `Retry-After`
- body: pagina de erro padrao (`/pages/error/`) via `ErrorController`

## 8) Regra de IP

Identificacao do cliente:
- usa `REMOTE_ADDR` por padrao
- ignora headers de IP (como `X-Forwarded-For`)
- evita spoof de IP via header na aplicacao

## 9) Fast path com APCu

Quando APCu esta ativa no runtime web:
- contador local em memoria reduz IO no store
- fallback persistente continua garantindo robustez

Observacoes:
- APCu em CLI pode estar desativada (`apc.enable_cli=Off`) e isso e normal
- o relevante para requests HTTP e APCu no PHP-FPM

## 10) Boas praticas

- aplicar limites mais restritos em rotas sensiveis (login, auth, APIs publicas)
- manter limites globais coerentes com o trafego real da aplicacao
- monitorar crescimento da blacklist (`ratelimitabuse`) em producao
- monitorar ocorrencias de `429` para calibrar limites reais de producao

## 11) Troubleshooting

### Nao bloqueia
- conferir se middleware esta registrado
- validar `RATE_LIMIT_REQUESTS` e `RATE_LIMIT_PERIOD` > 0

### Bloqueia cedo
- revisar limites globais da aplicacao

### Fica bloqueado por muito tempo
- validar historico de strikes do IP em `ratelimitabuse`
- considerar limpeza manual do registro em caso operacional excepcional

### Erro de persistencia
- validar permissao de escrita em `app/writable/db/`
- validar caminho de `RATE_LIMIT_DB_PATH`

### Benchmark inconsistente
- para medir overhead sem bloqueio, usar limite alto para evitar `429`
- comparar cenarios equivalentes (`200` vs `200`)

## 12) Checklist de validacao

- dentro do limite: `200`
- acima do limite: `429`
- `Retry-After` presente em `429`
- durante suspensao, `Retry-After` deve apenas diminuir com o tempo
- resposta visual de `429` via `ErrorController`
- banco dedicado sendo usado corretamente

## 13) Resumo

Esta e a implementacao padrao de rate limiting do Quazymodo:
- previsivel
- global e simples
- com blacklist progressiva para IP abusador
- persistente em banco dedicado
- otimizada com APCu quando disponivel

Ela funciona como baseline oficial para novos projetos Quazymodo, com ajuste por configuracao sem alterar o comportamento-base.
