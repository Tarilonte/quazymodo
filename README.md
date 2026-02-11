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
4.  **Frontend Assets (se aplicável):**
    *   Se estiver usando TailwindCSS ou outras ferramentas de frontend gerenciadas por Node.js (sugerido pela presença de `package.json` e `tailwind.config.js`), instale as dependências:
        ```bash
        npm install
        ```
    *   Execute o processo de build do frontend:
        ```bash
        npm run build # ou similar, dependendo dos scripts em package.json
        ```

## Tecnologias Utilizadas (Principais)

*   **PHP**
*   **League\Route:** Para roteamento.
*   **Nyholm PSR-7:** Implementação de PSR-7 para requisições e respostas HTTP.
*   **Medoo:** Micro-framework para interações com banco de dados.
*   **HTMX (sugerido):** Para interatividade no frontend.
*   **TailwindCSS (sugerido):** Para estilização CSS.

Este README fornece uma visão geral do Quazymodo com base na estrutura e nos arquivos do projeto.
