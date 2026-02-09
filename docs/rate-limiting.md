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
  - resolve politica por rota
  - retorna `429` quando necessario

- `app/Services/RateLimitStore.php`
  - persiste contadores no banco dedicado de rate limiting
  - calcula janela, hits e `retry_after`

- `app/Services/RedBeanService.php`
  - suporta multiplos bancos por alias
  - usado pelo `RateLimitStore` para operar no alias `rate_limit`

- `quazymodo/Helper.php`
  - resolve IP do cliente com regra de proxy confiavel

- `app/controllers/ErrorController.php`
  - renderiza a resposta visual de erro para `429`

## 3) Configuracao padrao

Configuracoes em `app/Config.php`:

- `RATE_LIMIT_REQUESTS`
  - limite padrao de requests por janela

- `RATE_LIMIT_PERIOD`
  - duracao da janela padrao, em segundos

- `RATE_LIMIT_DB_PATH`
  - caminho do SQLite dedicado ao rate limiting

- `TRUSTED_PROXIES`
  - lista de IPs de proxy confiaveis
  - `X-Forwarded-For` so e considerado quando a origem estiver nessa lista

- `RATE_LIMIT_POLICIES`
  - override por rota
  - formato:
    - chave: `"METHOD /path"`
    - valor: `['requests' => X, 'period' => Y]`

Exemplo:
- `POST /User/processLoginForm` -> `10/60`
- `POST /User/processRegistrationForm` -> `15/60`
- `POST /api/cep/lookup` -> `30/60`

## 4) Banco dedicado

O rate limiting usa banco separado do banco funcional da aplicacao:

- padrao: `app/writable/db/rate_limit.sqlite`

Beneficios:
- isolamento operacional
- manutencao independente
- sem poluicao de tabelas de dominio

## 5) Fluxo de decisao

1. Middleware resolve politica da rota (default ou override).
2. Monta chave de limite:
   - metodo + path + client key
3. Se APCu estiver disponivel, usa fast path em memoria.
4. Valida/sincroniza no store persistente quando necessario.
5. Se exceder limite:
   - retorna `429`
   - inclui `Retry-After`
   - renderiza via `ErrorController`
6. Se nao exceder:
   - request segue normalmente

## 6) Resposta 429 (padrao)

Quando excede limite:
- status: `429`
- header: `Retry-After`
- body: pagina de erro padrao (`/pages/error/`) via `ErrorController`

## 7) Regra de IP e proxy

Identificacao do cliente:
- usa `REMOTE_ADDR` por padrao
- usa `HTTP_X_FORWARDED_FOR` apenas quando `REMOTE_ADDR` pertence a `TRUSTED_PROXIES`
- reduz risco de spoof de headers de IP

## 8) Fast path com APCu

Quando APCu esta ativa no runtime web:
- contador local em memoria reduz IO no store
- fallback persistente continua garantindo robustez

Observacoes:
- APCu em CLI pode estar desativada (`apc.enable_cli=Off`) e isso e normal
- o relevante para requests HTTP e APCu no PHP-FPM

## 9) Boas praticas

- aplicar limites mais restritos em rotas sensiveis (login, auth, APIs publicas)
- manter politicas explicitas em `RATE_LIMIT_POLICIES`
- revisar `TRUSTED_PROXIES` em ambientes com reverse proxy
- monitorar ocorrencias de `429` para calibrar limites reais de producao

## 10) Troubleshooting

### Nao bloqueia
- conferir se middleware esta registrado
- validar `RATE_LIMIT_REQUESTS` e `RATE_LIMIT_PERIOD` > 0
- validar chave da policy no formato exato (`METHOD /path`)

### Bloqueia cedo
- revisar policy da rota
- validar identificacao de cliente (proxy confiavel)

### Erro de persistencia
- validar permissao de escrita em `app/writable/db/`
- validar caminho de `RATE_LIMIT_DB_PATH`

### Benchmark inconsistente
- para medir overhead sem bloqueio, usar limite alto para evitar `429`
- comparar cenarios equivalentes (`200` vs `200`)

## 11) Checklist de validacao

- dentro do limite: `200`
- acima do limite: `429`
- `Retry-After` presente em `429`
- resposta visual de `429` via `ErrorController`
- politicas por rota funcionando
- banco dedicado sendo usado corretamente

## 12) Resumo

Esta e a implementacao padrao de rate limiting do Quazymodo:
- previsivel
- configuravel por rota
- persistente em banco dedicado
- otimizada com APCu quando disponivel

Ela funciona como baseline oficial para novos projetos Quazymodo, com ajuste por configuracao sem alterar o comportamento-base.
