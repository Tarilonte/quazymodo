# QMD-SDD-0002 — Environment Selector

Status: `implemented-pending-smoke`
Prioridade: `media`
Area: `componentes|seguranca|http|frontend`

## Contexto

O Quazymodo usa um ambiente efetivo para decidir comportamento de runtime, como
diagnostico em desenvolvimento e respostas amigaveis em producao. Hoje o
ambiente principal e representado por `APP_ENV`, constante definida durante o
bootstrap da aplicacao.

Como `APP_ENV` e constante, uma troca feita durante uma requisicao nao deve
tentar redefinir a constante ja criada. O novo comportamento precisa registrar um
override temporario por sessao, lido antes da definicao do ambiente efetivo nas
proximas requisicoes.

O recurso deve facilitar a alternancia manual entre `development` e `production`
sem persistir configuracao permanente e sem esconder o controle em producao.

## Estado atual brownfield

Arquivos afetados esperados:

- `app/config/index.php` ou bootstrap equivalente que define o ambiente efetivo.
- `app/routes/index.php` ou arquivo modular de rotas equivalente.
- `app/controllers/*` para o endpoint de submissao do ambiente temporario.
- `app/components/plugins/environmentSelector/*` para o novo plugin.
- Template do navbar que recebera o componente por slot pre-preenchido.
- `app/components/ComponentShortcuts.php`, caso o shortcut seja criado.

Rotas afetadas:

- Criar uma rota de submissao para alternar o ambiente temporario da sessao.
- A rota deve aceitar somente valores explicitamente permitidos.
- A rota deve validar CSRF antes de alterar qualquer valor de sessao.

Componentes afetados:

- Criar componente plugin `environmentSelector`.
- Incluir o plugin no navbar por slot pre-preenchido.
- Avaliar criacao de shortcut em `App\Components\ComponentShortcuts`.

Dados persistidos afetados:

- Nenhum dado persistido em banco ou arquivo deve ser criado.
- O override deve ser temporario por sessao.
- A troca nao deve alterar `.env`, arquivos de configuracao, banco de dados ou
  qualquer fonte persistente.

Comportamento existente que deve ser preservado:

- `development` e `production` continuam sendo os unicos ambientes aceitos.
- A aplicacao continua usando o ambiente definido no bootstrap quando nao houver
  override temporario valido.
- Controllers continuam retornando respostas PSR-7 via helpers existentes.
- Todo HTML continua em templates de componentes.
- Templates nao passam a conter JavaScript.

## Objetivo

Criar um plugin `environmentSelector` exibido no navbar em qualquer ambiente,
permitindo alternar temporariamente a sessao atual entre `development` e
`production` por HTMX, com validacao CSRF e recarregamento da pagina atual apos
mensagem de sucesso.

## Fora de escopo

- Persistir a escolha do ambiente fora da sessao.
- Criar novo sistema de configuracao de ambiente.
- Aceitar ambientes diferentes de `development` e `production`.
- Alterar o contrato publico de `APP_ENV` para codigo que apenas le a constante.
- Criar JavaScript dentro de templates.
- Fazer controller retornar HTML montado manualmente.
- Reescrever navbar, sistema de componentes ou fluxo geral de bootstrap.
- Implementar uma politica geral de CSRF para todo o projeto.

## Proposta

Adicionar um plugin `environmentSelector` composto por blueprint e template. A
UI deve usar o toggle do daisyUI, com estado ligado representando `production` e
estado desligado representando `development`.

O componente deve ser renderizado em qualquer ambiente. Em `production`, o toggle
continua visivel e deve permitir voltar para `development`.

O template do componente deve conter o formulario ou controle necessario para a
submissao HTMX, incluindo campo oculto de CSRF. A interacao deve disparar uma
requisicao HTMX ao backend quando o usuario alternar o toggle.

O backend deve validar o token CSRF, validar o ambiente solicitado contra a lista
fechada `development` e `production`, gravar somente o override temporario na
sessao e responder com mensagem de sucesso.

A troca passa a valer somente na proxima requisicao, porque o ambiente efetivo da
requisicao atual ja foi resolvido no bootstrap. Apos receber a mensagem de
sucesso, a pagina atual deve ser atualizada para que a proxima requisicao ja use
o ambiente temporario.

O bootstrap deve resolver o ambiente efetivo lendo primeiro o override temporario
da sessao, antes de definir `APP_ENV` ou qualquer valor equivalente consumido no
runtime. Se o override estiver ausente ou invalido, deve usar o ambiente original
configurado para a aplicacao.

## Contratos

### Plugin `environmentSelector`

- Tipo: plugin de componente.
- UI: toggle do daisyUI.
- Estado ligado: `production`.
- Estado desligado: `development`.
- Visibilidade: sempre renderizado, incluindo em `production`.
- Inclusao: slot pre-preenchido no template do navbar.

### Submissao HTMX

- A submissao deve usar HTMX.
- A requisicao deve enviar o ambiente desejado e o token CSRF.
- O backend deve responder com uma mensagem de sucesso quando a troca for aceita.
- A pagina atual deve ser recarregada somente apos sucesso, para aplicar o
  ambiente temporario na proxima requisicao.

### Backend

- A rota de submissao deve validar CSRF.
- A rota deve rejeitar qualquer valor diferente de `development` e `production`.
- O override deve ser gravado somente na sessao.
- A resposta nao deve incluir HTML injetado pelo controller.
- Erros de CSRF ou ambiente invalido devem retornar resposta rejeitada e nao
  alterar a sessao.

### Shortcut

- A implementacao deve avaliar a criacao de shortcut em
  `App\Components\ComponentShortcuts`.
- Se o componente for usado somente no navbar e a equipe optar por nao criar
  shortcut, a justificativa deve ser registrada na implementacao ou na revisao da
  spec.

## Criterios de aceite

- [ ] Existe spec aceita antes de qualquer codigo de aplicacao ser escrito.
- [ ] O plugin `environmentSelector` e criado como componente plugin.
- [ ] A UI usa toggle do daisyUI.
- [ ] Toggle ligado representa `production`.
- [ ] Toggle desligado representa `development`.
- [ ] O componente aparece em `development` e `production`.
- [ ] Em `production`, o componente permite voltar para `development`.
- [ ] A troca de ambiente nao e persistente fora da sessao.
- [ ] A troca passa a valer somente na proxima requisicao.
- [ ] O override temporario e lido antes de definir o ambiente efetivo.
- [ ] Somente `development` e `production` sao aceitos.
- [ ] O componente e incluido no navbar por slot pre-preenchido.
- [ ] A submissao usa HTMX.
- [ ] A rota de submissao valida CSRF.
- [ ] O backend responde com mensagem de sucesso quando a troca e aceita.
- [ ] A pagina atual recarrega apos sucesso para usar o ambiente atualizado.
- [ ] Todo HTML fica em templates.
- [ ] Templates nao contem JavaScript.
- [ ] Controller nao injeta HTML.
- [ ] A criacao de shortcut em `App\Components\ComponentShortcuts` foi avaliada.

## Plano de migracao

1. Identificar o ponto de bootstrap que define `APP_ENV`.
2. Inserir a leitura segura do override temporario antes da definicao do ambiente
   efetivo.
3. Criar a rota de submissao com validacao CSRF e validacao fechada de ambiente.
4. Criar o plugin `environmentSelector` com template usando daisyUI e HTMX.
5. Incluir o plugin no navbar por slot pre-preenchido.
6. Avaliar e criar shortcut se houver ganho de clareza ou reuso.
7. Validar manualmente os fluxos de alternancia e rejeicao.

## Validacao

Inspecoes manuais esperadas:

- Renderizar uma pagina com navbar e confirmar que o seletor aparece.
- Em `development`, alternar para `production` e confirmar mensagem de sucesso.
- Confirmar que a pagina recarrega apos sucesso.
- Confirmar que a requisicao seguinte usa o ambiente temporario `production`.
- Em `production`, alternar para `development` e confirmar mensagem de sucesso.
- Confirmar que a requisicao seguinte usa o ambiente temporario `development`.
- Enviar submissao com CSRF invalido e confirmar rejeicao sem alterar a sessao.
- Enviar valor de ambiente invalido e confirmar rejeicao sem alterar a sessao.
- Confirmar que nenhum dado persistente foi alterado.
- Confirmar que templates nao possuem JavaScript e controllers nao montam HTML.

Validacao assistida recomendada:

- Usar Playwright para acompanhar renderizacao do navbar.
- Usar Playwright para alternar `development` para `production`.
- Usar Playwright para alternar `production` para `development`.
- Usar Playwright para confirmar o reload apos mensagem de sucesso.

Validacao tecnica esperada apos implementacao:

- Verificar a sintaxe dos arquivos PHP alterados.
- Executar smoke manual no navegador para os fluxos acima.

## Riscos

- Risco: tentar redefinir `APP_ENV` depois que a constante ja foi criada.
- Mitigacao: resolver o override temporario antes da definicao do ambiente
  efetivo e tratar a troca como valida apenas para a proxima requisicao.

- Risco: permitir ambiente arbitrario por entrada do usuario.
- Mitigacao: validar contra lista fechada com `development` e `production`.

- Risco: alternancia em producao ficar inacessivel e impedir retorno para
  desenvolvimento na sessao.
- Mitigacao: renderizar o componente em qualquer ambiente.

- Risco: endpoint de troca virar superficie de CSRF.
- Mitigacao: exigir token CSRF valido antes de gravar o override de sessao.

- Risco: resposta HTMX introduzir HTML gerado por controller.
- Mitigacao: controller deve responder com mensagem de sucesso sem montar HTML de
  componente manualmente; HTML visual permanece em templates.

## Decisoes

- O toggle ligado representa `production` e desligado representa `development`.
- O seletor aparece em qualquer ambiente.
- A troca e temporaria por sessao e nao persistente.
- A troca passa a valer na proxima requisicao.
- O override temporario deve ser lido antes de definir o ambiente efetivo.
- `development` e `production` sao os unicos valores aceitos.
- HTMX sera usado para submissao e o reload ocorre apos sucesso.
- O componente sera incluido no navbar por slot pre-preenchido.

## Notas de implementacao

- Consultar `docs/htmx.md` e os topicos indicados de atributos, respostas e
  seguranca antes de codar a submissao.
- Consultar `docs/daisyui.md` e o componente Toggle antes de codar a UI.
- Consultar `docs/tailwindcss.md` para manter classes utilitarias no padrao do
  projeto.
- Consultar `docs/league-route.md` para definir a rota e o controller.
- Preservar a regra de que todo formulario deve incluir CSRF e toda rota de
  submissao deve validar CSRF.
- Preservar a regra de que templates nao contem JavaScript.
- Preservar a regra de que controllers nao injetam HTML.
