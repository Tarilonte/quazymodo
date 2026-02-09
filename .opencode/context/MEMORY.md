# PROJECT MEMORY

- [2026-02] Estrutura de memória do projeto separada em BASE, RULES, MEMORY e TASKS
- [2026-02] IA deve sempre sinalizar decisões persistíveis via “Memory candidate”
- [2026-02] Frontend utiliza a extensão DaisyUI do TailwindCSS
- [2026-02] Ao instanciar componentes com blueprint, usar ComponentFactory::Plugin('/plugins/.../', ...) e não Template (que não carrega blueprint)
- [2026-02] Merge de Blueprint em extends: css/js são sempre cumulativos (normalização string->array + concatenação + deduplicação), sem uso de array_merge_recursive
- [2026-02] Marcadores de inserts (`slot@append`, `slot@prepend`, `slot@replace`) são processados somente em ComponentData; sem marcador = append
- [2026-02] Validação de slots considera árvore de templates usada por composição/herança e também chaves de entrada consumidas via $inserts no blueprint
- [2026-02] Exceções do Quazymodo seguem namespace dedicado `Quazymodo\\Exceptions` com uma classe por arquivo (ex.: `Exceptions/SlotNotFoundException.php`)
- [2026-02] Painel Tracy de componentes inclui coluna agregada de `cache hits` por componente, alimentada por contadores em `BaseComponent`
- [2026-02] Mensagens de commit devem ser claras e úteis para referência futura, com contexto técnico suficiente para humanos e code agents
- [2026-02] Template de commit recomendado: título `tipo(escopo): resumo claro no imperativo`
- [2026-02] Corpo de commit (quando necessário): `Contexto` (problema/objetivo), `Mudança` (o que foi implementado), `Impacto` (efeitos/compatibilidade)
