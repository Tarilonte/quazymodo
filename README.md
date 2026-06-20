# Quazymodo PHP Framework

Quazymodo é um framework PHP minimalista projetado para o desenvolvimento rápido de aplicações web, com foco na componentização da interface do usuário e integração com ferramentas modernas de frontend.

## O que é Quazymodo?

Quazymodo é uma plataforma PHP que visa simplificar o desenvolvimento web através de uma arquitetura baseada em componentes, um sistema de roteamento flexível e ferramentas integradas para tarefas comuns como interação com banco de dados e gerenciamento de Content Security Policy (CSP). Ele é construído para ser leve e direto ao ponto.

## Principais Características

*   **Arquitetura Baseada em Componentes:** A interface do usuário é construída usando componentes reutilizáveis, cada um com seu próprio blueprint (PHP) e template (HTML). Veja `quazymodo/BaseComponent.php` e `quazymodo/ComponentFactory.php`.
*   **Roteamento:** Utiliza a biblioteca `league/route` para um sistema de roteamento poderoso e flexível. As rotas são definidas em `app/routes/index.php` e organizadas em `app/routes/`.
*   **Requisição e Resposta PSR-7:** Lida com requisições e respostas HTTP seguindo o padrão PSR-7, inicializado em `quazymodo/App.php`.
*   **Banco de Dados com Medoo:** Integração com o micro-framework de banco de dados Medoo através da classe wrapper `Quazymodo\Database`, com suporte a debugging de queries (`Quazymodo\MedooDebug`).
*   **Gerenciamento de Assets:** CSS e JavaScript são definidos nos blueprints dos componentes e injetados automaticamente nas páginas, com versionamento de arquivos.
*   **Content Security Policy (CSP):** Gerenciador de CSP (`Quazymodo\CSPManager`) para aumentar a segurança da aplicação, aplicado automaticamente em respostas HTML via `Quazymodo\AbstractController.php`.
*   **HTMX Ready:** Projetado para funcionar bem com bibliotecas como HTMX para interações dinâmicas no frontend (visto em exemplos como `app/components/templates/forms/form-login.html`).
*   **TailwindCSS:** Embora não seja um recurso central do backend, a estrutura dos componentes e exemplos de templates (e.g., `app/components/pages/home/home.html`) indicam o uso de TailwindCSS para estilização.
*   **Debugging:** Ferramentas de debug integradas para desenvolvimento, incluindo informações sobre componentes e queries de banco de dados.

## Estrutura de Diretórios Chave

*   `app/`: Contém a lógica principal da sua aplicação.
    *   `config/index.php`: Entrypoint de configuração da aplicação (modularizado em `app/config/*.php`).
    *   `routes/index.php`: Entrypoint de rotas da aplicação (modularizado em `app/routes/*.php`).
    *   `components/`: Onde os componentes da interface do usuário (blueprints, templates, assets específicos) são definidos.
    *   `controllers/`: Controladores que lidam com a lógica de requisição.
    *   `Entities/`: (Provavelmente) Entidades ou modelos de dados.
*   `quazymodo/`: O núcleo do framework Quazymodo.
    *   `App.php`: Ponto de entrada e orquestrador principal do framework.
    *   `BaseComponent.php`: Classe base para todos os componentes visuais.
    *   `AbstractController.php`: Classe base para os controladores da aplicação.
*   `public/`: Raiz pública da aplicação, contém o `index.php` e assets públicos.
*   `vendor/`: Dependências do Composer.

## Componentes

Os componentes são a espinha dorsal da construção da UI no Quazymodo. Eles são tipicamente encontrados em `app/components/`.

Um componente geralmente consiste em:

1.  **Blueprint (`.blueprint.php`):** Um arquivo PHP que define metadados do componente, como:
    *   `extends`: Para herdar de outro blueprint base (e.g., um layout de página).
    *   `css`: Array de arquivos CSS a serem incluídos.
    *   `js`: Array de arquivos JavaScript a serem incluídos.
    *   `inserts`: Um array associativo onde as chaves são nomes de "slots" no template e os valores são o conteúdo a ser injetado nesses slots. O conteúdo pode ser strings, outros componentes (usando `Quazymodo\ComponentFactory`), etc.
    *   Exemplo: `app/components/pages/home/home.blueprint.php`

2.  **Template (`.html`):** Um arquivo HTML que define a estrutura do componente. Ele usa placeholders no formato `{{ nome_do_slot }}` que serão preenchidos com os dados definidos nos `inserts` do blueprint.
    *   Exemplo: `app/components/templates/pages/home.html`

A classe `Quazymodo\ComponentFactory` é usada para criar instâncias de componentes (`Page`, `Plugin`, `Template`), que são então processados pela `Quazymodo\BaseComponent` para renderizar o HTML final.

### Slots pré-preenchidos

Templates podem declarar conteúdo padrão diretamente em um slot. O conteúdo declarado no template entra como preenchimento inicial, com prioridade menor que os inserts do blueprint e os inserts passados em runtime.

Exemplo simples:

```html
{{ navbar-logo = template:/plugins/logo/ }}
```

Durante a preparação do componente, o HTML é normalizado em memória para:

```html
{{ navbar-logo }}
```

E o valor padrão é armazenado em `prefilledSlots`, seguindo o mesmo fluxo de preenchimento de `ComponentData`.

Também é possível declarar inserts internos para o componente padrão usando bloco:

```html
{{ navbar-logo = template:/plugins/logo/ }}
  {{ logo-class = 'w-8 fill-primary' }}
{{ /navbar-logo }}
```

Nesse caso, `navbar-logo` recebe o componente `template:/plugins/logo/`, e `logo-class` é enviado como insert para esse componente.

Valores internos aceitos no bloco:

```html
{{ title = 'Texto com aspas simples' }}
{{ subtitle = "Texto com aspas duplas" }}
{{ icon = template:/plugins/icon/ }}
{{ actions = plugin:/plugins/actions/ }}
{{ title@append = ' extra' }}
```

As declarações usam o mesmo `componentName` aceito por `ComponentFactory::Plugin()` e `ComponentFactory::Template()`. Não há shorthand de caminho: use caminhos como `/plugins/logo/` quando esse for o nome do componente.

Todo conteúdo dentro de um bloco pré-preenchido deve declarar explicitamente o slot de destino. Conteúdo solto dentro do bloco é inválido.

## Roteamento

As rotas são definidas no arquivo `app/routes/index.php` (com arquivos de contexto em `app/routes/`) usando a sintaxe da biblioteca `league/route`.

Exemplo de definição de rota:
```php
// filepath: app/routes/index.php
// ...
$router->map('GET', '/', 'Controller\IndexController::index');
$router->map(['GET','POST'], '/test/{test:.*}', 'Controller\Test\TestController::index');
// ...
```
Isso mapeia uma requisição GET para a URL `/` para o método `index` da classe `Controller\IndexController`.

## Configuração

A configuração principal da aplicação reside em `app/config/index.php`. Este entrypoint carrega arquivos modulares em `app/config/*.php` para:

*   Ambiente da aplicação (`APP_ENV`)
*   URL da aplicação (`APP_URL`)
*   Nome da aplicação (`APP_NAME`)
*   Configurações de timezone e locale
*   Habilitação de sessão (`APP_SESSION_ENABLE`)
*   Habilitação de CSP (`APP_CSP_ENABLED`)
*   Credenciais de banco de dados (`DB`)
*   Constantes para assets comuns (e.g., `ASSET_HTMX`, `ASSET_JQUERY`)

## CSP

CSP (Content Security Policy) restringe origens de scripts, frames e outros recursos carregados pela página. Serve para reduzir XSS e conteúdo não autorizado.

No Quazymodo:

*   `Quazymodo\CSPManager` monta diretivas, gera nonce e adiciona `'nonce-...'` em `script-src`.
*   `Quazymodo\AbstractController::html()` lê as diretivas e envia `Content-Security-Policy` nas respostas HTML.
*   `BaseComponent` chama `CSPManager::setNonce()` em páginas, então o nonce fica disponível para componentes e templates.

Como usar:

*   Ative `APP_CSP_ENABLED`.
*   Para liberar origem extra, use `CSPManager::addSource('script-src', 'https://exemplo.com')`.
*   Para inline script com nonce, leia `CSPManager::getNonce()` e passe valor para template ou componente.
*   Páginas criadas com `ComponentFactory::Page()` já entram no fluxo de nonce automaticamente.

### Uso com componente de atributo nonce

Quando o template precisa aplicar o nonce atual em uma tag `<script>`, o plugin
`/plugins/nonceAttributeCp/` pode ser injetado diretamente no atributo da tag.

Exemplo de uso por slot pre-preenchido:

```html
<script {{ nonce = plugin:/plugins/nonceAttributeCp/ }}>
  console.log('script protegido por CSP');
</script>
```

Depois do render, a tag recebe um fragmento como:

```html
<script nonce="...">
  console.log('script protegido por CSP');
</script>
```

Direcao de uso:

*   use esse padrao para scripts inline que precisem obedecer ao `script-src` com nonce;
*   prefira esse componente dentro da propria tag `<script>`, nao em slot de `body` solto;
*   quando usar `/plugins/jsComponent/`, o nonce ja entra pelo fluxo do proprio componente.

## CSRF

O Quazymodo possui protecao de CSRF baseada em sessao, com validacao
centralizada em middleware e aplicacao explicita por rota.

Arquivos principais:

*   `quazymodo/Csrf.php`: gera e verifica token em `$_SESSION['csrf-token']`.
*   `app/middleware/CsrfMiddleware.php`: exige token valido sempre que estiver anexado a uma rota.
*   `app/routes/web.php`: declara `CsrfMiddleware` individualmente apenas nas rotas protegidas.

### Como funciona

Fluxo atual:

1.  Gere token com `Csrf::setToken()` antes de renderizar form ou tela que fara submissao mutante.
2.  Envie token no body como campo `csrf-token` ou no header `X-CSRF-Token`.
3.  Anexe `CsrfMiddleware` apenas na rota que deve exigir CSRF.
4.  Quando middleware estiver anexado, ele sempre valida token antes do controller, independentemente do metodo HTTP.
5.  Se token estiver ausente ou invalido, requisicao para antes do controller com `403`.

Sem middleware anexado:

*   rota nao exige CSRF por efeito lateral.

Fora do escopo atual:

*   rotas fora de `app/routes/web.php`, como `api`, `dev` e `test`, salvo declaracao explicita futura.

### Exemplo de rota protegida

Exemplo de declaracao em `app/routes/web.php`:

```php
$router->map(
  method: 'POST',
  path: '/minha-rota-web',
  handler: 'Controller\MinhaController::salvar',
)->middleware(middleware: new Middleware\CsrfMiddleware());
```

Nesse padrao:

*   a rota continua sendo registrada normalmente com `league/route`;
*   o middleware e anexado apenas na rota que exige CSRF;
*   qualquer requisicao nessa rota precisa enviar token valido por body `csrf-token` ou header `X-CSRF-Token`.

### Uso em formularios HTML

No controller:

```php
use Quazymodo\Csrf;

$token = Csrf::setToken();
```

No template HTML:

```html
<form method="POST" action="/minha-rota-web">
  <input type="hidden" name="csrf-token" value="{{ csrf-token }}">
  <button type="submit">
    Enviar
  </button>
</form>
```

Direcao de uso:

*   Gere token antes de renderizar formulario.
*   Passe token para componente/template como insert normal.
*   Inclua campo oculto `csrf-token` em formularios que apontem para rotas com `CsrfMiddleware`.

### Uso com slot pre-preenchido em templates de formulario

Quando o template do formulario ja usa slots pre-preenchidos, o componente
`/plugins/csrfTokenComponent/` pode ser injetado direto no HTML do template,
evitando repetir o hidden input manualmente.

Exemplo:

```html
<form method="POST" action="/minha-rota-web">
  {{ csrf-field = plugin:/plugins/csrfTokenComponent/ }}

  <input type="email" name="email">
  <button type="submit">
    Enviar
  </button>
</form>
```

Direcao de uso:

*   Use esse padrao em templates de formulario reutilizaveis.
*   O componente renderiza apenas `<input type="hidden" name="csrf-token" ...>`.
*   O token atual e reutilizado da sessao quando ja existir; caso contrario, e gerado no render.
*   Nao redeclare o mesmo slot depois da declaracao pre-preenchida; a normalizacao ja converte a declaracao para o slot simples internamente.
*   O slot continua opcional: so injete quando formulario apontar para rota protegida com `CsrfMiddleware`.

### Uso com HTMX, fetch ou AJAX

Quando submissao nao usar form tradicional, envie token no header `X-CSRF-Token`.

Exemplo conceitual com `fetch`:

```js
fetch('/minha-rota-web', {
  method: 'POST',
  headers: {
    'X-CSRF-Token': csrfToken,
  },
  body: formData,
});
```

Direcao de uso:

*   Reutilize mesmo token gerado no render da pagina.
*   Prefira body `csrf-token` em forms.
*   Use `X-CSRF-Token` como fallback para HTMX/AJAX/fetch quando body nao for conveniente.

### Observacoes importantes

*   A sessao precisa estar habilitada. Hoje isso ocorre via `app/config/session.php` quando `APP_SESSION_ENABLE === 1`.
*   O middleware so cobre rotas onde ele for declarado explicitamente.
*   Controllers cobertos por esse middleware nao precisam validar CSRF manualmente.
*   Se uma nova rota mutante for registrada em `app/routes/web.php`, ela so exigira token quando declarar `CsrfMiddleware`.

## Como Começar (Inferido)

1.  **Instalar Dependências PHP:**
    ```bash
    composer install
    ```
2.  **Configurar o Ambiente:**
    *   Copie ou renomeie qualquer arquivo de configuração de exemplo (se houver).
    *   Ajuste as configurações em `app/config/*.php`, especialmente as credenciais do banco de dados e `APP_URL`.
3.  **Configurar o Servidor Web:**
    *   Aponte a raiz do seu servidor web para o diretório `public`.
    *   Certifique-se de que o `mod_rewrite` (Apache) ou configuração equivalente (Nginx) esteja habilitado para que o `public/.htaccess` (ou regras equivalentes) funcione corretamente.
4.  **Frontend Assets:**
    *   O frontend base carrega Tailwind CSS e daisyUI via CDN em runtime.
    *   Os ajustes visuais especificos do projeto ficam em arquivos CSS locais dos componentes, como `app/components/pages/base/base-cdn.css`.

## CLI

O projeto possui uma CLI local chamada `qzy` para scaffolding e inspecao basica.

Manual de uso:

- `docs/cli.md`

## Tecnologias Utilizadas (Principais)

*   **PHP**
*   **League\Route:** Para roteamento.
*   **Nyholm PSR-7:** Implementação de PSR-7 para requisições e respostas HTTP.
*   **Medoo:** Micro-framework para interações com banco de dados.
*   **HTMX (sugerido):** Para interatividade no frontend.
*   **Tailwind CSS + daisyUI via CDN:** Para estilização CSS e componentes visuais.

Este README fornece uma visão geral do Quazymodo com base na estrutura e nos arquivos do projeto.
