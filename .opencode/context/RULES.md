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

## Memória
- Quando surgir decisão nova, mudança de padrão ou convenção:
  emitir um bloco final chamado “Memory candidate”
- O bloco deve ter 1–5 bullets, pronto para colar
- Nunca alterar arquivos automaticamente
- Se não houver nada relevante, não mostrar o bloco
