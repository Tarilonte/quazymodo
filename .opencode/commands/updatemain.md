---
description: Atualizar main com commits de lab via cherry-pick seguro (ate merge final)
---
Objetivo: sincronizar implementacoes de `lab` para `main` sem duplicar mudancas equivalentes e sem sobrescrever historico.

Regras obrigatorias:
- Antes de iniciar, rode `git status --short --branch`.
- Se houver qualquer pendencia local (staged/unstaged/untracked), pause e solicite acao do usuario para limpar/stash/commit antes de continuar.
- Nunca usar `git reset --hard`, `git push --force`, `--no-verify` ou `git commit --amend`.
- Nao incluir segredos/arquivos sensiveis (`*.key`, `*.crt`, `.env*`, `credentials*`, `secrets*`).

Execucao:
1) Diagnostico inicial (em paralelo quando possivel):
- `git rev-list --left-right --count main...lab`
- `git log --left-right --cherry-pick --oneline main...lab`
- `git cherry -v main lab`
- `git diff --name-status main..lab`
- Monte a lista inicial de commits de `lab` candidatos a cherry-pick.

2) Eliminar falsos positivos de equivalencia:
- Para cada commit candidato em `lab`, obtenha arquivos alterados com `git show --name-only --pretty="" <sha>`.
- Se `git diff --name-only main..lab -- <arquivos-do-commit>` vier vazio, marque o commit como ja implementado em `main` e remova da lista.
- Explique brevemente cada commit pulado por equivalencia funcional.

3) Criar branch de integracao:
- `git switch main`
- `git switch -c chore/updatemain-<YYYYMMDD-HHMM>`

4) Aplicar commits:
- Aplique apenas os SHAs finais, em ordem cronologica, com `git cherry-pick -x <sha>`.
- Se houver conflito:
  - resolva preservando o comportamento atual de `main` quando houver ambiguidade,
  - `git add <arquivos>`,
  - `git cherry-pick --continue`.

5) Validar branch de integracao:
- `git status --short --branch`
- `git diff --name-status main..HEAD`
- Rode a validacao disponivel para os arquivos alterados (`php -l` nos PHP afetados e checagens locais aplicaveis ao escopo)
- Se houver falha, corrija sem reescrever historico.

6) Integrar ate o fim (obrigatorio):
- `git switch main`
- `git merge --ff-only <branch-de-integracao>`
- `git branch -d <branch-de-integracao>`

7) Entrega:
- Informe claramente:
  - commits aplicados,
  - commits pulados por equivalencia,
  - conflitos e como foram resolvidos,
  - arquivos sensiveis detectados/excluidos,
  - branch de integracao removida com sucesso.
