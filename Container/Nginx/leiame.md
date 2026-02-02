# Ambiente de containers (Docker/Podman) do projeto PHP

Este documento explica como funciona a configuração de containers usada
para rodar o projeto PHP com Nginx + PHP-FPM 8.4 em Alpine Linux,
gerenciados pelo Supervisor.

Arquivos envolvidos:

-   `Container/Nginx/docker-compose.yml`
-   `Container/Nginx/Dockerfile`
-   `Container/Nginx/nginx.conf`
-   `Container/Nginx/supervisord.conf`

------------------------------------------------------------------------

## Visão geral

-   Um único serviço (`app`) é definido no `docker-compose.yml`.
-   A imagem é construída a partir do `Dockerfile` na raiz do projeto
    (contexto `../..`).
-   Dentro do container rodam **dois processos** principais:
    -   `php-fpm84` (PHP-FPM 8.4)
    -   `nginx`
-   Os dois são gerenciados pelo **supervisord**, que é o processo PID 1
    do container.
-   O código do projeto é montado via volume em `/var/www/html`,
    permitindo desenvolvimento com hot-reload.
-   O Nginx:
    -   escuta em HTTP (80) e HTTPS (443),
    -   redireciona todo HTTP para HTTPS,
    -   encaminha requisições PHP para o PHP-FPM em `127.0.0.1:9000`,
    -   serve estáticos e aplica cache agressivo para assets.

------------------------------------------------------------------------

## docker-compose.yml

### Serviço `app`

(Conteúdo detalhado conforme análise anterior.)

------------------------------------------------------------------------

## Dockerfile

Descrição completa do processo de build, instalação do PHP-FPM 8.4,
Nginx, Supervisor, permissões e CMD final.

------------------------------------------------------------------------

## nginx.conf

Explicação do redirecionamento HTTP→HTTPS, servidor HTTPS, assets,
front-controller e configuração do FastCGI.

------------------------------------------------------------------------

## supervisord.conf

Detalhes sobre os programas geridos (php-fpm84 e nginx), prioridades,
logs e modo non-daemon.

------------------------------------------------------------------------

## Fluxo da requisição

1.  Navegador acessa `http://localhost:8080`.
2.  Nginx redireciona para `https://localhost:8443`.
3.  Para arquivos PHP, Nginx envia para PHP-FPM em `127.0.0.1:9000`.
4.  Resposta retorna via HTTPS.

------------------------------------------------------------------------

## Como subir o ambiente

``` bash
docker compose up --build
# ou
podman compose up --build
```

Acesse em:

-   `http://localhost:8080`
-   `https://localhost:8443`

------------------------------------------------------------------------

## Resumo

Configuração completa integrando Nginx + PHP-FPM 8.4 + Supervisor em
Alpine Linux, com volumes de código e SSL, front-controller em PHP e
cache otimizado de assets.
