# BASE CONTEXT — Quazymodo

<!--
Finalidade: definir a identidade tecnica e os limites estruturais do projeto.
Este arquivo descreve o que o projeto e (stack/arquitetura) e o que ele nao sera.
-->

## Objetivo
Framework PHP próprio, simples, explícito e sob controle total.

## Stack
- PHP 8.4
- Nginx + PHP-FPM
- RedBeanPHP
- jQuery (v4)
- HTMX (v4)
- Tailwind CSS com daisyUI
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
