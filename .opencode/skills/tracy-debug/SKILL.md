---
name: tracy-debug
description: Configure and use Tracy safely in Quazymodo with practical debug, logging, and production guardrails.
license: MIT
compatibility: opencode
metadata:
  audience: maintainers
  stack: php-8.4
  framework: quazymodo
---

## Purpose
- Help implement and review Tracy usage in this project with current practices.
- Prefer explicit, predictable configuration aligned with Quazymodo conventions.

## Use When
- You need to enable Tracy in bootstrap/app startup.
- You need to configure development vs production behavior.
- You need logging, timer, `dump()`, `barDump()`, or custom Tracy panels.
- You need to validate CSP, session storage, or AJAX diagnostics in Tracy Bar.

## Avoid When
- The task is unrelated to debugging/observability.
- The request is purely visual frontend work with no runtime diagnostics.

## Project-Specific Guardrails
- Target stack is PHP 8.4; avoid legacy Tracy guidance tied to old PHP versions.
- Prefer Tracy docs for versions compatible with PHP 8.4.
- In production, do not expose stack traces or dumps to end users.
- Prefer explicit `Debugger::enable(...)` mode and log directory configuration.
- Hide sensitive data in dumps via `Debugger::$keysToHide` and scrubber patterns.
- Keep controllers returning PSR-7 responses; avoid injecting HTML in controllers.
- Keep JavaScript out of templates.

## Recommended Workflow
1. Confirm where startup bootstrap happens and place Tracy enablement early.
2. Set mode intentionally (`DEVELOPMENT` or `PRODUCTION`) for the environment.
3. Configure log directory and optional email/reporting strategy.
4. Configure safe dump behavior (`$keysToHide`, scrubber, depth/length limits).
5. Validate CSP (`script-src` nonce + `strict-dynamic`) and session strategy when used.
6. Use `dump()`/`barDump()` only for development diagnostics.
7. Validate behavior in production mode (silent dumps, error logging, safe UX).

## Practical Checklist
- Is Tracy enabled before output is sent?
- Is production mode safe (no detailed errors to users)?
- Is log directory writable and outside sensitive exposure paths?
- Are `dump()` and debug bar assumptions validated for production?
- Are secrets protected in dumps (`$keysToHide` or BlueScreen scrubber)?
- Are CSP requirements met (`script-src` nonce and `strict-dynamic`)?
- If nginx is used, does `try_files` preserve query string (`$is_args$args`)?
- If AJAX debugging is needed, is `X-Tracy-Ajax`/`Tracy.getAjaxHeader()` handled?

## Output Style
- Provide concise, actionable steps.
- Prefer modern Tracy docs/examples and avoid outdated Firebug-era practices.
- Prefer modern AJAX debugging guidance (`fetch` + Tracy headers) over legacy tools.
- When suggesting code changes, explain why each config choice exists.
