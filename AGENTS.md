# AGENTS.md

Purpose: guidance for agentic tools working in this repo.

## Project overview
- PHP app using the Quazymodo framework (component-based UI, PSR-7, League Route).
- Frontend styling via Tailwind browser CDN + daisyUI CDN, with local component CSS when needed.
- Containerized dev setup (Nginx + PHP-FPM 8.4 + Supervisor) in `Container/Nginx`.

## Key paths
- `app/config/index.php`: app configuration entrypoint (loads modular config files).
- `app/routes/index.php`: route definitions entrypoint (League Route).
- `app/controllers/`: controllers extending `Quazymodo\AbstractController`.
- `app/components/`: component blueprints/templates/assets.
- `quazymodo/`: framework core.
- `public/`: web root / front controller.
- `docs/sdd/`: official project management area for specs, roadmap, state, and progress.

## Build / lint / test
### PHP dependencies
```bash
composer install
```

### Tests
- No PHPUnit config (`phpunit*.xml`) and no `tests/` directory in repo.
- There is no single-test command defined yet.

If you add PHPUnit later, use the standard pattern:
```bash
vendor/bin/phpunit --filter "TestName" path/to/TestFile.php
```

### Container runtime (optional)
```bash
podman-compose -f Container/Nginx/docker-compose.yml up -d --build
```

## Code style guidelines
### PHP language and structure
- Namespace per folder, aligned with `composer.json` PSR-4 autoload.
- Classes use `PascalCase`, methods use `camelCase`.
- Prefer explicit `ResponseInterface` return types in controllers.
- Avoid global state; use `app/config/*.php` (via `app/config/index.php`) for configuration constants.

### Formatting
- Indentation: 2 spaces (matching existing files).
- Braces on same line as class/method declaration.
- Keep lines reasonably short; wrap arrays and argument lists as in current style.

### Imports
- Use `use` statements for external classes.
- Group related imports together; no strict ordering enforced in repo.
- Avoid unused imports.

### Types
- Use scalar and class type hints where practical.
- Return types are commonly declared; follow that pattern.
- Nullable types used when needed (e.g., `string|null`).

### Naming conventions
- Controllers: `SomethingController` in `app/controllers`.
- Components: `componentName` in `ComponentFactory::Page/Plugin/Template` calls.
- Blueprint files: `*.blueprint.php`; template files: `*.html`.

### Error handling
- Controllers should return PSR-7 responses via `AbstractController` helpers:
  - `$this->html($component)` for HTML
  - `$this->json($array)` for JSON
- In production, exceptions are handled by `App::handleException` to render error page.
- Avoid `die()` in production paths; prefer exceptions and handled responses.

### Database usage
- Current codebase has Medoo + a `BaseRepository` abstraction.
- RedBean is also installed and bootstrapped through `app/config/index.php`.
- SQLite is stored in `app/writable/db/app.sqlite` (ignored by git).

### Components and templates
- Component slots in HTML use `{{ slot_name }}`.
- Blueprints define `inserts`, `css`, and `js` arrays.
- Assets are injected by `BaseComponent` and can include external URLs.
- Use `ComponentFactory::Page` for page components.

### Frontend assets
- Base pages load Tailwind from `@tailwindcss/browser@4` and daisyUI from jsDelivr CDN.
- Project-specific frontend CSS lives in local component stylesheets such as `app/components/pages/base/base-cdn.css`.

## Repo-specific notes
- No Cursor or Copilot instruction files detected.
- `app/writable/` is ignored by git (logs, cache, sqlite db).
- Container image uses Alpine and PHP 8.4; ensure required extensions are installed.
- Keep project management in `docs/sdd/`; `.opencode/` is only for agent rules/context.

## When adding new code
- Follow existing directory structure and naming.
- Update route files under `app/routes/` for new endpoints.
- Prefer small, composable components over large templates.
- Keep UI assets scoped to component directories.
