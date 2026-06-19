# QMD-SDD-0003 — Change Runtime Endpoint

Status: `done`
Prioridade: `media`
Area: `config|seguranca|http`

## Contexto

O Quazymodo define o ambiente real da aplicacao pela constante `APP_ENV` em
`app/config/app.php`. O objetivo deste recorte e ter um endpoint local de
desenvolvimento capaz de alternar esse valor persistente entre `development` e
`production` diretamente no arquivo de configuracao.

Como a alteracao e persistente e exposta por HTTP, o endpoint deve ser limitado a
hosts locais. A mudanca so passa a valer na proxima requisicao, porque a
constante `APP_ENV` ja foi carregada no bootstrap da requisicao atual.

## Estado atual brownfield

- Arquivos afetados:
  - `app/config/app.php` contem `const APP_ENV = 'development';`.
  - `app/routes/web.php` recebera a rota do endpoint.
  - `app/controllers/ChangeRuntimeController.php` sera criado.
- Rotas afetadas:
  - `GET /changeRuntime`.
- Componentes afetados:
  - nenhum componente sera criado neste recorte.
- Dados persistidos afetados:
  - `app/config/app.php` sera alterado em arquivo.
- Comportamento existente que deve ser preservado:
  - `APP_ENV` continua aceitando apenas `development` e `production`.
  - o restante do bootstrap continua lendo `APP_ENV` como ambiente real.
  - controllers continuam retornando respostas PSR-7.

## Objetivo

Criar o endpoint `/changeRuntime` para alternar o valor atual de `APP_ENV` em
`app/config/app.php` entre `development` e `production`, somente quando a
requisicao vier de host local, recarregando a pagina apos a alteracao.

## Fora de escopo

- Criar UI dedicada.
- Criar modo temporario por sessao.
- Aceitar parametro de ambiente vindo da requisicao.
- Alterar outros arquivos de configuracao.
- Criar backup historico do arquivo.
- Recarregar PHP-FPM ou limpar OPcache neste recorte.

## Proposta

Adicionar `ChangeRuntimeController` com uma acao que:

- verifica se o host da requisicao e local;
- le `app/config/app.php`;
- identifica o valor atual de `APP_ENV`;
- alterna `development` para `production` ou `production` para `development`;
- escreve o arquivo com substituicao restrita a declaracao de `APP_ENV`;
- responde com o header HTMX `HX-Refresh: true` para recarregar a pagina atual.

Hosts locais aceitos:

- `localhost`
- `127.0.0.1`
- `::1`
- `quazymodo`

O host deve ser normalizado removendo porta e colchetes IPv6 antes da validacao.

## Contratos

- Metodo: `GET`.
- Path: `/changeRuntime`.
- Handler: `Controller\ChangeRuntimeController::toggle`.
- Entrada: nenhum parametro e necessario.
- Sucesso: resposta `204` com header `HX-Refresh: true`.
- Host nao local: resposta JSON `403`.
- Arquivo sem declaracao valida de `APP_ENV`: resposta JSON `500`.
- Falha de escrita: resposta JSON `500`.

## Criterios de aceite

- [x] Existe rota `GET /changeRuntime`.
- [x] A rota rejeita hosts nao locais.
- [x] A rota aceita `localhost`, `127.0.0.1`, `::1` e `quazymodo`.
- [x] A rota alterna `APP_ENV` de `development` para `production`.
- [x] A rota alterna `APP_ENV` de `production` para `development`.
- [x] A rota nao aceita nem grava valores diferentes de `development` e
  `production`.
- [x] A rota inclui `HX-Refresh: true` no response header apos sucesso.
- [x] O controller nao monta HTML.
- [x] A alteracao so precisa valer na proxima requisicao.

## Plano de migracao

1. Criar `ChangeRuntimeController`.
2. Criar rota `GET /changeRuntime` em `app/routes/web.php`.
3. Implementar normalizacao e validacao de host local.
4. Implementar leitura, substituicao restrita e escrita de `APP_ENV`.
5. Validar sintaxe dos arquivos PHP alterados.
6. Validar alternancia em CLI ou navegador local.

## Validacao

- [x] Verificar a sintaxe dos arquivos PHP alterados.
- [x] Confirmar que `app/config/app.php` alterna entre `development` e `production`.
- [x] Confirmar que host local e aceito.
- [x] Confirmar que host nao local e rejeitado.
- [x] Confirmar que a resposta de sucesso inclui `HX-Refresh: true`.

## Riscos

- Risco: endpoint persistente ser acessado fora do ambiente local.
- Mitigacao: validar host normalizado contra lista fechada de hosts locais.

- Risco: substituicao corromper `app/config/app.php`.
- Mitigacao: usar regex restrita para a declaracao `const APP_ENV = '...'` e
  rejeitar arquivo sem declaracao esperada.

- Risco: alteracao nao valer imediatamente por causa de bootstrap/opcache.
- Mitigacao: documentar que a mudanca vale na proxima requisicao e deixar
  limpeza de OPcache fora deste recorte.

## Decisoes

- O endpoint sera exatamente `/changeRuntime`.
- A alternancia sera automatica, sem parametro de entrada.
- O endpoint sera limitado a hosts locais.
- A persistencia sera feita diretamente em `app/config/app.php`.

## Notas de implementacao

- Consultar `docs/league-route.md` para rota e controller.
- Usar resposta PSR-7.
- Nao criar HTML no controller.
- Manter a implementacao pequena e explicita.
