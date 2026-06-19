# QMD-SDD-0005 — Pagina Dev de Teste CSRF

Status: `done`
Prioridade: `media`
Area: `seguranca|http|componentes|docs`

## Contexto

O Quazymodo ja possui a base de geracao e verificacao de token em
`Quazymodo\Csrf`, mas ainda nao possui um fluxo oficial e padronizado de CSRF
para formularios.

Antes de formalizar esse fluxo no core, e util ter uma pagina de desenvolvimento
que permita exercitar visualmente a geracao do token e a validacao de uma
submissao `POST` no ambiente local.

## Estado atual brownfield

- Arquivos afetados:
  - `quazymodo/Csrf.php`
  - `app/routes/dev.php`
  - `app/controllers/CsrfController.php`
  - `app/components/pages/csrf/**`
- Rotas afetadas:
  - `GET /csrf`
  - `POST /csrf`
- Componentes afetados:
  - pagina de desenvolvimento `pages/csrf`
- Dados persistidos afetados:
  - nenhum
- Comportamento existente que deve ser preservado:
  - rotas de desenvolvimento continuam disponiveis apenas em `APP_ENV === development`;
  - controllers continuam retornando `ResponseInterface`;
  - a validacao atual de CSRF continua baseada em token de sessao.

## Objetivo

Criar uma pagina de desenvolvimento com formulario simples e um endpoint no
mesmo controller para validar o token CSRF em uma submissao `POST` apenas para
teste local.

## Fora de escopo

- formalizar helper global de formulario;
- adicionar middleware global de CSRF;
- proteger automaticamente todos os formularios do projeto;
- criar fluxo de erro padronizado para producao;
- expor essa pagina fora do ambiente de desenvolvimento.

## Proposta

Adicionar um `CsrfController` dev-only com uma unica acao `index()` para:

- exibir o formulario em `GET /csrf`;
- validar o token em `POST /csrf`;
- regenerar o token para o proximo teste apos cada renderizacao;
- mostrar um resultado visual simples de sucesso ou falha na propria pagina.

## Contratos

- `GET /csrf` renderiza a pagina de teste com token oculto.
- `POST /csrf` recebe o mesmo formulario e valida `csrf-token` contra a sessao.
- sucesso e falha sao exibidos na pagina por meio de um bloco visual de status.
- a rota existe apenas em `app/routes/dev.php`.

## Criterios de aceite

- [x] existe rota dev `GET /csrf`.
- [x] existe rota dev `POST /csrf`.
- [x] o mesmo controller exibe o formulario e recebe a submissao.
- [x] o formulario inclui o token CSRF em campo oculto.
- [x] uma submissao com token valido exibe sucesso.
- [x] uma submissao com token invalido exibe falha.
- [x] a implementacao fica restrita ao ambiente de desenvolvimento.

## Plano de migracao

1. Criar a pagina dev de teste.
2. Criar o controller para GET e POST.
3. Registrar as duas rotas em `app/routes/dev.php`.
4. Validar o fluxo com requisicoes locais.

## Validacao

- `php -l app/controllers/CsrfController.php`
- `php -l app/routes/dev.php`
- `GET /csrf` responde `200` em development.
- `POST /csrf` com token valido exibe sucesso.
- `POST /csrf` com token invalido exibe falha.

## Riscos

- Risco: confundir a pagina de teste com um fluxo oficial de CSRF do framework.
- Mitigacao: limitar a rota ao arquivo `dev.php` e documentar que se trata de
  um teste local.

## Decisoes

- a pagina de teste ficara em `/csrf` no conjunto de rotas de desenvolvimento;
- o mesmo controller tratara `GET` e `POST`;
- a implementacao sera pequena e visual, apenas para exercitar a base atual de
  CSRF.

## Notas de implementacao

- Consultar `docs/league-route.md` para o padrao de rotas e controllers.
- Consultar `docs/tailwindcss.md` e `docs/daisyui.md` para a montagem da UI.
- Nao formalizar helper ou middleware de CSRF neste recorte.
