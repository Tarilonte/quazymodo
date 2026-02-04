# BASE CONTEXT — Quazymodo

## Objetivo
Framework PHP próprio, simples, explícito e sob controle total.

## Stack
- PHP 8.4
- Nginx + PHP-FPM
- RedBeanPHP (modo controlado)
- HTMX (v4)
- Alpine.js (mínimo)
- Podman (rootless / WSL)

## Arquitetura
- Controllers estendem AbstractController
- Views = componentes
- Plugins em app/components/plugins
- Assets versionados por hash de data
- ENV próprio (sem Dotenv)

## Decisões fixas
- Não usar Laravel, Symfony full, Blade ou Twig
- Sem Service Container
- Sem annotations
- Sem migrations automáticas
- Código explícito > abstração
