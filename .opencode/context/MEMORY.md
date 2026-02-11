# PROJECT MEMORY

<!--
Finalidade: registrar decisoes tecnicas tomadas no projeto e seus motivos.
Este arquivo NAO define regras; ele preserva contexto historico para continuidade.
-->

- [2026-02] Decisao: separar memoria operacional em BASE, RULES, MEMORY e TASKS. Motivo: reduzir mistura de contexto estavel, regras de execucao e tarefas ativas.
- [2026-02] Decisao: frontend padronizado com TailwindCSS + daisyUI. Motivo: acelerar UI utilitaria com consistencia visual.
- [2026-02] Decisao: ao instanciar componentes com blueprint, usar `ComponentFactory::Plugin('/plugins/.../', ...)` e nao `Template`. Motivo: `Template` nao carrega blueprint.
- [2026-02] Decisao: merge de blueprint em `extends` com `css/js` cumulativos (normalizacao string->array + concat + deduplicacao), sem `array_merge_recursive`. Motivo: evitar estruturas aninhadas incorretas e manter previsibilidade.
- [2026-02] Decisao: marcadores de inserts (`slot@append`, `slot@prepend`, `slot@replace`) processados somente em `ComponentData`; sem marcador = append. Motivo: centralizar regra de composicao em uma unica camada.
- [2026-02] Decisao: validacao de slots considera arvore completa de templates e chaves consumidas via `$inserts`. Motivo: reduzir falso positivo em composicoes/heranca.
- [2026-02] Decisao: excecoes do core em namespace dedicado `Quazymodo\\Exceptions`, uma classe por arquivo. Motivo: melhorar rastreabilidade de falhas por dominio.
- [2026-02] Decisao: painel Tracy de componentes exibe `cache hits` agregados por componente via contadores de `BaseComponent`. Motivo: facilitar diagnostico de desempenho de render.
- [2026-02] Decisao: padrao de commit adotado com titulo `tipo(escopo): resumo claro no imperativo` e, quando necessario, corpo `Contexto/Mudanca/Impacto`. Motivo: preservar historico util para humanos e code agents.
- [2026-02] Decisao: falhas de template/blueprint inexistente usam excecoes de dominio (`TemplateNotFoundException`, `BlueprintNotFoundException`) no lugar de `die()`. Motivo: manter fluxo de erro consistente e tratavel.
- [2026-02] Decisao: `CSPManager::addSource` passou a lancar `InvalidCspDirectiveException` em diretiva invalida, sem escrever no body. Motivo: evitar efeitos colaterais em resposta e padronizar erro de dominio.
