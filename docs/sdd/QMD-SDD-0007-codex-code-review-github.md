# QMD-SDD-0007 — Codex Code Review no GitHub

Status: `rejected`
Prioridade: `media`
Area: `docs`

## Contexto

O repositorio Quazymodo precisa de uma camada adicional de revisao em pull
requests usando o Codex da OpenAI no GitHub. A funcionalidade e externa ao
codigo da aplicacao, mas impacta diretamente o fluxo de contribuicao, a triagem
de PRs e o papel do arquivo `AGENTS.md` como guia de revisao.

Segundo a documentacao oficial de integracao com GitHub, o recurso depende de
`Codex cloud` configurado para o repositorio, de ativacao explicita nas
configuracoes do Codex e do uso do gatilho `@codex review` para revisao manual
quando a automacao nao estiver habilitada.

## Estado atual brownfield

- Arquivos afetados:
  - `AGENTS.md`
  - `docs/sdd/ROADMAP.md`
  - `docs/sdd/STATE.md`
  - documentacao operacional futura do fluxo de contribuicao, se necessario
- Rotas afetadas:
  - nenhuma
- Componentes afetados:
  - nenhum
- Dados persistidos afetados:
  - nenhum dado da aplicacao
- Comportamento existente que deve ser preservado:
  - o fluxo atual de PRs continua funcional sem dependencia do Codex;
  - `AGENTS.md` continua sendo fonte primaria de contexto do repositorio;
  - a revisao humana continua sendo a decisao final de merge;
  - nada no core do Quazymodo deve mudar por causa desta integracao.

## Objetivo

Habilitar o Codex code review no GitHub para o repositorio Quazymodo,
permitindo revisao automatica de PRs e revisao sob demanda por comentario, sem
substituir a revisao humana.

## Fora de escopo

- auto-merge de pull requests;
- aprovacao automatica de mudancas;
- reescrita do fluxo de CI do repositorio;
- criacao de bot proprio de revisao;
- alteracoes no core do framework;
- integracoes com plataformas fora do GitHub;
- uso do Codex para aplicar correcoes automaticamente nesta spec.

## Proposta

Implementar o recorte como integracao de processo, seguindo o modelo oficial de
`Codex code review in GitHub`.

Regras do recorte:

1. o repositorio deve ter `Codex cloud` configurado antes da ativacao do review;
2. o code review deve ser habilitado nas configuracoes do Codex para o
   repositorio;
3. a revisao manual deve funcionar com comentario `@codex review` no PR;
4. revisao automatica em novos PRs pode ser habilitada separadamente;
5. o comportamento do review deve seguir as orientacoes registradas em
   `AGENTS.md`;
6. o foco inicial deve ser risco relevante de codigo, nao triagem exaustiva de
   estilo;
7. a decisao final de merge permanece humana.

## Contratos

### Entrada

- pull requests abertos no repositorio;
- comentario de disparo `@codex review` quando aplicavel;
- instrucoes do `AGENTS.md` do repositorio.

### Saida

- review padrao do GitHub publicado pelo Codex;
- comentarios focados em problemas de maior impacto;
- feedback legivel para mantenedores e autores do PR.

### Restricoes

- o Codex nao deve substituir aprovacao humana;
- o escopo inicial deve ficar restrito a revisao de PR no GitHub;
- permissoes devem ser minimas e compativeis com leitura do diff e publicacao
  de review;
- o fluxo nao deve alterar comportamento da aplicacao Quazymodo.

## Criterios de aceite

- [ ] o repositorio esta habilitado para `Codex code review`;
- [ ] o review manual por comentario `@codex review` funciona em PR de teste;
- [ ] a revisao automatica pode ser ativada ou desativada no repositorio;
- [ ] o Codex segue orientacoes de revisao presentes em `AGENTS.md`;
- [ ] a integracao nao altera rotas, componentes ou comportamento da aplicacao;
- [ ] a revisao humana continua sendo a decisao final de merge.

## Plano de migracao

1. confirmar elegibilidade e acesso ao `Codex cloud` para o repositorio;
2. revisar `AGENTS.md` para garantir orientacoes de revisao adequadas;
3. habilitar `Code review` nas configuracoes do Codex;
4. testar o gatilho `@codex review` em um PR controlado;
5. avaliar se `Automatic reviews` deve ficar ligado por padrao;
6. registrar validacao e desvios em `STATE.md` quando a ativacao ocorrer.

## Validacao

Passos minimos:

- abrir um PR de teste no repositorio;
- comentar `@codex review` no PR;
- confirmar reacao e publicacao do review pelo Codex;
- se `Automatic reviews` for habilitado, abrir novo PR e confirmar execucao
  automatica;
- verificar se o review respeita o `AGENTS.md` e mantem foco em achados de
  maior prioridade.

## Riscos

- Risco: revisao automatica gerar ruido excessivo em PRs pequenos.
- Mitigacao: manter orientacoes claras no `AGENTS.md` e avaliar ativacao de
  `Automatic reviews` com base no volume real de ruido.

- Risco: equipe interpretar o recurso como aprovador automatico.
- Mitigacao: documentar explicitamente que a decisao final continua humana.

- Risco: conflito entre regras locais do repositorio e heuristicas padrao do
  Codex.
- Mitigacao: ajustar secao de review no `AGENTS.md` antes da ativacao global.

## Decisoes

- A integracao sera tratada como funcionalidade de processo, nao de aplicacao.
- O escopo inicial cobre apenas revisao de pull requests no GitHub.
- `AGENTS.md` sera a principal referencia de orientacao para o review.
- A revisao humana continuara obrigatoria para decisao final.

## Notas de implementacao

- Base oficial consultada: documentacao `Codex code review in GitHub` da
  OpenAI.
- Esta spec cobre configuracao operacional externa ao repositorio e ajustes
  documentais minimos.
- Se a ativacao exigir convencoes novas de revisao, atualizar `AGENTS.md` em
  recorte controlado.

## Motivo da rejeicao

- A integracao com `Codex code review` no GitHub foi descartada para o
  repositorio neste momento.
- O fluxo de revisao continua sem dependencia dessa automacao externa.
