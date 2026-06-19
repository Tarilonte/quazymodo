# CLI do Quazymodo

## Objetivo

O `qzy` e a CLI local do projeto para scaffolding e inspecao basica.

No estado atual, ela cobre:

- criacao de componentes;
- criacao de controllers com rota;
- listagem de rotas;
- checagens locais de estrutura.

## Execucao

Na raiz do projeto:

```bash
./qzy <comando>
```

Alternativamente:

```bash
php qzy <comando>
```

Ajuda geral:

```bash
./qzy
./qzy help
```

## Comandos

### `make:component`

Cria um componente do tipo `page` ou `plugin`.

Uso:

```bash
./qzy make:component [nome] [--type=page|plugin] [--shortcut=yes|no] [--shortcut-name=camelCase] [--no-interaction]
```

Comportamento:

- por padrao, o fluxo e interativo;
- `page` gera arquivos em `app/components/pages/<nome>/`;
- `plugin` gera arquivos em `app/components/plugins/<nome>/`;
- os arquivos minimos gerados sao `<nome>.html` e `<nome>.blueprint.php`;
- no modo interativo, a CLI pergunta se deve criar shortcut em `App\Components\ComponentShortcuts`.

Exemplos:

```bash
./qzy make:component blog/post --type=page --no-interaction
./qzy make:component ui/card --type=plugin --shortcut=yes --shortcut-name=uiCard --no-interaction
```

### `make:controller`

Cria um controller e adiciona uma rota em `web.php`, `api.php`, `dev.php` ou `test.php`.

Uso:

```bash
./qzy make:controller [nome] [--route-file=web|api|dev|test] [--http-method=GET] [--path=/rota] [--action=index] [--no-interaction]
```

Comportamento:

- por padrao, o fluxo e interativo;
- o controller e criado em `app/controllers/<Nome>Controller.php`;
- a rota e adicionada ao fim do arquivo escolhido;
- o scaffold inicial do metodo retorna JSON para manter o endpoint funcional antes da view final existir.

Exemplos:

```bash
./qzy make:controller BlogPost --route-file=web --http-method=GET --path=/blog/post --action=index --no-interaction
./qzy make:controller HealthCheck --route-file=api --http-method=GET --path=/api/health --action=show --no-interaction
```

### `route:list`

Lista as rotas conhecidas do projeto com leitura direta de `app/routes/*.php`.

Uso:

```bash
./qzy route:list
```

Saida atual:

- `METHOD`
- `PATH`
- `HANDLER`
- `MIDDLEWARE`
- `SCOPE`

Observacao:

- a coluna `MIDDLEWARE` e `best effort`; no v0.1 ela tenta refletir middleware global detectado no bootstrap atual.

### `check`

Executa validacoes locais de estrutura.

Uso:

```bash
./qzy check [--only=routes|components|config] [--strict] [--format=text|json]
```

Escopos atuais:

- `routes`
- `components`
- `config`

Comportamento:

- sucesso retorna `0`;
- falha retorna `1`;
- `--format=json` produz saida estruturada;
- `--strict` trata warnings como falha, caso existam.

Exemplos:

```bash
./qzy check
./qzy check --only=routes
./qzy check --only=config --format=json
./qzy check --strict
```

## Regras praticas

- execute o `qzy` a partir da raiz do projeto;
- use `--no-interaction` quando quiser automacao previsivel;
- para `make:component`, informe `--type` quando desativar o modo interativo;
- para `make:controller`, informe `--route-file`, `--http-method`, `--path` e, se necessario, `--action` quando desativar o modo interativo;
- a CLI nao tenta reorganizar arquivos existentes; no v0.1 ela privilegia append simples e previsivel.

## Arquivos afetados pelos comandos

- `make:component`:
  - `app/components/pages/**`
  - `app/components/plugins/**`
  - opcionalmente `app/components/ComponentShortcuts.php`
- `make:controller`:
  - `app/controllers/*.php`
  - `app/routes/web.php`
  - `app/routes/api.php`
  - `app/routes/dev.php`
  - `app/routes/test.php`
- `route:list`:
  - somente leitura de `app/routes/*.php`
- `check`:
  - somente leitura de `app/routes/`, `app/components/` e `app/config/`

## Limitacoes atuais

- nao ha aliases de comandos;
- nao ha comandos de `dev`, `assets` ou `db`;
- `route:list` nao tenta reconstruir todo o pipeline de middleware do runtime;
- `check` faz validacoes estruturais e sintaticas basicas, nao auditoria completa do projeto.

## Referencias

- implementacao: `qzy`
- aplicacao CLI: `quazymodo/CliApplication.php`
- spec: `docs/sdd/QMD-SDD-0004-cli-quazymodo-v0-1.md`
