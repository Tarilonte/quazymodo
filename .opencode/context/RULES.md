# AI RULES

<!--
Finalidade: definir regras operacionais obrigatorias para execucao das tasks.
Este arquivo usa linguagem normativa (DEVE, NAO DEVE, SEMPRE, NUNCA).
-->

## 1) Hierarquia e escopo
- O agente DEVE tratar `BASE.md` e `MEMORY.md` como fonte primaria de contexto do projeto.
- O agente DEVE usar todo o contexto do projeto sob `.opencode/`.
- O agente NAO DEVE contradizer decisoes ja registradas em `BASE.md` e `MEMORY.md`.

## 2) Principios de implementacao
- O agente DEVE sugerir solucoes simples e previsiveis.
- O agente NAO DEVE fazer overengineering.
- O agente NAO DEVE sugerir frameworks externos.
- O agente NAO DEVE refatorar blocos grandes sem pedido explicito.

## 3) Fluxo de git e branches
- O agente DEVE SEMPRE verificar pendencias com `git status` antes de iniciar nova task/subtask.
- Se houver pendencias, o agente DEVE pausar e solicitar acao do usuario antes de prosseguir.
- O agente NUNCA DEVE fazer merge completo da branch `lab` para `main`.
- O agente DEVE SEMPRE promover mudancas de `lab` para `main` via selecao de commits (ex.: cherry-pick).

## 4) Seguranca e regras web
- Todo formulario DEVE incluir campo oculto de CSRF.
- Toda rota que recebe submissao de formulario DEVE validar token CSRF.
- Controllers NAO DEVEM injetar HTML.
- Todo HTML DEVE estar em templates.
- Templates NUNCA DEVEM possuir JavaScript.

## 5) Regras de codigo
- O agente DEVE SEMPRE incluir comentarios no codigo gerado (bloco inicial breve + comentarios de intencao em trechos-chave).
- O agente DEVE SEMPRE usar named arguments em chamadas de funcoes/metodos ao escrever novo codigo.
- O agente DEVE avaliar e preferir o uso de HTMX quando adequado para UX.
- Ao implementar Rotas e Middleware, o agente DEVE usar como referencia o PHP League Route (`https://route.thephpleague.com/`).

## 6) Regras de componentes e shortcuts
- Ao criar novo componente, o agente DEVE sugerir criacao de shortcut.
- Ao sugerir shortcut, o agente DEVE propor assinatura com typed hints e named arguments.
- O agente DEVE preferir centralizar shortcuts em `App\Components\ComponentShortcuts`.
- Se o agente optar por nao criar shortcut para componente novo, DEVE registrar justificativa curta (ex.: uso unico/baixo reuso).

## 7) Regras de commit
- O agente NUNCA DEVE usar mensagens de commit genericas (`ajuste`, `update`, `fixes`, `wip`, `misc` e similares).
- Todo commit DEVE seguir o formato `tipo(escopo): resumo claro no imperativo`.
- O `escopo` DEVE refletir o dominio/componente real alterado.
- Mudancas nao relacionadas NAO DEVEM ser agrupadas no mesmo commit.
- Quando houver artefato gerado (ex.: build CSS), isso DEVE ser indicado no titulo ou no corpo do commit.

## 8) Memoria operacional
- Quando surgir decisao nova, mudanca de padrao ou convencao, o agente DEVE emitir bloco final chamado `Memory candidate`.
- O bloco `Memory candidate` DEVE ter de 1 a 5 bullets e estar pronto para colar.
- O agente NUNCA DEVE alterar automaticamente os arquivos de memoria (`BASE.md`, `RULES.md`, `MEMORY.md`, `TASKS.md`) sem pedido explicito.
- Se nao houver decisao persistivel relevante, o agente NAO DEVE exibir `Memory candidate`.
- Ao concluir mudancas relevantes, o agente DEVE avaliar se AGENTS.md, BASE.md, RULES.md e MEMORY.md precisam de atualizacao.
- Quando identificar necessidade, o agente DEVE SEMPRE sugerir a atualizacao ao usuario com justificativa curta (o que mudou e por que atualizar).

## 9) Consulta obrigatoria de documentacao tecnica
- Sempre que a task envolver uma tecnologia documentada em `docs/*.md`, o agente DEVE consultar o arquivo correspondente antes de codar.
- O agente DEVE usar o indice como entrada e aprofundar apenas nos links de topicos/fontes relevantes para a task.
- Em respostas tecnicas, o agente DEVE citar explicitamente qual arquivo `docs/*.md` foi utilizado.
- Se houver divergencia entre suposicao do agente e o documento tecnico local, o agente DEVE priorizar `docs/*.md` e sinalizar a divergencia.
- Mapeamento minimo de consulta:
  - RedBean/ORM/persistencia (`R::`, repositories, migrations) -> `docs/redbean.md`
  - HTMX (`hx-*`, interacoes parciais) -> `docs/htmx.md`
  - Rotas/middleware/controllers com League Route -> `docs/league-route.md`
  - Debug/log/paineis com Tracy -> `docs/tracy.md`
  - jQuery (ajax/eventos/selectors/manipulacao) -> `docs/jquery.md`
  - Tailwind/daisyUI (utilitarios, temas, responsivo) -> `docs/tailwind-daisyui.md`
