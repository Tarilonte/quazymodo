<?php

namespace Quazymodo;

use Quazymodo\Blueprint;
use Quazymodo\ComponentData;
use Quazymodo\CSPManager;
use Quazymodo\Exceptions\ComponentCycleException;
use Quazymodo\Exceptions\SlotNotFoundException;
use Quazymodo\Exceptions\TemplateNotFoundException;

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
  public array $prefilledSlots = [];
  public int $cacheHits = 0;
  public static array $allData = [];
  private static array $templateReadCache = [];
  private static array $slotMapCache = [];
  private static array $activeComponentStack = [];
  private static array $activeComponentSet = [];
  private static array $loadedBlueprintComponents = [];
  private static int $renderDepth = 0;
  private const MAX_COMPONENT_DEPTH = 100;
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

    $this->initializeComponentContext($componentName, $componentType);
    $this->enterComponentGuard();

    try {
      if ($componentType === "template") {
        $this->buildTemplateComponent($componentName, $inserts);
      } else {
        $this->buildBlueprintComponent($componentName, $inserts);
      }
    } finally {
      $this->exitComponentGuard();
    }

    return $this;
  }

  private function enterComponentGuard(): void
  {
    $componentKey = $this->componentGuardKey($this->componentType, $this->componentName);

    if (count(self::$activeComponentStack) >= self::MAX_COMPONENT_DEPTH) {
      throw ComponentCycleException::depthExceeded(self::$activeComponentStack, $componentKey, self::MAX_COMPONENT_DEPTH);
    }

    if (isset(self::$activeComponentSet[$componentKey])) {
      throw ComponentCycleException::cycleDetected(self::$activeComponentStack, $componentKey);
    }

    self::$activeComponentStack[] = $componentKey;
    self::$activeComponentSet[$componentKey] = true;
  }

  private function exitComponentGuard(): void
  {
    $componentKey = array_pop(self::$activeComponentStack);

    if ($componentKey === null) {
      return;
    }

    unset(self::$activeComponentSet[$componentKey]);

    if (count(self::$activeComponentStack) === 0) {
      self::$activeComponentSet = [];
    }
  }

  private function componentGuardKey(string $componentType, string $componentName): string
  {
    return $componentType . ':' . $componentName;
  }

  private function initializeComponentContext(string $componentName, string $componentType): void
  {
    $this->componentName = $componentName;
    $this->componentType = $componentType;
  }

  private function buildTemplateComponent($templateName, $inserts = []): void
  {    
    $this->html = $this->load_template($templateName);
    $this->parsePrefilledSlots();
    $this->slots = $this->map_slots($this->html);    
    $this->write_componentName($templateName . '_template');
    $this->data = new ComponentData(
      blueprintInserts: [],
      inserts: $inserts,
      prefilledSlots: $this->prefilledSlots
    );

    $this->syncFinalDataToGlobalStore();
    $this->mergeDataAssets();

    $this->fill_slots();
  }

  private function buildBlueprintComponent($componentName, $inserts): void
  {
    $this->blueprint = new Blueprint($componentName, $inserts);
    $this->registerLoadedBlueprintComponent($componentName);

    $templateName = (string) $this->blueprint->get('template', '');
    $this->html = $this->load_template($templateName);
    $this->parsePrefilledSlots();
    $this->slots = $this->map_slots($this->html);
    $this->write_componentName($componentName);

    $blueprintInserts = $this->blueprint->get('inserts', []);
    $this->data = new ComponentData(
      blueprintInserts: is_array($blueprintInserts) ? $blueprintInserts : [],
      inserts: $inserts,
      prefilledSlots: $this->prefilledSlots
    );

    $this->syncFinalDataToGlobalStore();
    $this->mergeBlueprintAssets();
    $this->mergeDataAssets();

    $this->fill_slots();
  }

  private function syncFinalDataToGlobalStore(): void
  {
    foreach($this->data->final_data as $key => $value) {
      self::$allData[$key] = $value;
    }
  }

  private function registerLoadedBlueprintComponent(string $componentName): void
  {
    $normalizedComponent = $this->normalizeComponentName($componentName);
    self::$loadedBlueprintComponents[$normalizedComponent] = true;
  }

  private function collectConsumedInputKeysFromLoadedBlueprints(): array
  {
    $keys = [];
    $visited = [];

    foreach (array_keys(self::$loadedBlueprintComponents) as $componentName) {
      $keys = array_merge($keys, $this->collectConsumedInputKeysForComponent($componentName, $visited));
    }

    return array_values(array_unique($keys));
  }

  private function collectConsumedInputKeysForComponent(string $componentName, array &$visited): array
  {
    $normalizedComponent = $this->normalizeComponentName($componentName);

    if (in_array($normalizedComponent, $visited, true)) {
      return [];
    }

    $visited[] = $normalizedComponent;

    $blueprintPath = $this->resolveBlueprintPath($normalizedComponent);
    $source = $this->readFileIfExists($blueprintPath);

    if ($source === null) {
      return [];
    }

    $keys = [];

    preg_match_all('/\$inserts\[[\'\"]([^\'\"]+)[\'\"]\]/', $source, $inputMatches);
    $keys = array_merge($keys, $inputMatches[1] ?? []);

    $parentComponent = $this->extractExtendedComponentName($source);
    if ($parentComponent !== null) {
      $keys = array_merge($keys, $this->collectConsumedInputKeysForComponent($parentComponent, $visited));
    }

    return array_values(array_unique($keys));
  }

  private function extractExtendedComponentName(string $source): ?string
  {
    if (!preg_match('/[\'\"]extends[\'\"]\s*=>\s*[\'\"]([^\'\"]+)[\'\"]/i', $source, $extendsMatch)) {
      return null;
    }

    return $extendsMatch[1];
  }

  private function normalizeComponentName(string $componentName): string
  {
    if (substr($componentName, -1) === '/') {
      return $componentName . basename($componentName);
    }

    return $componentName;
  }

  private function resolveBlueprintPath(string $componentName): string
  {
    $normalized = $this->normalizeComponentName($componentName);
    return "../app/components/$normalized.blueprint.php";
  }

  private function readFileIfExists(string $path): ?string
  {
    if (!file_exists($path)) {
      return null;
    }

    $content = file_get_contents($path);
    return $content === false ? null : $content;
  }

  private function mergeBlueprintAssets(): void
  {
    $blueprintCss = $this->blueprint->get('css', []);
    $blueprintJs = $this->blueprint->get('js', []);

    if (is_array($blueprintCss)) {
      $this->add_asset('css', $blueprintCss);
    }

    if (is_array($blueprintJs)) {
      $this->add_asset('js', $blueprintJs);
    }
  }

  private function mergeDataAssets(): void
  {
    if (is_array($this->data->css)) {
      $this->add_asset('css', $this->data->css);
    }

    if (is_array($this->data->js)) {
      $this->add_asset('js', $this->data->js);
    }
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
    // E.g. /pages/home/ -> /pages/home/home.html
    if (substr($templateName, -1) === '/') {
      $templateName .= basename($templateName);
    }

    $path = "../app/components/$templateName.html";

    if (!file_exists($path)) {
      throw new TemplateNotFoundException($path);
    }

    if (array_key_exists($path, self::$templateReadCache)) {
      $this->cacheHits++;
      return self::$templateReadCache[$path];
    }

    $template = file_get_contents($path);
    
    $template = str_replace('[{', '{{', $template);
    $template = str_replace('}]', '}}', $template);

    self::$templateReadCache[$path] = $template;

    return $template;
  }

  private function map_slots($html) : array
  {
    $cacheKey = md5($html);

    if (array_key_exists($cacheKey, self::$slotMapCache)) {
      $this->cacheHits++;
      return self::$slotMapCache[$cacheKey];
    }

    preg_match_all('/{{ ?([^ ]*?) ?}}/i', $html, $matches);
    self::$slotMapCache[$cacheKey] = $matches[1];

    return $matches[1];
  }

  private function parsePrefilledSlots(): void
  {
    $this->prefilledSlots = [];

    // Slots pre-preenchidos viram inserts iniciais; o HTML segue com slots simples.
    $this->html = preg_replace_callback(
      '/{{\s*([^{}=]*?)\s*=\s*([^{}]*?)\s*}}/s',
      function (array $matches): string {
        $slotName = trim($matches[1]);
        $componentDeclaration = trim($matches[2]);

        if ($slotName === '') {
          throw new \InvalidArgumentException('Declaracao de slot pre-preenchido sem nome.');
        }

        if (!preg_match('/^(plugin|template):(.+)$/', $componentDeclaration, $componentMatches)) {
          throw new \InvalidArgumentException("Declaracao de slot pre-preenchido invalida: [$componentDeclaration]");
        }

        $componentType = $componentMatches[1];
        $componentName = trim($componentMatches[2]);

        if ($componentName === '') {
          throw new \InvalidArgumentException("Declaracao de slot pre-preenchido sem componente: [$componentDeclaration]");
        }

        $this->prefilledSlots[$slotName][] = $this->resolvePrefilledSlotContent(
          componentType: $componentType,
          componentName: $componentName
        );

        return '{{ ' . $slotName . ' }}';
      },
      $this->html
    ) ?? $this->html;
  }

  private function resolvePrefilledSlotContent(string $componentType, string $componentName): BaseComponent
  {
    // O componentName segue exatamente as regras atuais de ComponentFactory.
    return match ($componentType) {
      'plugin' => ComponentFactory::Plugin(componentName: $componentName),
      'template' => ComponentFactory::Template(componentName: $componentName),
    };
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
    foreach ($this->slots as $slot) {
      $this->fillDeclaredSlot($slot);
    }
  }

  private function fillDeclaredSlot(string $slot): void
  {
    if (!isset(self::$allData[$slot])) {
      return;
    }

    $content = implode(PHP_EOL, self::$allData[$slot]);
    $this->html = preg_replace('/{{ ?' . $slot . ' ?}}/', $content . "{{ $slot }}", $this->html);
    unset(self::$allData[$slot]);
  }

  public function render() : String
  {
    self::$renderDepth++;

    $isRootRenderCall = self::$renderDepth === 1;
    $isIsolatedNonPageRoot = $isRootRenderCall && $this->componentType !== 'page';
    $allDataSnapshot = $isIsolatedNonPageRoot ? self::$allData : [];
    $loadedComponentsSnapshot = $isIsolatedNonPageRoot ? self::$loadedBlueprintComponents : [];

    try {
      $this->flush_assets();
      $this->fillRemainingSlotsFromGlobalData();

      if ($this->hasPendingSlotsInHtml()) {
        $this->render();
      }

      if (self::$renderDepth === 1 && $this->componentType === 'page') {
        $this->assertNoUnresolvedSlots();
      }

      return $this->html;
    } finally {
      self::$renderDepth--;

      if ($isIsolatedNonPageRoot) {
        self::$allData = $allDataSnapshot;
        self::$loadedBlueprintComponents = $loadedComponentsSnapshot;
      } elseif (self::$renderDepth === 0) {
        self::$allData = [];
        self::$loadedBlueprintComponents = [];
      }
    }
  }

  private function assertNoUnresolvedSlots(): void
  {
    if (count(self::$allData) === 0) {
      return;
    }

    $unresolvedSlots = array_values(array_unique(array_keys(self::$allData)));

    $consumedInputKeys = $this->collectConsumedInputKeysFromLoadedBlueprints();
    $unresolvedSlots = array_values(array_diff($unresolvedSlots, $consumedInputKeys));

    if (count($unresolvedSlots) === 0) {
      return;
    }

    throw new SlotNotFoundException($this->componentName, $unresolvedSlots);
  }

  private function fillRemainingSlotsFromGlobalData(): void
  {
    $voidSlots = $this->map_slots($this->html);

    foreach ($voidSlots as $slot) {
      $content = isset(self::$allData[$slot]) ? implode(PHP_EOL, self::$allData[$slot]) : "";
      $this->html = preg_replace('/{{ ?' . $slot . ' ?}}/', $content, $this->html);
      unset(self::$allData[$slot]);
    }
  }

  private function hasPendingSlotsInHtml(): bool
  {
    return count($this->map_slots($this->html)) > 0;
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
      $href = $this->resolveCssHref($href);
      $cssLinks .= '<link rel="stylesheet" type="text/css" href="' . $href . '">' . PHP_EOL;
      unset($this->css[$index]);
    }
    if (preg_match('/{{ ?CSS ?}}/i', $this->html)) {
      $this->html = preg_replace('/{{ ?CSS ?}}/i', $cssLinks, $this->html);
    } else {
      $this->html = $cssLinks . $this->html;
    }
  }

  private function resolveCssHref(string $href): string
  {
    if (strpos($href, 'http') === false) {
      return $this->assetsPath . $href;
    }

    return $href;
  }

  private function flush_js() {
    $jsLinks = '';
    foreach ($this->js as $index => $file) {
      $href = $this->resolveJsHref($file);
      $jsLinks .= $this->buildScriptTag($href);
      unset($this->js[$index]);
    }

    if (preg_match('/{{ ?JS ?}}/i', $this->html)) {
        $this->html = preg_replace('/{{ ?JS ?}}/i', $jsLinks, $this->html);
    } else {
        $this->html .= PHP_EOL . $jsLinks;
    }
  }

  private function resolveJsHref(string $file): string
  {
    if (strpos($file, 'http') === 0) {
      CSPManager::addSource('script-src', $file);
      return $file;
    }

    $versionedFile = $this->versionedFile($file);
    return $this->assetsPath . $versionedFile;
  }

  private function buildScriptTag(string $href): string
  {
    list($src, $attributes) = $this->get_jsAttributes($href);
    return '<script src="' . $src . '" ' . $attributes . '></script>' . PHP_EOL;
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
