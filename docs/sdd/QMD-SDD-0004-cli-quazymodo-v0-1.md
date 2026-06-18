# QMD-SDD-0004 — CLI Quazymodo v0.1

Status: `accepted`
Prioridade: `alta`
Area: `cli|docs|componentes|http`

## Contexto

O Quazymodo ainda nao possui uma CLI propria. Hoje, criacao de componentes,
controllers, leitura de rotas e checagens basicas de estrutura dependem de
edicao manual e conhecimento previo da organizacao do projeto.

Como brownfield, a primeira entrega da CLI deve ser pequena, explicita e
centrada em produtividade imediata, sem introduzir container, framework externo
ou automacoes que escondam o comportamento do projeto.

## Estado atual brownfield

- Arquivos afetados:
  - `composer.json`
  - `app/routes/*.php`
  - `app/controllers/*.php`
  - `app/components/pages/**`
  - `app/components/plugins/**`
  - `app/components/ComponentShortcuts.php`
- Rotas afetadas: nenhuma rota de runtime existente deve mudar por causa da
  CLI.
- Componentes afetados: novos componentes gerados em `pages` e `plugins`.
- Dados persistidos afetados: nenhum.
- Comportamento existente que deve ser preservado:
  - bootstrap HTTP atual via `public/index.php` e `Quazymodo\App`;
  - organizacao de rotas em `app/routes/index.php`, `web.php`, `api.php`,
    `dev.php` e `test.php`;
  - componentes com `html` e `blueprint`, usando `ComponentFactory::Page`,
    `Plugin` e `Template`;
  - `ComponentShortcuts` como ponto preferencial para atalhos reutilizaveis;
  - named arguments em codigo novo.

## Objetivo

Definir e implementar uma CLI minima, executada como `php qzy ...`, para
acelerar tarefas recorrentes do projeto sem alterar o modelo arquitetural do
Quazymodo.

O v0.1 deve cobrir apenas:

- geracao guiada de componentes (`make:component`);
- geracao guiada de controllers com rota (`make:controller`);
- listagem de rotas (`route:list`);
- checagens locais de estrutura (`check`).

## Fora de escopo

- expor binario via Composer ou `vendor/bin` no v0.1;
- aliases de comandos;
- servidor local (`dev`, `serve`);
- pipeline de assets;
- comandos de banco (`db:*`);
- geracao automatica de testes;
- atualizacao automatica de documentacao fora do necessario para a CLI;
- inferencia completa de middleware em todos os cenarios de runtime;
- alteracoes em arquivos de `app/components/plugins/**` que nao sejam criados
  explicitamente pelo proprio comando.

## Proposta

Implementar um entrypoint `qzy` na raiz do projeto, executado com `php qzy ...`,
com dispatcher simples e comandos explicitamente registrados.

Recortes do v0.1, nesta ordem:

1. criar bootstrap da CLI e parser minimo de argumentos;
2. implementar `make:component` para `page` e `plugin`;
3. implementar `make:controller` com criacao de rota interativa;
4. implementar `route:list` lendo o estado atual de `app/routes/*.php`;
5. implementar `check` para `routes`, `components` e `config`.

## Contratos

### Entrada principal

- comando raiz: `php qzy <comando> [opcoes]`;
- se nenhum comando for informado, a CLI deve mostrar ajuda resumida e retornar
  codigo diferente de zero;
- comandos desconhecidos devem retornar erro objetivo e ajuda curta.

### `make:component`

Assinatura alvo:

```bash
php qzy make:component [nome] [--type=page|plugin] [--no-interaction]
```

Regras:

- modo interativo por padrao;
- tipos aceitos no v0.1: `page` e `plugin`;
- `page` gera diretoria em `app/components/pages/<nome>/`;
- `plugin` gera diretoria em `app/components/plugins/<nome>/`;
- arquivos minimos gerados: `<nome>.html` e `<nome>.blueprint.php`;
- a CLI deve perguntar se tambem deseja criar shortcut em
  `App\Components\ComponentShortcuts`;
- se `--no-interaction` for usado, faltas de informacao obrigatoria devem virar
  erro explicito.

### `make:controller`

Assinatura alvo:

```bash
php qzy make:controller [nome] [--no-interaction]
```

Regras:

- modo interativo por padrao;
- gera classe em `app/controllers/<Nome>Controller.php`;
- gera rota junto com o controller no v0.1;
- a CLI deve perguntar:
  - arquivo de rota: `web`, `api`, `dev` ou `test`;
  - verbo HTTP;
  - path da rota;
  - metodo do controller a ser apontado, com `index` como padrao sugerido;
- a rota gerada deve seguir o padrao atual de `app/routes/*.php` com
  `$router->map(method: 'GET', path: '/...', handler: 'Controller\...::...');`;
- se `--no-interaction` for usado, a CLI deve exigir os parametros necessarios
  para completar a geracao com seguranca ou falhar com mensagem objetiva.

### `route:list`

Assinatura alvo:

```bash
php qzy route:list
```

Saida minima em texto:

- `method`
- `path`
- `handler`
- `middleware` em modo best effort
- `scope` indicando ao menos `web`, `api`, `dev` ou `test`

Regras:

- a leitura deve partir dos arquivos atuais de `app/routes/*.php`;
- a CLI deve preservar a leitura do estado real do projeto, sem depender de
  manifesto paralelo;
- middleware pode ser parcial no v0.1, desde que a saida deixe claro quando a
  informacao for inferida ou indisponivel.

### `check`

Assinatura alvo:

```bash
php qzy check [--only=routes|components|config] [--strict] [--format=text|json]
```

Regras:

- escopos do v0.1: `routes`, `components`, `config`;
- saida padrao em texto;
- `json` existe para automacao basica, sem exigir schema complexo no v0.1;
- sucesso retorna `0`;
- qualquer falha retorna `1` com resumo objetivo por categoria.

Checks minimos:

- `routes`:
  - confirmar existencia dos arquivos de rota esperados;
  - confirmar que `app/routes/index.php` carrega os modulos previstos;
  - detectar definicoes de rota evidentemente invalidas no padrao atual.
- `components`:
  - validar existencia de arquivos base esperados para componentes gerados;
  - validar referencias principais de `template` e `blueprint`;
  - validar caminhos basicos de `css` e `js` declarados nos blueprints.
- `config`:
  - confirmar existencia dos arquivos obrigatorios de configuracao;
  - confirmar cadeia de `require` do entrypoint `app/config/index.php`;
  - validar constantes-chave como `APP_ENV` e `APP_URL` em nivel sintatico
    minimo.

## Criterios de aceite

- [ ] o projeto passa a ter um entrypoint `qzy` executavel via `php qzy ...`.
- [ ] `make:component` gera `page` e `plugin` com `html + blueprint` no local
  esperado.
- [ ] `make:component` pergunta sobre criacao de shortcut em modo interativo.
- [ ] `make:controller` gera controller e rota seguindo o padrao atual de
  `app/routes/*.php`.
- [ ] `make:controller` pergunta arquivo de rota e verbo HTTP em modo
  interativo.
- [ ] `route:list` lista ao menos `method`, `path`, `handler`, `middleware` best
  effort e `scope`.
- [ ] `check` cobre `routes`, `components` e `config` com `exit code 0/1`
  consistente.
- [ ] a CLI nao altera comportamento HTTP atual do app fora dos arquivos
  explicitamente gerados pelo usuario.

## Plano de migracao

1. Adicionar `qzy` sem impactar o bootstrap web.
2. Implementar dispatcher e ajuda minima.
3. Implementar scaffolding de `make:component`.
4. Implementar scaffolding de `make:controller` com append seguro em arquivo de
   rota.
5. Implementar `route:list` com leitura do estado atual.
6. Implementar `check` com validacoes pequenas e verificaveis.
7. Validar em execucao local e ajustar mensagens de erro.

## Validacao

Comandos minimos esperados:

```bash
php -l qzy
php qzy
php qzy make:component --help
php qzy make:controller --help
php qzy route:list
php qzy check
```

Smoke tests manuais:

- gerar um `page` novo e confirmar criacao de `html` e `blueprint`;
- gerar um `plugin` novo e confirmar criacao de `html` e `blueprint`;
- gerar um `controller` novo, escolher arquivo de rota e verbo, e confirmar
  append no arquivo certo;
- executar `route:list` e confirmar leitura das rotas existentes;
- executar `check` com sucesso no estado valido e com falha controlada em caso
  de referencia quebrada criada para teste local.

## Riscos

- Risco: geracao automatica inserir rota em local errado do arquivo.
- Mitigacao: append simples e previsivel, sem reordenacao nem parser agressivo
  no v0.1.

- Risco: `route:list` tentar inferir middleware alem do que o codigo atual
  permite observar de forma confiavel.
- Mitigacao: tratar middleware como best effort e sinalizar indisponibilidade.

- Risco: `check` crescer demais e virar validador complexo antes da hora.
- Mitigacao: limitar o v0.1 a verificacoes sintaticas e estruturais pequenas.

## Decisoes

- A CLI v0.1 sera executada como `php qzy ...`, sem binario Composer.
- `make:component` cobre apenas `page` e `plugin`.
- `make:controller` sera comando separado e gerara controller + rota.
- O fluxo sera interativo por padrao.
- O v0.1 nao tera aliases, `dev`, `assets` ou `db`.
- `route:list` mostrara middleware em modo best effort.
- `check` cobrira apenas `routes`, `components` e `config`.

## Notas de implementacao

- Preferir dispatcher pequeno com mapa de comandos explicito.
- Evitar dependencias externas so para parsing de argumentos no v0.1.
- Reutilizar convencoes atuais de nomes em `app/controllers`, `app/routes` e
  `app/components`.
- Ao editar `ComponentShortcuts.php`, preservar ordem e estilo do arquivo atual.
- Ao gerar codigo novo, usar named arguments e incluir comentarios sucintos
  conforme as regras do projeto.
