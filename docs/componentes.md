# Manual Completo de Uso de Componentes — Quazymodo

## 1) Visao geral

No Quazymodo, componentes sao unidades de UI autonomas e completas.
Cada componente reune:

- estrutura (`html`)
- logica propria (`js`)
- estilos proprios (`css`)

Isso significa que, ao ser utilizado, o componente ja possui tudo o que precisa para funcionar.

Esse modelo oferece:

- reutilizacao real
- uniformidade visual e comportamental
- manutencao mais simples
- composicao de interfaces complexas sem adicionar complexidade arquitetural
- HTML puro, sem dependencia de templating engine externa

## 2) Objetivo deste manual

Este manual define:

- os conceitos fundamentais de componentes no Quazymodo
- as regras oficiais de uso
- o fluxo recomendado de construcao
- os padroes de composicao/heranca
- as boas praticas e erros comuns

Ele deve ser usado como referencia para criacao de novos componentes e revisao de implementacoes existentes.

## 3) Conceitos fundamentais

- **Componente**: unidade reutilizavel de interface.
- **Template (`.html`)**: estrutura visual com slots (`{{ slot }}`).
- **Blueprint (`.blueprint.php`)**: contrato de montagem do componente (template, inserts, assets, heranca).
- **Slot**: ponto nomeado no template para receber conteudo.
- **Insert**: conteudo enviado para um slot.
- **Composicao**: um componente usando outros componentes para montar UI.
- **Heranca (`extends`)**: blueprint filho aproveita e complementa blueprint pai.
- **Assets do componente**: CSS/JS declarados no blueprint e injetados no render.

## 4) Filosofia de arquitetura

- Codigo explicito > abstracao excessiva.
- Componentes pequenos e compostos > templates grandes e monoliticos.
- Composicao deve simplificar, nao esconder comportamento.
- Regras de insercao devem ser previsiveis e deterministicas.
- Falhas de contrato (ex.: slot invalido) devem falhar de forma explicita.

## 5) Tipos de componente (Factory)

### `ComponentFactory::Page(...)`

Uso para paginas completas.
Aplica fluxo de pagina do framework (inclui comportamento de seguranca/cabecalhos do tipo page no pipeline).

### `ComponentFactory::Plugin(...)`

Uso para componentes com blueprint.
E o modo padrao para blocos reutilizaveis.

### `ComponentFactory::Template(...)`

Uso para template puro (sem blueprint).
Indicado apenas quando voce precisa renderizar um HTML direto.

**Regra importante**: se existe blueprint, prefira `Plugin`, nao `Template`.

## 6) Estrutura recomendada de arquivos

Para um componente com blueprint:

- `.../nomeComponente.html`
- `.../nomeComponente.blueprint.php`
- (opcional) arquivos CSS/JS do componente

Para templates auxiliares internos (ex.: linhas de tabela), pode existir mais de um HTML no mesmo diretorio.

## 7) Contrato de slots e inserts

### Slots

- Definidos no HTML com `{{ slot }}`.
- Representam pontos de insercao oficiais da estrutura.

### Inserts

- Declarados no blueprint e/ou enviados via controller/runtime.
- Podem conter texto, HTML, arrays de itens e ate outros componentes.

### Validacao

- Declaracoes de slot inexistente geram excecao.
- A validacao considera:
  - template do componente
  - templates usados por composicao/heranca
  - chaves consumidas internamente via `$inserts[...]` no blueprint (parametros internos)

Isso evita falso positivo em componentes "montadores" que usam dados intermediarios.

## 8) Operacoes de insert (marcadores)

O Quazymodo suporta operacoes explicitas no nome do slot:

- `slot@append`
- `slot@prepend`
- `slot@replace`

Sem marcador, o padrao e `append`.

### Semantica

- **append**: adiciona ao final do slot
- **prepend**: adiciona no inicio do slot
- **replace**: substitui o conteudo do slot naquele ponto do fluxo

As operacoes sao aplicadas em ordem de declaracao.

## 9) Heranca de blueprint (`extends`)

Quando um blueprint estende outro:

- `css/js` sao cumulativos (pai + filho), com normalizacao e deduplicacao.
- `inserts` sao combinados de forma previsivel por slot.
- Nao ha merge recursivo imprevisivel de arrays.

Objetivo: comportamento deterministico e legivel no tempo.

## 10) Assets (CSS/JS)

- Assets podem ser declarados no blueprint.
- Caminhos relativos do componente sao resolvidos no pipeline.
- Assets de componentes compostos sobem para o componente final renderizado.
- O sistema evita duplicidade de assets no resultado final.

## 11) Fluxo de render (conceitual)

1. Factory instancia componente.
2. Blueprint e carregado (se houver).
3. Template e carregado.
4. Slots do template sao mapeados.
5. Inserts sao consolidados e operacoes aplicadas.
6. Slots sao preenchidos.
7. Assets sao coletados/injetados.
8. Render final e retornado.

## 12) Cache por request (estado atual)

Implementado:

- cache de leitura de template
- cache de mapeamento de slots por regex

Rejeitado (por decisao de escopo):

- cache de `filemtime` para versionamento de JS

Observabilidade:

- painel de componentes no Tracy exibe `cache hits` agregados por componente.

## 13) Excecoes e falhas explicitas

- Erros de contrato de slot usam excecao de dominio: `SlotNotFoundException`.
- Excecoes do framework seguem namespace dedicado `Quazymodo\Exceptions`.
- Uma classe por arquivo para manter rastreabilidade e manutencao.

## 14) Passo a passo para criar um componente novo

1. Defina o objetivo do componente (responsabilidade unica).
2. Crie o template HTML com slots claros.
3. Crie blueprint com:
   - template alvo
   - inserts padrao
   - assets necessarios
4. Faca composicao com outros componentes se necessario.
5. Use `Plugin` na instanciacao.
6. Teste cenario normal e cenarios de override (`append/prepend/replace`).
7. Verifique render final, assets e consistencia visual.

## 15) Exemplos conceituais de uso

### Exemplo A — Pagina simples

- Pagina com slot `body`.
- Controller injeta conteudo no `body`.
- Render limpo e previsivel.

### Exemplo B — Composicao de plugin

- Pagina injeta componente de tabela no `body`.
- Componente de tabela monta subestruturas internas.
- Resultado final mantem isolamento de responsabilidades.

### Exemplo C — Controle de ordem

- `body@prepend` para cabecalho contextual.
- `body` para conteudo principal.
- `body@append` para complemento final.

### Exemplo D — Sobrescrita explicita

- Blueprint fornece valor padrao.
- Cenario especifico usa `slot@replace` para substituir integralmente.

## 16) Boas praticas

- Nomeie slots de forma explicita e estavel.
- Evite "slot generico demais" sem contexto.
- Mantenha blueprint curto e orientado a composicao.
- Prefira parametros claros para personalizacao.
- Reutilize componentes antes de duplicar estrutura.
- Use heranca quando houver padrao comum real.
- Preserve HTML simples e legivel.

## 17) Anti-padroes a evitar

- Instanciar com `Template` quando o componente depende de blueprint.
- Criar componentes "tudo em um".
- Misturar regras de negocio complexas na camada de template.
- Usar muitos slots implicitos sem contrato documentado.
- Dependencia oculta entre componentes sem composicao explicita.

## 18) Checklist de revisao antes de subir

- O tipo de factory esta correto?
- Os slots usados existem no escopo valido?
- As operacoes de insert estao intencionais?
- O componente continua autonomo (html/css/js)?
- Assets nao estao duplicados?
- Composicao esta clara para quem manter depois?
- Ha risco de acoplamento indevido?

## 19) Convencao de manutencao do projeto

- Mensagens de commit devem ser claras para humanos e code agents.
- Template recomendado:
  - `tipo(escopo): resumo claro no imperativo`
  - corpo opcional com `Contexto`, `Mudanca`, `Impacto`

## 20) Resumo final

O modelo de componentes do Quazymodo existe para entregar:

- autonomia
- previsibilidade
- composicao simples
- reaproveitamento real
- HTML puro sem engine externa

Se uma decisao aumentar acoplamento, reduzir clareza ou esconder regra de montagem, ela vai contra o proposito do sistema.
