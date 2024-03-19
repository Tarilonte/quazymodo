<?php

namespace pangaTemplater;
use function pangaFunctions\recursiveArraySearch;
use function pangaFunctions\show;

class Component
{
  public Blueprint $blueprint;
  public Data $data;
  public string $componentType;
  public string $componentName;
  public string $html = "";
  public array $js = [];
  public array $css = [];
  public array $slots = [];
  public string $assetsURL = "/assets";
  public static array $allData = [];

  public function __construct($componentName, $controllerData = [], $componentType = "component")
  {
    $this->componentName = $componentName;
    $this->componentType = $componentType; 
    if ($componentType === "htmlOnly") {
      $this->construct_htmlOnly($componentName, $controllerData);
    } else {
      $this->blueprint = new \pangaTemplater\Blueprint($componentName);
      if (null !== $this->blueprint->type) {
        $this->componentType = $this->blueprint->type;
      }        
      $this->html = $this->load_template($this->blueprint->array()['template']);
      $this->slots = $this->map_slots($this->html);
      $this->insert_componentName($componentName);
      $this->data = new \pangaTemplater\Data($this->blueprint->data, $controllerData);
      foreach($this->data->final_data as $key => $value) {
        self::$allData[$key] = $value;
      }
      is_array($this->blueprint->css)? $this->add_asset('css', $this->blueprint->css) : '';
      is_array($this->blueprint->js)? $this->add_asset('js', $this->blueprint->js) : '';
      is_array($this->data->css)? $this->add_asset('css', $this->data->css) : '';
      is_array($this->data->js)? $this->add_asset('js', $this->data->js) : '';
      $this->fill_slots();
      CSPManager::sendCSPHeader();
    }
    return $this;
  }

  private function construct_htmlOnly($templateName, $controllerData = [])
  {    
    $this->html = $this->load_template($templateName);
    $this->slots = $this->map_slots($this->html);    
    $this->insert_componentName($templateName . '_htmlOnly');
    $this->data = new \pangaTemplater\Data([], $controllerData);
    foreach($this->data->final_data as $key => $value) {
      self::$allData[$key] = $value;
    }
    is_array($this->data->css)? $this->add_asset('css', $this->data->css) : '';
    is_array($this->data->js)? $this->add_asset('js', $this->data->js) : '';
    $this->fill_slots();
  }

  public function __get($name)
  {
      return "Atributo '{$name}' não existe.";
  }

  public function add_asset(string $assetName, array $asset)
  {
    foreach ($asset as $value) {
      $this->$assetName[] = $value;
    }
    $this->$assetName = array_unique($this->$assetName);
  }

  private function load_template($templateName)
  {
    $template = file_get_contents("../components/templates/$templateName.html");
    $template = str_replace('[{', '{{', $template);
      $template = str_replace('}]', '}}', $template);
    return $template;
  }

  private function map_slots($html) : array
  {
    preg_match_all('/{{ ?([^ ]*?) ?}}/i', $html, $matches);
    return $matches[1];
  }

  private function insert_componentName($componentName) : void
  {
    $insertion = ' component-name="'. $componentName . '" ';
    $pattern = '/(<\w+\s*)(.*?)(>)/im';
    $replacement = '${1}' . $insertion . '${2}${3}';
    $this->componentName = $componentName;
    $this->html = preg_replace($pattern, $replacement, $this->html, 1);
  }

  private function fill_slots()
  {
    // Preenche os slots do html com os conteúdos definitivos
    foreach ($this->slots as $slot) {
      if(isset(self::$allData[$slot])) {
        $content = implode(PHP_EOL, self::$allData[$slot]);
        $this->html = preg_replace('/{{ ?' . $slot . ' ?}}/', $content . "{{ $slot }}", $this->html);
        unset(self::$allData[$slot]);
      }
    }
    
    // Se o componente for do tipo 'page', decarrega os assets no html
    if ($this->componentType == 'page') {
      $this->flush_css();
      $this->flush_js();
    }
  }

  public function render() : Component
  {
    $void_slots = $this->map_slots($this->html);
    foreach ($void_slots as $slot) {
      $this->html = preg_replace('/{{ ?' . $slot . ' ?}}/', isset(self::$allData[$slot]) ? implode(PHP_EOL, self::$allData[$slot]) : "", $this->html);
      unset(self::$allData[$slot]);
    }
    if ($this->map_slots($this->html)) {
      $this->render();
    }
    return $this;
  }

  private function flush_css()
  {
    $cssLinks = '';
    foreach ($this->css as $file) {
        // Verifica se o arquivo é um link externo (começa com 'http://' ou 'https://')
        if (strpos($file, 'http://') === 0 || strpos($file, 'https://') === 0) {
            $href = $file;
        } else {
            // Caso contrário, é um arquivo interno e adiciona o caminho 'assets/css/'
            $href = $this->assetsURL . "/css/$file";
        }
        $cssLinks .= '<link rel="stylesheet" type="text/css" href="' . $href . '">' . PHP_EOL;
    }

    $this->html = preg_replace('/{{ ?CSS ?}}/i', $cssLinks, $this->html);
  }

  private function flush_js() {
    $jsLinks = '';
    foreach ($this->js as $file) {
        // Verifica se o arquivo é um link externo (começa com 'http://' ou 'https://')
        if (strpos($file, 'http://') === 0 || strpos($file, 'https://') === 0) {
            $string = $file;
            $externo = true;
        } else {
            // Caso contrário, é um arquivo interno e adiciona o caminho 'assets/js/'
            $string = $this->assetsURL . "/js/$file";
            $externo = false;
        }
        // Extrai os atributos do script - Exemplo: [defer]
        list($src, $attributes) = $this->get_jsAttributes($string);
        // Adiciona o script de fonte externa à lista de fontes de script autorizados pela CSP
        if ($externo) {
          CSPManager::addSource('script-src', $src);
        }
        // Adiciona o script à variável $jsLinks
        $jsLinks .= '<script src="' . $src . '" '.$attributes.'></script>' . PHP_EOL;
    }
    // Descarrega os scripts no html
    $this->html = preg_replace('/{{ ?JS ?}}/i', $jsLinks, $this->html);
  }

  private function get_jsAttributes(string $string): array {
    // Inicializa as variáveis
    $src = '';
    $attributes = '';
  
    // Verifica se a string termina com um valor entre colchetes
    if (preg_match('/^(.*)\s*\[(.*?)\]$/', $string, $matches)) {
        // Caso positivo, separa a string e o atributo
        $src = rtrim($matches[1]); // Remove espaços em branco no final de $src
        $attributes = $matches[2]; // Captura o valor dentro dos colchetes
    } else {
        // Caso negativo, atribui a string original a $src e remove espaços em branco no final
        $src = rtrim($string);
    }
  
    // Retorna um array com os valores de $src e $attributes
    return array($src, $attributes);
  }
}

class Blueprint
{
  private array $array;
  private string $raw;

  public function __construct($componentName)
  {
    $this->raw = $this->load_rawBlueprint($componentName);
    $this->array = $this->parse_blueprint($this->raw);
  }

  private function load_rawBlueprint($componentName) : string
  {
    if (file_exists("../components/blueprints/$componentName.json")) {
      $raw = file_get_contents("../components/blueprints/$componentName.json");
      $raw = $this->insert_componentName($componentName, $raw);  // Insere no nome do componente no blueprint
      return $raw;
    }else{
      die("Blueprint [$componentName] não encontrado.");
    }
    
  }

  // Essa função insere no nome do componente no blueprint
  private function insert_componentName($componentName, $raw) : string
  {
    $insertion = "\n  \"COMPONENT-NAME\": \"$componentName\",";
    $pattern = '/(\{)/';
    $replacement = '${1}' . $insertion;
    return preg_replace($pattern, $replacement, $raw, 1);
  }

  private function parse_blueprint($raw) : array
  {
    $blueprint = json_decode($raw, true);
    foreach ($blueprint as $item => $value) {
      // Caso a chave seja css ou js e o valor seja uma string, converte para array
      if (in_array($item, ['css', 'js']) && is_string($value)) {
        if (strlen($value)>0) {
          $blueprint[$item] = [$value];
        }
      }
    }
    return $blueprint;
  }

  // Getters e setters
  public function __get($name): string|array|null
  {
    if (isset($this->array[$name])) {
      return $this->array[$name];
    } else {
      return null;
    }
  }

  public function array()
  {
    return $this->array;
  }

  public function raw()
  {
    return $this->raw;
  }
}

class Data
{
  private $blueprintData = [];
  private $controllerData = [];
  private $merged_data = [];
  public  $final_data = [];
  public  $js = [];
  public  $css = [];

  public function __construct(array $blueprintData = [], array $controllerData = [])
  {
    $this->blueprintData = $blueprintData;
    $this->controllerData = $controllerData;
    foreach ($this->blueprintData as $data_piece) {
      $this->merge_blueprintData($data_piece);
    }
    $this->merge_controllerData($this->controllerData);
    $this->parse_mergedData($this->merged_data);
    //print_r($this->css);
    //die("xxxxxxxx [ interrupção ] xxxxxxxxxxx");
  }

  /*
  |--------------------------------------------------------------------------
  | merge_blueprintData
  |--------------------------------------------------------------------------
  |
  | Processa um elemento de dados do blueprint e o armazena em $merged_data
  |
  */
  private function merge_blueprintData(array $data_piece) : void
  {
    switch ($data_piece['data-type']) {
      case 'template':
        if (file_exists("../components/templates/".$data_piece['data-source'].".html")) {
          $template = file_get_contents("../components/templates/".$data_piece['data-source'].".html");
          $this->merged_data[$data_piece['data-slot']][] = $template;
        }else{
          //echo "Template: ". $data_piece['data-source']. " não encontrado";
        }
        break;
      case 'string':
        $this->merged_data[$data_piece['data-slot']][] = $data_piece['data-source'];
        break;
      case 'env-var':
        $this->merged_data[$data_piece['data-slot']][] = recursiveArraySearch($_ENV, $data_piece['data-source']);
        break;
      case 'session-var':
        $this->merged_data[$data_piece['data-slot']][] = recursiveArraySearch($_SESSION, $data_piece['data-source']);
        break;
      case 'cookie':
        if (!isset($_COOKIE[$data_piece['data-source']])) {
          $data_source = "";
        }else {
          $data_source = $_COOKIE[$data_piece['data-source']];
        }
        $this->merged_data[$data_piece['data-slot']][] = $data_source;
        break;
      case 'component':
        // TODO: Implementar verificação para impedir que um componente não seja incluído dentro dele mesmo
        $this->merged_data[$data_piece['data-slot']][] = new Component($data_piece['data-source']);
        break;
    }
  }

  /*
  |--------------------------------------------------------------------------
  | merge_controllerData
  |--------------------------------------------------------------------------
  |
  | Processa o array de dados recebidos do controller e o armazena em $merged_data
  |
  */
  private function merge_controllerData($controllerData) : void
  {
    foreach ($controllerData as $slot => $content) {
      if (is_array($content)) {
        for ($i = 0; $i < count($content); $i++) {
          $this->merge_controllerData([$slot => $content[$i]]);         
          }
      }elseif(in_array($slot, ['css', 'js'])){
          $this->$slot[] = $content;
      }else{
        $this->merged_data[$slot][] = $content;
      }
    }
  }

  private function parse_mergedData(array $merged_data) : void
  {
    foreach ($merged_data as $slot => $content) {
      if ( $content instanceof Component) {
        $this->parse_childComponent($slot, $content);
      } elseif (is_array($content)) {
        for ($i = 0; $i < count($content); $i++) {
          $this->parse_mergedData([$slot => $content[$i]]);         
          }
      } else {
          $this->final_data[$slot][] = $content;
      }
    }
  }

  private function parse_childComponent(string $key, Component $component) : void
  {
    isset($component->js)? $this->push_asset("js", $component->js) : '';
    isset($component->css)? $this->push_asset("css", $component->css) : '';
    $this->final_data[$key][] = $component->html;
  }

  private function push_asset(string $assetName, array $sources) : void
  {
    foreach ($sources as $source) {
      $this->$assetName[] = $source;
    }
  }
}

class CSPManager
{
    private static $directives = [
        'script-src' => ["'self'"],
        //'style-src' => ["'self'"],
    ];

    public static function addSource($directive, $source)
    {
        // Verifica se a diretiva é suportada; caso contrário, ignora a adição
        if (!array_key_exists($directive, self::$directives)) {
            echo "Diretiva {$directive} não suportada.";
            return;
        }

        // Adiciona a origem à diretiva correspondente, evitando duplicatas
        if (!in_array($source, self::$directives[$directive])) {
            self::$directives[$directive][] = $source;
        }
    }

    public static function sendCSPHeader()
    {
        $policies = [];
        foreach (self::$directives as $directive => $sources) {
            if (!empty($sources)) { // Verifica se há fontes definidas
                $policies[] = $directive . " " . implode(" ", $sources);
            }
        }
        
        // Constrói e envia o header CSP
        header("Content-Security-Policy: " . implode("; ", $policies));
    }
}