# RedBeanPHP - Manual Completo Consolidado

## Escopo

Este manual consolida a documentacao publica do RedBeanPHP em um unico arquivo, incluindo explicacoes praticas e exemplos de codigo PHP por topico.

Fontes consolidadas: `https://redbeanphp.com` e paginas principais de introduction, basics, relations, advanced e project.

## Sumario

1. Visao geral
2. Instalacao, requisitos e conexao
3. CRUD e ciclo de vida dos beans
4. Busca e consultas SQL
5. Relacoes
6. Recursos avancados
7. Database tools, transacoes, fluid/frozen e debug
8. Projeto, versoes e ecossistema
9. Catalogo de metodos `R::...`
10. Boas praticas e anti-patterns
11. Notas de ambiguidade/desatualizacao
12. Checklist de adocao

---

## 1) Visao geral

- RedBeanPHP e um ORM zero config com facade estatica `R::...`.
- Modelo principal: um bean representa um registro; o tipo do bean representa a tabela.
- Modo fluid: cria/altera schema automaticamente durante desenvolvimento.
- Modo frozen: nao altera schema, recomendado para producao.
- Filosofia: produtividade alta para dominio e CRUD, sem esconder SQL quando SQL for melhor.

---

## 2) Instalacao, requisitos e conexao

### 2.1 Download e install

- Fluxo classico: baixar pacote, validar hash/assinatura, incluir `rb.php`.
- Fluxo alternativo: Composer (tambem documentado).

Exemplo minimo:

```php
<?php
// Bootstrap minimo do RedBeanPHP
require 'rb.php';
```

### 2.2 Requisitos

- PHP com PDO.
- Driver PDO do banco usado.
- MBString.

Bancos citados no site:

- MySQL/MariaDB
- PostgreSQL
- SQLite
- CUBRID
- Firebird/Interbase (com ressalvas)

### 2.3 Conexao

```php
<?php
require 'rb.php';

// Conexao MySQL/MariaDB
R::setup('mysql:host=localhost;dbname=app', 'user', 'password');

// Opcional e recomendado no site para linhas 5.3+
R::useFeatureSet('novice/latest');

// Fechar conexao quando necessario
R::close();
```

SQLite rapido:

```php
<?php
require 'rb.php';

// SQLite temporario
R::setup();
```

---

## 3) CRUD e ciclo de vida dos beans

### 3.1 Criar, ler, atualizar, deletar

```php
<?php
require 'rb.php';
R::setup('sqlite:/tmp/app.db');

// Criar bean
$post = R::dispense('post');
$post->title = 'Hello RedBean';
$post->body = 'Conteudo inicial';
$id = R::store($post);

// Ler bean
$loadedPost = R::load('post', $id);

// Atualizar bean
$loadedPost->body = 'Conteudo atualizado';
R::store($loadedPost);

// Deletar bean
R::trash($loadedPost);
```

Notas importantes:

- `R::load('tipo', $id)` retorna bean vazio (`id = 0`) quando nao encontra.
- `R::store()` cria ou atualiza conforme estado atual do bean.

### 3.2 Operacoes em lote

```php
<?php
$books = R::loadAll('book', [1, 2, 3]);
R::trashBatch('book', [1, 2, 3]);
```

### 3.3 Convencoes

- Tipo do bean deve ser simples e consistente (ex.: `book`, `page`).
- PK padrao: `id`.
- Campos `*_id` costumam ser usados para relacao.
- camelCase em propriedade pode virar snake_case no schema.

### 3.4 Hooks de modelo (FUSE)

Hooks comuns:

- `dispense()`
- `open()`
- `update()`
- `after_update()`
- `delete()`
- `after_delete()`

Exemplo:

```php
<?php
// Modelo com regra de dominio no ciclo update
class Model_Band extends RedBean_SimpleModel {
  public function update() {
    if (count($this->bean->ownMemberList) > 4) {
      throw new Exception('Band limit exceeded');
    }
  }
}
```

---

## 4) Busca e consultas SQL

### 4.1 Finding API

```php
<?php
$books = R::find('book', ' rating > ? ', [4]);
$book = R::findOne('book', ' title = ? ', ['SQL Dreams']);
$allBooks = R::findAll('book', ' ORDER BY title DESC LIMIT 10 ');
```

`IN (...)` com slots:

```php
<?php
$ids = [3, 5, 8];
$people = R::find(
  'person',
  ' contract_id IN (' . R::genSlots($ids) . ') ',
  $ids
);
```

Outros metodos:

- `findLike`
- `findOrCreate`
- `hunt` (buscar e remover)

### 4.2 SQL direto

```php
<?php
R::exec('UPDATE page SET title = ? WHERE id = ?', ['Home', 1]);

$rows = R::getAll('SELECT * FROM page WHERE title = :title', [':title' => 'Home']);
$row = R::getRow('SELECT * FROM page WHERE id = ? LIMIT 1', [1]);
$col = R::getCol('SELECT title FROM page');
$cell = R::getCell('SELECT title FROM page LIMIT 1');
$assoc = R::getAssoc('SELECT id, title FROM page');
```

### 4.3 Extended SQL

Exemplos usuais:

```php
<?php
$people = R::find('person', ' @joined.movement.name = ? ', ['romanticism']);
$writers = R::find('person', ' @shared.tag.title = ? ', ['writer']);
```

Com alias:

```php
<?php
$books = R::find('book', ' @joined.person[as:author].firstname = ? ', ['Bob']);
```

### 4.4 SQL snippets em relacoes

```php
<?php
$pages = $book->with(' ORDER BY pagenum ASC ')->ownPageList;
$vases = $shop->withCondition(' category = ? ORDER BY price ASC ', ['vase'])->ownProductList;
```

---

## 5) Relacoes

### 5.1 One-to-many

```php
<?php
$shop = R::dispense('shop');
$shop->name = 'Antiques';

$product = R::dispense('product');
$product->price = 25;

$shop->ownProductList[] = $product;
R::store($shop);
```

- `ownXxxList`: remove da lista tende a desassociar.
- `xownXxxList`: remove da lista tende a excluir dependente (semantica exclusiva).

### 5.2 Many-to-one

```php
<?php
$product = R::load('product', 10);
$shop = $product->shop;

$product->shop = R::load('shop', 2);
R::store($product);

$product->shop = null;
R::store($product);
```

### 5.3 Aliases

```php
<?php
$course = R::dispense('course');
$course->teacher = R::dispense('person');
$course->student = R::dispense('person');
$id = R::store($course);

$course = R::load('course', $id);
$teacher = $course->fetchAs('person')->teacher;
```

Aliases globais:

```php
<?php
R::aliases([
  'teacher' => 'person',
  'student' => 'person',
]);
```

### 5.4 Many-to-many

```php
<?php
[$vase, $lamp] = R::dispense('product', 2);
$tag = R::dispense('tag');
$tag->name = 'Art Deco';

$vase->sharedTagList[] = $tag;
$lamp->sharedTagList[] = $tag;
R::storeAll([$vase, $lamp]);
```

### 5.5 Link beans

```php
<?php
[$employee, $project] = R::dispenseAll('employee,project');
$project->link('employee_project', ['role' => 'director'])->employee = $employee;
R::store($project);
```

### 5.6 Counting

```php
<?php
$totalBooks = R::count('book', ' pages > ? ', [250]);
$numPages = $book->countOwn('page');
$numProjects = $member->countShared('project');
```

### 5.7 Labels, enums e tags

Labels:

```php
<?php
$labels = R::dispenseLabels('meals', ['pizza', 'pasta']);
$labelNames = R::gatherLabels($labels);
```

Enums:

```php
<?php
$tea = R::dispense('tea');
$tea->flavour = R::enum('flavour:english');
R::store($tea);

$isEnglish = $tea->flavour->equals(R::enum('flavour:english'));
```

Tags:

```php
<?php
R::tag($page, ['topsecret', 'mi6']);
$tags = R::tag($page);
R::addTags($page, ['funny']);
R::untag($page, ['mi6']);
```

### 5.8 Trees (pagina /trees)

Modelagem auto-relacional:

```php
<?php
$page = R::dispense('page');
$page->title = 'Home';

$childPage = R::dispense('page');
$childPage->title = 'Section';

$page->ownPageList[] = $childPage;
R::store($page);
```

Traverse:

```php
<?php
$titles = [];

$page->traverse('ownPage', function($node) use (&$titles) {
  $titles[] = $node->title;
}, 3);
```

CTE helpers:

```php
<?php
$descendants = R::children($homePage, ' ORDER BY title ASC ');
$ancestors = R::parents($leafPage, ' ORDER BY title ASC ');
$countChildren = R::countChildren($homePage);
$countParents = R::countParents($leafPage);
```

Nota: nas docs, recursos de trees/CTE em certas versoes aparecem como experimentais.

---

## 6) Recursos avancados

### 6.1 Meta data

```php
<?php
$beanType = $bean->getMeta('type');
$bean->setMeta('my.secret.property', 'secret');

$isTainted = $bean->isTainted();
$hasChanged = $book->hasChanged('title');
$oldTitle = $book->old('title');
$listChanged = $author->hasListChanged('ownBook');
```

### 6.2 Duplicate

```php
<?php
$bookCopy = R::duplicate($book);
$bookCopy->title = 'Book Copy';
R::store($bookCopy);
```

### 6.3 Import/export

```php
<?php
$book->import($_POST, 'title,summary,price');
$book->importFrom($otherBean);

$exported = $book->export();
$allExported = R::exportAll([$book], true);
```

Dispense por array estruturado:

```php
<?php
$book = R::dispense([
  '_type' => 'book',
  'title' => 'Gifted Programmers',
  'author' => [
    '_type' => 'author',
    'name' => 'Xavier',
  ],
  'ownPageList' => [
    ['_type' => 'page', 'text' => 'Page content'],
  ],
]);
```

### 6.4 Non-static

Adaptação didatica para uso sem facade estatica:

```php
<?php
use RedBeanPHP\Driver\RPDO;
use RedBeanPHP\Adapter\DBAdapter;
use RedBeanPHP\QueryWriter\MySQL;
use RedBeanPHP\OODB;
use RedBeanPHP\ToolBox;
use RedBeanPHP\Finder;

$pdo = new RPDO('mysql:host=localhost;dbname=test', 'user', 'pass');
$adapter = new DBAdapter($pdo);
$writer = new MySQL($adapter);
$oodb = new OODB($writer);
$toolbox = new ToolBox($oodb, $adapter, $writer);

$finder = new Finder($toolbox);
$music = $finder->find('music', ' composer = ? ', ['Bach']);
```

### 6.5 UUIDs

- As docs tratam UUID como customizacao avancada (QueryWriter custom, setup especifico).
- Nao e modo padrao plug-and-play para todos os cenarios.

### 6.6 Templates DDL

```php
<?php
$writer = R::getWriter();
$writer->setDDLTemplate(
  'createTable',
  '*',
  $writer->getDDLTemplate('createTable', '*') . ' ROW_FORMAT=DYNAMIC '
);
```

### 6.7 Prefixes

```php
<?php
R::ext('xdispense', function($type) {
  return R::getRedBean()->dispense($type);
});

$page = R::xdispense('cms_page');

R::renameAssociation([
  'tbl_book_tbl_category' => 'tbl_book_category',
]);
```

Nota: docs alertam risco de usar prefixos como estrategia de multi-tenant no mesmo banco.

### 6.8 Query builder

- O core nao tem query builder oficial; a recomendacao e SQL direto.

### 6.9 LOBs

- Nao ha camada ORM dedicada para LOBs; usar PDO direto para esse tipo de operacao.

### 6.10 Migrations

Fluxo sugerido nas docs:

1. Rodar migracao controlada em fluid.
2. Aplicar ajustes de schema.
3. Voltar para frozen em producao.

---

## 7) Database tools, transacoes, fluid/frozen e debug

### 7.1 Database tools

```php
<?php
$tableInfo = R::inspect('book');
$allTables = R::inspect();

R::addDatabase('analytics', 'mysql:host=localhost;dbname=analytics', 'user', 'pass');
R::selectDatabase('analytics');

R::resetQueryCount();
$beforeCount = R::getQueryCount();

R::startLogging();
$logs = R::getLogs();
```

### 7.2 Transacoes

```php
<?php
R::begin();

try {
  R::store($order);
  R::store($payment);
  R::commit();
} catch (Exception $exception) {
  R::rollback();
}
```

Com closure:

```php
<?php
R::transaction(function() {
  // bloco atomico
});
```

### 7.3 Fluid e frozen

```php
<?php
R::setup('mysql:host=localhost;dbname=app', 'user', 'pass');

// Desenvolvimento
R::freeze(false);

// Producao
R::freeze(true);
```

Freeze parcial:

```php
<?php
R::freeze(['book', 'page', 'book_page']);
```

Hybrid mode:

```php
<?php
R::store($bean, true);
R::storeAll($beans, true);
```

### 7.4 Debugging

```php
<?php
R::fancyDebug(true);
R::debug(true, 2);

$connected = R::testConnection();
```

Inspecao de beans:

```php
<?php
echo $bean;
print_r(R::dump($beans));
dmp($bean);
```

---

## 8) Projeto, versoes e ecossistema

### 8.1 Changelog (resumo)

- 5.7.5 (2025): correcoes e melhorias.
- 5.7.4: compatibilidade PHP 8.2 e ajustes.
- 5.7.3: introduz `either()`.
- 5.7.2: `trimport()`, TypedModel.
- 5.6: DDL templates, `findFromSQL`, `info()`.
- 5.5: extended SQL `@joined/@own/@shared`, `loadJoined`.

### 8.2 Roadmap

- Linha 5.x com foco em estabilidade e compatibilidade.

### 8.3 Beta

- Recursos em pre-release disponiveis para testes via branch principal do repositorio.

### 8.4 About / plugins / frameworks / sqn

- Posicionamento de ponte entre ORM e SQL.
- Plugins e integracoes existem, com manutencao variavel (ecossistema comunitario).
- `SQN` aparece como recurso/plugin para join por convencao.

### 8.5 Licenca e historico

- Licenca dual New BSD + GPLv2.
- Arquivos historicos em `archives`.
- Creditos da comunidade em `credits`.

---

## 9) Catalogo de metodos `R::...`

### Setup e ambiente

- `setup`, `close`, `freeze`, `useFeatureSet`, `testConnection`

### CRUD

- `dispense`, `dispenseAll`, `store`, `storeAll`, `load`, `loadAll`, `loadForUpdate`, `trash`, `trashAll`, `trashBatch`, `wipe`, `nuke`

### Busca

- `find`, `findOne`, `findAll`, `findLike`, `findOrCreate`, `findMulti`, `findCollection`, `findFromSQL`, `loadJoined`, `hunt`

### SQL direto

- `exec`, `getAll`, `getRow`, `getCol`, `getCell`, `getAssoc`, `getInsertID`

### Relacoes e classificacao

- `aliases`, `renameAssociation`, `tag`, `untag`, `addTags`, `tagged`, `taggedAll`, `countTagged`, `countTaggedAll`, `enum`, `dispenseLabels`, `gatherLabels`

### Trees

- `children`, `parents`, `countChildren`, `countParents`

### Dados utilitarios

- `look`, `matchUp`, `csv`, `diff`, `duplicate`, `exportAll`, `convertToBean`, `convertToBeans`, `genSlots`

### Database tools e observabilidade

- `inspect`, `addDatabase`, `selectDatabase`, `startLogging`, `getLogs`, `resetQueryCount`, `getQueryCount`, `getDatabaseServerVersion`

### Transacao

- `begin`, `commit`, `rollback`, `transaction`

### Debug

- `debug`, `fancyDebug`, `dump`

---

## 10) Boas praticas e anti-patterns

### Boas praticas

- Em dev, use fluid; em producao, use frozen.
- Sempre usar bind de parametros em SQL.
- Revisar schema antes de congelar: indices, FKs, constraints.
- Usar SQL direto para relatorios pesados.
- Evitar N+1 com estrategia de carregamento apropriada.
- Usar hooks de modelo para regras de dominio.

### Anti-patterns comuns

- Deixar app em fluid em producao.
- Tentar resolver analytics complexa so com beans.
- Ignorar diferencas de recursos por banco.
- Usar prefixos como isolamento multi-tenant sem arquitetura adequada.

---

## 11) Notas de ambiguidade e desatualizacao

- Nota: varias paginas mantem contexto historico (HHVM, PHP 5.x, patch antigo).
- Nota: exemplos antigos podem exigir adaptacao para versoes mais novas de PHP.
- Nota: certos recursos (especialmente em trees/CTE em versoes antigas) aparecem como experimentais.
- Nota: texto sobre strict mode do MySQL pode nao refletir todas as combinacoes modernas de driver/versao.
- Nota: quando houver conflito entre pagina antiga e changelog recente, priorizar comportamento da versao alvo instalada.

---

## 12) Checklist de adocao em projeto existente

1. Confirmar compatibilidade de naming conventions (`id`, `*_id`, tipos).
2. Definir estrategia de entrada (gradual ou modulo novo).
3. Validar recursos do banco usados pelo dominio (CTE, JSON, locks).
4. Padronizar SQL com bindings obrigatorios.
5. Ativar logs de query em ambiente de desenvolvimento.
6. Revisar schema manualmente antes de congelar.
7. Ativar `R::freeze(true)` no deploy de producao.
8. Definir padrao de models/hooks para regras de negocio.
9. Documentar versao alvo do RedBean e politicas de upgrade.

---

## Apendice A - Bootstrap recomendado

```php
<?php
// Bootstrap recomendado para projeto novo
require 'rb.php';

R::setup('mysql:host=localhost;dbname=app', 'user', 'pass');
R::useFeatureSet('novice/latest');
R::freeze(false); // dev

$post = R::dispense('post');
$post->title = 'Hello';
R::store($post);

// Em producao:
// R::freeze(true);
```
