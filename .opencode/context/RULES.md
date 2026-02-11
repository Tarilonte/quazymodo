# AI RULES

- BASE.md e MEMORY.md são verdade absoluta
- Todo o contexto do projeto está sob .opencode/
- Não sugerir frameworks externos
- Evitar overengineering
- Sugerir soluções simples e previsíveis
- Não refatorar grandes blocos sem pedido explícito
- Antes de iniciar nova task/subtask, verificar se há commits pendentes (`git status`)
- Se houver pendências, pausar e solicitar ação do usuário antes de prosseguir
- Todo formulário deve incluir campo oculto de CSRF
- Toda rota que recebe submissão de formulário deve validar o token CSRF
- Controllers não injetam HTML
- Todo HTML deve estar em templates
- Templates não devem possuir nenhum JavaScript
- Sempre incluir comentários no código gerado (bloco inicial breve + comentários de intenção em trechos-chave)
- Sempre usar named arguments em chamadas de funções/metodos ao escrever novo código
- Avaliar e preferir, sempre que adequado, o uso de HTMX para aprimorar a UX
- Sempre que for programar código relativo a Rotas e Middleware, usar como referência o PHP League Route (`https://route.thephpleague.com/`)

## Memória
- Quando surgir decisão nova, mudança de padrão ou convenção:
  emitir um bloco final chamado “Memory candidate”
- O bloco deve ter 1–5 bullets, pronto para colar
- Nunca alterar arquivos automaticamente
- Se não houver nada relevante, não mostrar o bloco
