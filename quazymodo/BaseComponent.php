<?php

namespace Quazymodo;

use Quazymodo\Blueprint;
use Quazymodo\ComponentData;
use Quazymodo\CSPManager;

class BaseComponent
{
  public Blueprint $blueprint;
  public ComponentData $data;
  public string $componentType;
  public string $componentName;
  public string $html = '';
  public array $js = [];
  public array $css = [];
  public array $slots = [];
  public static array $allData = [];
  private string $assetsPath = '/assets';

  /**
   * @param mixed $componentName 
   * @param array $inserts 
   * @param string $componentType
   * @return $this 
   */
  public function __construct($componentName, $inserts = [], $componentType)
  {
    if($componentType === "page"){
      CSPManager::setNonce($componentName);
    }
    $this->componentName = $componentName;
    $this->componentType = $componentType; 
    if ($componentType === "template") {
      $this->construct_template($componentName, $inserts);
    } else {
      $this->blueprint = new Blueprint($componentName, $inserts);  
      $this->html = $this->load_template($this->blueprint->array()['template']);
      $this->slots = $this->map_slots($this->html);
      $this->write_componentName($componentName);
      $this->data = new ComponentData($this->blueprint->inserts, $inserts);

      foreach($this->data->final_data as $key => $value) {
        self::$allData[$key] = $value;
      }
      is_array($this->blueprint->css)? $this->add_asset('css', $this->blueprint->css) : '';
      is_array($this->blueprint->js)? $this->add_asset('js', $this->blueprint->js) : '';
      is_array($this->data->css)? $this->add_asset('css', $this->data->css) : '';
      is_array($this->data->js)? $this->add_asset('js', $this->data->js) : '';
      $this->fill_slots();
    }
    return $this;
  }

  private function construct_template($templateName, $inserts = [])
  {    
    $this->html = $this->load_template($templateName);
    $this->slots = $this->map_slots($this->html);    
    $this->write_componentName($templateName . '_template');
    $this->data = new ComponentData([], $inserts);
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

  private function add_asset(string $assetName, array $asset)
  {
    foreach ($asset as $value) {
      $this->$assetName[] = $value;
    }
    $this->$assetName = array_unique($this->$assetName);
  }

  private function load_template($templateName)
  {
    // If $templateName ends with a slash, the template file has the same name as the last folder in the path
    if (substr($templateName, -1) === '/') {
      $templateName .= basename($templateName);
    }

    $template = file_get_contents("../app/components/$templateName.html");
    $template = str_replace('[{', '{{', $template);
    $template = str_replace('}]', '}}', $template);
    return $template;
  }

  private function map_slots($html) : array
  {
    preg_match_all('/{{ ?([^ ]*?) ?}}/i', $html, $matches);
    return $matches[1];
  }

  private function write_componentName($componentName) : void
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
  }

  public function render() : String
  {
    $this->flush_assets();
    $void_slots = $this->map_slots($this->html);
    foreach ($void_slots as $slot) {
      $this->html = preg_replace('/{{ ?' . $slot . ' ?}}/', isset(self::$allData[$slot]) ? implode(PHP_EOL, self::$allData[$slot]) : "", $this->html);
      unset(self::$allData[$slot]);
    }
    if ($this->map_slots($this->html)) {
      $this->render();
    }
    return $this->html;
  }

  private function flush_assets() : BaseComponent
  {
    $this->flush_css();
    $this->flush_js();
    return $this;
  }

  private function flush_css()
  {
    $cssLinks = '';
    foreach ($this->css as $index => $href) {
      if (strpos($href, 'http') === false) {
        $href = $this->assetsPath . $href;
      }
      $cssLinks .= '<link rel="stylesheet" type="text/css" href="' . $href . '">' . PHP_EOL;
      unset($this->css[$index]);
    }
    if (preg_match('/{{ ?CSS ?}}/i', $this->html)) {
      $this->html = preg_replace('/{{ ?CSS ?}}/i', $cssLinks, $this->html);
    } else {
      $this->html = $cssLinks . $this->html;
    }
  }

  private function flush_js() {
    $jsLinks = '';
    foreach ($this->js as $index => $file) {
      // Check if the file is an external link (starts with 'http')
      if (strpos($file, 'http') === 0) {
        $href = $file;
        CSPManager::addSource('script-src', $file);
      } else {
        // Otherwise, it is an internal file and add the 'assets/js/' path
        $versionedFile = $this->versionedFile($file);
        $href = $this->assetsPath . $versionedFile;
      }
      // Extract the attributes of the script - Example: [defer]
      list($src, $attributes) = $this->get_jsAttributes($href);

      // Add the script to the $jsLinks variable then remove it from the $this->js array
      $jsLinks .= '<script src="' . $src . '" '.$attributes.'></script>' . PHP_EOL;
      unset($this->js[$index]);
    }
    // flush the scripts into the HTML
    if (preg_match('/{{ ?JS ?}}/i', $this->html)) {
        $this->html = preg_replace('/{{ ?JS ?}}/i', $jsLinks, $this->html);
    } else {
        $this->html .= PHP_EOL . $jsLinks;
    }
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

  private function versionedFile(string $file): string
  {
    // Localiza o primeiro espaço na string
    $spacePosition = strpos($file, ' ');

    // Se houver espaço, separa o nome do arquivo e os atributos
    if ($spacePosition !== false) {
        $filename = substr($file, 0, $spacePosition); // Parte antes do espaço (ex.: 'base.js')
        $attributes = substr($file, $spacePosition + 1); // Parte após o espaço (ex.: '[defer async]')
    } else {
        // Caso não haja espaço, considera a string inteira como o nome do arquivo
        $filename = $file;
        $attributes = '';
    }

    // Define o caminho completo do arquivo usando DIRECTORY_SEPARATOR para compatibilidade
    $filepath = dirname(__DIR__) . '/app/components/' . $filename;

    // Verifica se o arquivo existe antes de tentar obter o timestamp
    if (file_exists($filepath)) {
        $version = filemtime($filepath); // Obtém o timestamp da última modificação
        // Adiciona o parâmetro de versionamento ao nome do arquivo
        $filename = str_replace('.js', ".js?v=$version", $filename);
    }

    // Retorna o arquivo com o parâmetro de versionamento e os atributos adicionais
    return trim($filename . ' ' . $attributes);
  }
}