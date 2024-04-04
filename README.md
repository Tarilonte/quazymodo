# Blueprint

Um `blueprint` é um arquivo JSON que contém instruções para montar um componente.
Ele é interpretado pela classe 'Component' do quazyTemplater.

Um `blueprint` contém informações sobre o template a ser utilizado, os arquivos JavaScript e CSS associados, e os dados a serem utilizados.

## Nomeação e localização de um arquivo blueprint

O nome do arquivo `blueprint` deve ser o nome do componente que ele irá criar. Por exemplo, se o componente for `basic-form`, o arquivo blueprint deve ser chamado `basic-form.json`.

O arquivo blueprint deve ser colocado na pasta `components` na raiz do projeto.
Para fins de organização, o blueprint pode ser colocado em uma subpasta. Por exemplo, "`components/forms/basic-form.json`".

## Estrutura do Arquivo

O `blueprint` é um objeto JSON que contém as seguintes propriedades:

### `template`

- **Tipo**: `string`
- **Descrição**: Define o template a ser usado. Este é o único campo obrigatório.
- **Exemplo**: `"main-layout"`, `forms/basic-form.json`

### `js`

- **Tipo**: `string` | `array`
- **Descrição**: Especifica os arquivos JavaScript associados.
- **Exemplo**: `"app.js"` ou `["helper.js", "app.js"]`
- **Opcional**: Este campo é opcional.

### `css`

- **Tipo**: `string` | `array`
- **Descrição**: Indica os arquivos CSS.
- **Exemplo**: `"style.css"` ou `["reset.css", "style.css"]`
- **Opcional**: Este campo é opcional.

### `data`

- **Tipo**: `array`
- **Descrição**: Array de objetos, cada um representando um conjunto de dados.
- **Estrutura do Objeto**:
  - `data-slot` (`string`): Identificador do slot de dados.
  - `data-type` (`string`): Tipo do dado. Valores possíveis: "env-var", "session-var", "string" e "component".
  - `data-source` (`string`): Origem dos dados.
- **Opcional**: Este campo é opcional.

#### Valores da Propriedade `data-type`

- `string`: Indica uma String literal.
- `template`: Indica um Template espcífico.
- `component`: Indica um Componente específico.
- `env-var`: Indica uma variável de ambiente.
- `session-var`: Indica uma variável de sessão.
- `cookie`: Indica um Cookie.

## Exemplo

```json
{
  "template": "main-layout",
  "js": ["app.js"],
  "css": ["style.css"],
  "data": [
    {
      "data-slot": "title",
      "data-type": "string",
      "data-source": "My Application Title"
    },
    {
      "data-slot": "userSessionId",
      "data-type": "session-var",
      "data-source": "sessionId"
    },
    {
      "data-slot": "form-login",
      "data-type": "component",
      "data-source": "form-login"
    }
    // Outros objetos conforme necessário
  ]
}