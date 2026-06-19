<?php

namespace Quazymodo;

/*
 * Minimal project CLI for Quazymodo.
 *
 * Intencao: centralizar scaffolding e inspecoes locais do projeto em uma unica
 * implementacao pequena e explicita para o v0.1.
 */
final class CliApplication
{
  private string $projectRoot;

  public function __construct(string $projectRoot)
  {
    $this->projectRoot = rtrim(string: $projectRoot, characters: '/');
  }

  public function run(array $arguments): int
  {
    $tokens = $arguments;
    array_shift(array: $tokens);

    $command = $tokens[0] ?? null;
    if ($command === null || $command === '') {
      $this->writeLine(message: $this->mainHelp());
      return 1;
    }

    array_shift(array: $tokens);
    [$positionals, $options] = $this->parseTokens(tokens: $tokens);

    return match ($command) {
      'help', '--help', '-h' => $this->handleHelp(),
      'make:component' => $this->handleMakeComponent(positionals: $positionals, options: $options),
      'make:controller' => $this->handleMakeController(positionals: $positionals, options: $options),
      'route:list' => $this->handleRouteList(options: $options),
      'check' => $this->handleCheck(options: $options),
      default => $this->handleUnknownCommand(command: $command),
    };
  }

  private function handleHelp(): int
  {
    $this->writeLine(message: $this->mainHelp());
    return 0;
  }

  private function handleUnknownCommand(string $command): int
  {
    $this->writeLine(message: "Comando desconhecido: {$command}");
    $this->writeLine(message: '');
    $this->writeLine(message: $this->mainHelp());

    return 1;
  }

  private function handleMakeComponent(array $positionals, array $options): int
  {
    if ($this->wantsHelp(options: $options)) {
      $this->writeLine(message: $this->makeComponentHelp());
      return 0;
    }

    $interactive = !$this->isTruthyOption(options: $options, key: 'no-interaction');
    $name = $positionals[0] ?? null;
    $type = $options['type'] ?? null;

    if ($interactive) {
      $name = $this->resolveInteractiveValue(
        currentValue: $name,
        prompt: 'Nome do componente',
      );
      $type = $this->resolveInteractiveChoice(
        currentValue: $type,
        prompt: 'Tipo do componente',
        choices: ['page', 'plugin'],
      );
    }

    if ($name === null || $name === '' || $type === null || $type === '') {
      $this->writeLine(message: 'Uso invalido. Informe nome e --type=page|plugin, ou use o modo interativo.');
      return 1;
    }

    $componentName = $this->normalizeComponentInput(name: $name);
    if ($componentName === null) {
      $this->writeLine(message: 'Nome de componente invalido. Use letras, numeros, hífen, underscore e barras.');
      return 1;
    }

    if (!in_array(needle: $type, haystack: ['page', 'plugin'], strict: true)) {
      $this->writeLine(message: 'Tipo invalido. Use page ou plugin.');
      return 1;
    }

    $componentMeta = $this->buildComponentMeta(type: $type, componentName: $componentName);
    if (is_dir(filename: $componentMeta['directory'])) {
      $this->writeLine(message: 'O componente ja existe: ' . $componentMeta['directory']);
      return 1;
    }

    $this->ensureDirectory(path: $componentMeta['directory']);
    $this->writeFile(path: $componentMeta['htmlPath'], contents: $this->componentHtmlTemplate(type: $type, componentName: $componentName));
    $this->writeFile(path: $componentMeta['blueprintPath'], contents: $this->componentBlueprintTemplate(type: $type, componentName: $componentName));

    $this->writeLine(message: 'Componente criado:');
    $this->writeLine(message: '- ' . $this->relativePath(path: $componentMeta['htmlPath']));
    $this->writeLine(message: '- ' . $this->relativePath(path: $componentMeta['blueprintPath']));

    $shouldCreateShortcut = false;
    if ($interactive) {
      $shouldCreateShortcut = $this->confirm(
        prompt: 'Deseja criar shortcut em App\\Components\\ComponentShortcuts?',
        default: false,
      );
    } elseif (isset($options['shortcut'])) {
      $shouldCreateShortcut = $this->isTruthyValue(value: $options['shortcut']);
    }

    if ($shouldCreateShortcut) {
      $shortcutMethodName = $options['shortcut-name'] ?? $this->buildShortcutMethodName(componentName: $componentName);
      $shortcutResult = $this->appendShortcut(
        componentType: $type,
        componentName: $componentName,
        methodName: $shortcutMethodName,
      );

      if ($shortcutResult !== null) {
        $this->writeLine(message: $shortcutResult);
        return 1;
      }

      $this->writeLine(message: '- shortcut atualizado em app/components/ComponentShortcuts.php');
    }

    return 0;
  }

  private function handleMakeController(array $positionals, array $options): int
  {
    if ($this->wantsHelp(options: $options)) {
      $this->writeLine(message: $this->makeControllerHelp());
      return 0;
    }

    $interactive = !$this->isTruthyOption(options: $options, key: 'no-interaction');
    $name = $positionals[0] ?? null;
    $routeFile = $options['route-file'] ?? null;
    $httpMethod = $options['http-method'] ?? null;
    $path = $options['path'] ?? null;
    $action = $options['action'] ?? 'index';

    if ($interactive) {
      $name = $this->resolveInteractiveValue(
        currentValue: $name,
        prompt: 'Nome do controller',
      );

      $routeFile = $this->resolveInteractiveChoice(
        currentValue: $routeFile,
        prompt: 'Arquivo de rota',
        choices: ['web', 'api', 'dev', 'test'],
      );

      $httpMethod = $this->resolveInteractiveValue(
        currentValue: $httpMethod,
        prompt: 'Metodo HTTP',
        defaultValue: 'GET',
      );

      $defaultPath = $this->defaultRoutePath(name: $name ?? '');
      $path = $this->resolveInteractiveValue(
        currentValue: $path,
        prompt: 'Path da rota',
        defaultValue: $defaultPath,
      );

      $action = $this->resolveInteractiveValue(
        currentValue: $action,
        prompt: 'Metodo do controller',
        defaultValue: 'index',
      );
    }

    if ($name === null || $routeFile === null || $httpMethod === null || $path === null || $action === null) {
      $this->writeLine(message: 'Uso invalido. Informe nome, --route-file, --http-method, --path e opcionalmente --action, ou use o modo interativo.');
      return 1;
    }

    $className = $this->normalizeControllerClassName(name: $name);
    if ($className === null) {
      $this->writeLine(message: 'Nome de controller invalido. Use letras e numeros e deixe o sufixo Controller opcional.');
      return 1;
    }

    $routeFile = strtolower(string: trim(string: $routeFile));
    if (!in_array(needle: $routeFile, haystack: ['web', 'api', 'dev', 'test'], strict: true)) {
      $this->writeLine(message: 'Arquivo de rota invalido. Use web, api, dev ou test.');
      return 1;
    }

    $httpMethod = strtoupper(string: trim(string: $httpMethod));
    if (!preg_match(pattern: '/^[A-Z]+$/', subject: $httpMethod)) {
      $this->writeLine(message: 'Metodo HTTP invalido.');
      return 1;
    }

    $path = $this->normalizeRoutePath(path: $path);
    if ($path === null) {
      $this->writeLine(message: 'Path de rota invalido. O path deve comecar com /.');
      return 1;
    }

    $action = $this->normalizeActionName(action: $action);
    if ($action === null) {
      $this->writeLine(message: 'Metodo do controller invalido. Use um identificador PHP simples.');
      return 1;
    }

    $controllerPath = $this->projectPath(path: 'app/controllers/' . $className . '.php');
    if (file_exists(filename: $controllerPath)) {
      $this->writeLine(message: 'O controller ja existe: ' . $this->relativePath(path: $controllerPath));
      return 1;
    }

    $routePath = $this->projectPath(path: 'app/routes/' . $routeFile . '.php');
    if (!file_exists(filename: $routePath)) {
      $this->writeLine(message: 'Arquivo de rota nao encontrado: ' . $this->relativePath(path: $routePath));
      return 1;
    }

    $handler = 'Controller\\' . $className . '::' . $action;
    $routeSource = $this->readFile(path: $routePath);
    if (str_contains(haystack: $routeSource, needle: "path: '{$path}'") && str_contains(haystack: $routeSource, needle: "handler: '{$handler}'")) {
      $this->writeLine(message: 'A rota ja existe nesse arquivo.');
      return 1;
    }

    $this->writeFile(path: $controllerPath, contents: $this->controllerTemplate(className: $className, action: $action));
    $this->writeFile(path: $routePath, contents: $this->appendRouteSource(source: $routeSource, className: $className, action: $action, httpMethod: $httpMethod, path: $path));

    $this->writeLine(message: 'Controller criado:');
    $this->writeLine(message: '- ' . $this->relativePath(path: $controllerPath));
    $this->writeLine(message: '- rota adicionada em ' . $this->relativePath(path: $routePath));

    return 0;
  }

  private function handleRouteList(array $options): int
  {
    if ($this->wantsHelp(options: $options)) {
      $this->writeLine(message: $this->routeListHelp());
      return 0;
    }

    $routes = $this->collectRoutes();
    if ($routes === []) {
      $this->writeLine(message: 'Nenhuma rota encontrada.');
      return 0;
    }

    $headers = ['METHOD', 'PATH', 'HANDLER', 'MIDDLEWARE', 'SCOPE'];
    $rows = [];

    foreach ($routes as $route) {
      $rows[] = [
        $route['method'],
        $route['path'],
        $route['handler'],
        $route['middleware'],
        $route['scope'],
      ];
    }

    $widths = $this->tableWidths(headers: $headers, rows: $rows);
    $this->writeLine(message: $this->formatTableRow(values: $headers, widths: $widths));
    $this->writeLine(message: $this->formatTableSeparator(widths: $widths));

    foreach ($rows as $row) {
      $this->writeLine(message: $this->formatTableRow(values: $row, widths: $widths));
    }

    return 0;
  }

  private function handleCheck(array $options): int
  {
    if ($this->wantsHelp(options: $options)) {
      $this->writeLine(message: $this->checkHelp());
      return 0;
    }

    $only = $options['only'] ?? null;
    $format = strtolower(string: (string) ($options['format'] ?? 'text'));
    $strict = $this->isTruthyOption(options: $options, key: 'strict');

    $allowedScopes = ['routes', 'components', 'config'];
    if ($only !== null && !in_array(needle: $only, haystack: $allowedScopes, strict: true)) {
      $this->writeLine(message: 'Escopo invalido para --only. Use routes, components ou config.');
      return 1;
    }

    if (!in_array(needle: $format, haystack: ['text', 'json'], strict: true)) {
      $this->writeLine(message: 'Formato invalido. Use text ou json.');
      return 1;
    }

    $report = [];
    foreach ($allowedScopes as $scope) {
      if ($only !== null && $scope !== $only) {
        continue;
      }

      $report[$scope] = match ($scope) {
        'routes' => $this->checkRoutes(),
        'components' => $this->checkComponents(),
        'config' => $this->checkConfig(),
      };
    }

    if ($format === 'json') {
      $this->writeLine(message: $this->jsonReport(report: $report, strict: $strict));
    } else {
      $this->writeLine(message: $this->textReport(report: $report));
    }

    $summary = $this->summarizeReport(report: $report);
    $hasFailure = $summary['failed'] > 0 || ($strict && $summary['warnings'] > 0);

    return $hasFailure ? 1 : 0;
  }

  private function parseTokens(array $tokens): array
  {
    $positionals = [];
    $options = [];
    $count = count(value: $tokens);

    for ($index = 0; $index < $count; $index++) {
      $token = $tokens[$index];

      if (!str_starts_with(haystack: $token, needle: '--')) {
        $positionals[] = $token;
        continue;
      }

      $option = substr(string: $token, offset: 2);
      if (str_contains(haystack: $option, needle: '=')) {
        [$key, $value] = explode(separator: '=', string: $option, limit: 2);
        $options[$key] = $value;
        continue;
      }

      $nextToken = $tokens[$index + 1] ?? null;
      if ($nextToken !== null && !str_starts_with(haystack: $nextToken, needle: '--')) {
        $options[$option] = $nextToken;
        $index++;
        continue;
      }

      $options[$option] = true;
    }

    return [$positionals, $options];
  }

  private function wantsHelp(array $options): bool
  {
    return isset($options['help']) || isset($options['h']);
  }

  private function isTruthyOption(array $options, string $key): bool
  {
    if (!array_key_exists($key, $options)) {
      return false;
    }

    return $this->isTruthyValue(value: $options[$key]);
  }

  private function isTruthyValue(mixed $value): bool
  {
    if (is_bool(value: $value)) {
      return $value;
    }

    return in_array(
      needle: strtolower(string: (string) $value),
      haystack: ['1', 'true', 'yes', 'y', 'sim', 's'],
      strict: true,
    );
  }

  private function resolveInteractiveValue(?string $currentValue, string $prompt, ?string $defaultValue = null): ?string
  {
    if ($currentValue !== null && trim(string: $currentValue) !== '') {
      return trim(string: $currentValue);
    }

    $suffix = $defaultValue !== null ? " [{$defaultValue}]" : '';
    $input = $this->readLine(prompt: $prompt . $suffix . ': ');
    if ($input === '' && $defaultValue !== null) {
      return $defaultValue;
    }

    return $input !== '' ? $input : null;
  }

  private function resolveInteractiveChoice(?string $currentValue, string $prompt, array $choices): ?string
  {
    if ($currentValue !== null && in_array(needle: $currentValue, haystack: $choices, strict: true)) {
      return $currentValue;
    }

    $label = implode(separator: '/', array: $choices);
    $input = $this->readLine(prompt: "{$prompt} ({$label}): ");
    $input = trim(string: $input);

    return in_array(needle: $input, haystack: $choices, strict: true) ? $input : null;
  }

  private function confirm(string $prompt, bool $default = false): bool
  {
    $defaultLabel = $default ? 'S/n' : 's/N';
    $input = strtolower(string: trim(string: $this->readLine(prompt: "{$prompt} [{$defaultLabel}]: ")));

    if ($input === '') {
      return $default;
    }

    return in_array(needle: $input, haystack: ['s', 'sim', 'y', 'yes'], strict: true);
  }

  private function readLine(string $prompt): string
  {
    $this->write(message: $prompt);
    $input = fgets(stream: STDIN);

    return $input === false ? '' : trim(string: $input);
  }

  private function buildComponentMeta(string $type, string $componentName): array
  {
    $baseDirectory = $type === 'page' ? 'app/components/pages/' : 'app/components/plugins/';
    $lastSegment = $this->lastSegment(path: $componentName);
    $directory = $this->projectPath(path: $baseDirectory . $componentName);

    return [
      'directory' => $directory,
      'htmlPath' => $directory . '/' . $lastSegment . '.html',
      'blueprintPath' => $directory . '/' . $lastSegment . '.blueprint.php',
      'componentPath' => '/' . ($type === 'page' ? 'pages/' : 'plugins/') . $componentName . '/',
    ];
  }

  private function componentHtmlTemplate(string $type, string $componentName): string
  {
    $label = $this->humanizeName(name: $this->lastSegment(path: $componentName));

    if ($type === 'page') {
      return <<<HTML
<section class="mx-auto w-full max-w-screen-lg p-6 md:p-10">
  <div class="space-y-4 text-base-content">
    <h1 class="text-3xl font-bold">
      {$label}
    </h1>
    <p class="text-base-content/70">
      Pagina inicial gerada pelo qzy.
    </p>
  </div>
</section>
HTML;
    }

    return <<<HTML
<div class="rounded-box border border-base-300 bg-base-100 p-4 shadow-sm">
  <p class="font-medium text-base-content">
    {$label}
  </p>
</div>
HTML;
  }

  private function componentBlueprintTemplate(string $type, string $componentName): string
  {
    $componentPath = '/' . ($type === 'page' ? 'pages/' : 'plugins/') . $componentName . '/';
    $label = $this->humanizeName(name: $this->lastSegment(path: $componentName));

    if ($type === 'page') {
      return <<<PHP
<?php

use Quazymodo\ComponentFactory;

/*
 * Page blueprint generated by qzy.
 *
 * Intencao: fornecer um ponto inicial simples para a pagina {$label}.
 */
return [
  'extends' => '/pages/base/',
  'inserts' => [
    'page-title' => '{$label}',
    'body' => [
      ComponentFactory::Template(
        componentName: '{$componentPath}',
      ),
    ],
  ],
];
PHP;
    }

    return <<<PHP
<?php

/*
 * Plugin blueprint generated by qzy.
 *
 * Intencao: fornecer um ponto inicial simples para o plugin {$label}.
 */
return [
  'template' => '{$componentPath}',
  'inserts' => [
  ],
];
PHP;
  }

  private function controllerTemplate(string $className, string $action): string
  {
    return <<<PHP
<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;

/*
 * Controller generated by qzy.
 *
 * Intencao: fornecer um endpoint inicial seguro para evolucao manual.
 */
class {$className} extends AbstractController
{
  public function {$action}(): ResponseInterface
  {
    // Mantem o scaffold funcional mesmo antes da view definitiva existir.
    return \$this->json(
      body: [
        'message' => '{$className}::{$action}',
      ],
    );
  }
}
PHP;
  }

  private function appendRouteSource(string $source, string $className, string $action, string $httpMethod, string $path): string
  {
    $trimmed = rtrim(string: $source);
    $routeComment = strtolower(string: $httpMethod) === 'get' ? '// Generated route.' : '// Generated route.';

    return $trimmed . PHP_EOL . PHP_EOL . $routeComment . PHP_EOL . "\$router->map(method: '{$httpMethod}', path: '{$path}', handler: 'Controller\\{$className}::{$action}');" . PHP_EOL;
  }

  private function normalizeComponentInput(string $name): ?string
  {
    $normalized = trim(string: str_replace(search: '\\', replace: '/', subject: $name), characters: " /\t\n\r\0\x0B");
    if ($normalized === '') {
      return null;
    }

    return preg_match(pattern: '/^[A-Za-z0-9_-]+(?:\/[A-Za-z0-9_-]+)*$/', subject: $normalized) === 1
      ? $normalized
      : null;
  }

  private function normalizeControllerClassName(string $name): ?string
  {
    $normalized = preg_replace(pattern: '/[^A-Za-z0-9]/', replacement: ' ', subject: $name);
    if ($normalized === null) {
      return null;
    }

    $segments = preg_split(pattern: '/\s+/', subject: trim(string: $normalized)) ?: [];
    if ($segments === []) {
      return null;
    }

    $className = '';
    foreach ($segments as $segment) {
      $className .= ucfirst(string: strtolower(string: $segment));
    }

    if (!str_ends_with(haystack: $className, needle: 'Controller')) {
      $className .= 'Controller';
    }

    return preg_match(pattern: '/^[A-Z][A-Za-z0-9]*Controller$/', subject: $className) === 1
      ? $className
      : null;
  }

  private function normalizeActionName(string $action): ?string
  {
    $normalized = trim(string: $action);

    return preg_match(pattern: '/^[a-zA-Z_][a-zA-Z0-9_]*$/', subject: $normalized) === 1
      ? $normalized
      : null;
  }

  private function normalizeRoutePath(string $path): ?string
  {
    $normalized = trim(string: $path);
    if ($normalized === '') {
      return null;
    }

    if (!str_starts_with(haystack: $normalized, needle: '/')) {
      $normalized = '/' . $normalized;
    }

    return str_contains(haystack: $normalized, needle: ' ') ? null : $normalized;
  }

  private function defaultRoutePath(string $name): string
  {
    $className = $this->normalizeControllerClassName(name: $name) ?? 'NovoController';
    $trimmed = preg_replace(pattern: '/Controller$/', replacement: '', subject: $className) ?? $className;
    $slug = strtolower(string: preg_replace(pattern: '/(?<!^)[A-Z]/', replacement: '-$0', subject: $trimmed) ?? $trimmed);

    return '/' . $slug;
  }

  private function buildShortcutMethodName(string $componentName): string
  {
    $segments = explode(separator: '/', string: $componentName);
    $methodName = '';

    foreach ($segments as $index => $segment) {
      $chunk = preg_replace(pattern: '/[^A-Za-z0-9]/', replacement: ' ', subject: $segment) ?? $segment;
      $words = preg_split(pattern: '/\s+/', subject: trim(string: $chunk)) ?: [];
      $segmentName = '';

      foreach ($words as $wordIndex => $word) {
        $lower = strtolower(string: $word);
        $segmentName .= ($index === 0 && $wordIndex === 0)
          ? $lower
          : ucfirst(string: $lower);
      }

      $methodName .= $segmentName;
    }

    return $methodName !== '' ? $methodName : 'newComponent';
  }

  private function appendShortcut(string $componentType, string $componentName, string $methodName): ?string
  {
    if (!preg_match(pattern: '/^[a-z][A-Za-z0-9]*$/', subject: $methodName)) {
      return 'Nome de shortcut invalido. Use um metodo camelCase simples.';
    }

    $shortcutPath = $this->projectPath(path: 'app/components/ComponentShortcuts.php');
    $source = $this->readFile(path: $shortcutPath);

    if (str_contains(haystack: $source, needle: 'function ' . $methodName . '(')) {
      return 'Ja existe um shortcut com esse nome em app/components/ComponentShortcuts.php.';
    }

    $factory = $componentType === 'page' ? 'Page' : 'Plugin';
    $componentPath = '/' . ($componentType === 'page' ? 'pages/' : 'plugins/') . $componentName . '/';
    $methodSource = PHP_EOL
      . '  /**' . PHP_EOL
      . '   * Creates the generated ' . $componentType . ' component.' . PHP_EOL
      . '   */' . PHP_EOL
      . '  public static function ' . $methodName . '(array $inserts = []): BaseComponent' . PHP_EOL
      . '  {' . PHP_EOL
      . '    return ComponentFactory::' . $factory . '(' . PHP_EOL
      . "      componentName: '{$componentPath}'," . PHP_EOL
      . '      inserts: $inserts,' . PHP_EOL
      . '    );' . PHP_EOL
      . '  }' . PHP_EOL;

    $position = strrpos(haystack: $source, needle: '}');
    if ($position === false) {
      return 'Nao foi possivel localizar o fechamento da classe ComponentShortcuts.';
    }

    $updatedSource = substr(string: $source, offset: 0, length: $position)
      . $methodSource
      . substr(string: $source, offset: $position);

    $this->writeFile(path: $shortcutPath, contents: $updatedSource);

    return null;
  }

  private function collectRoutes(): array
  {
    $routes = [];
    $globalMiddleware = $this->globalMiddlewareNotes();

    foreach (['web', 'api', 'dev', 'test'] as $scope) {
      $path = $this->projectPath(path: 'app/routes/' . $scope . '.php');
      if (!file_exists(filename: $path)) {
        continue;
      }

      $source = $this->readFile(path: $path);
      preg_match_all(
        pattern: '/\$router->map\(method:\s*[\'\"](?<method>[^\'\"]+)[\'\"],\s*path:\s*[\'\"](?<path>[^\'\"]+)[\'\"],\s*handler:\s*[\'\"](?<handler>[^\'\"]+)[\'\"]\);/',
        subject: $source,
        matches: $matches,
        flags: PREG_SET_ORDER,
      );

      foreach ($matches as $match) {
        $routes[] = [
          'method' => $match['method'],
          'path' => $match['path'],
          'handler' => $match['handler'],
          'middleware' => $globalMiddleware[$scope] ?? '-',
          'scope' => $scope,
        ];
      }
    }

    return $routes;
  }

  private function globalMiddlewareNotes(): array
  {
    $indexPath = $this->projectPath(path: 'app/routes/index.php');
    if (!file_exists(filename: $indexPath)) {
      return [];
    }

    $source = $this->readFile(path: $indexPath);
    preg_match_all(
      pattern: '/\$router->middleware\(middleware:\s*new\s+([^\(]+)\(\)\);/',
      subject: $source,
      matches: $matches,
    );

    $middleware = $matches[1] ?? [];
    if ($middleware === []) {
      return [];
    }

    $label = 'global: ' . implode(separator: ', ', array: $middleware);
    if (str_contains(haystack: $source, needle: 'RATE_LIMIT_REQUESTS > 0')) {
      $label .= ' (conditional)';
    }

    return [
      'web' => $label,
      'api' => $label,
      'dev' => $label,
      'test' => $label,
    ];
  }

  private function checkRoutes(): array
  {
    $results = [];
    $expectedFiles = ['index.php', 'web.php', 'api.php', 'dev.php', 'test.php'];

    foreach ($expectedFiles as $file) {
      $path = $this->projectPath(path: 'app/routes/' . $file);
      $results[] = $this->result(
        status: file_exists(filename: $path) ? 'pass' : 'fail',
        message: 'Arquivo de rota ' . $file . (file_exists(filename: $path) ? ' encontrado.' : ' ausente.'),
      );
    }

    $indexPath = $this->projectPath(path: 'app/routes/index.php');
    if (file_exists(filename: $indexPath)) {
      $source = $this->readFile(path: $indexPath);
      foreach (['web.php', 'api.php', 'test.php', 'dev.php'] as $requiredFile) {
        $results[] = $this->result(
          status: str_contains(haystack: $source, needle: $requiredFile) ? 'pass' : 'fail',
          message: 'Entrypoint de rotas carrega ' . $requiredFile . (str_contains(haystack: $source, needle: $requiredFile) ? '.' : ' nao encontrado.'),
        );
      }
    }

    foreach (['web', 'api', 'dev', 'test'] as $scope) {
      $path = $this->projectPath(path: 'app/routes/' . $scope . '.php');
      if (!file_exists(filename: $path)) {
        continue;
      }

      $lines = preg_split(pattern: '/\R/', subject: $this->readFile(path: $path)) ?: [];
      foreach ($lines as $lineNumber => $line) {
        if (!str_contains(haystack: $line, needle: '$router->map(')) {
          continue;
        }

        $valid = preg_match(
          pattern: '/\$router->map\(method:\s*[\'\"][^\'\"]+[\'\"],\s*path:\s*[\'\"][^\'\"]+[\'\"],\s*handler:\s*[\'\"][^\'\"]+[\'\"]\);/',
          subject: trim(string: $line),
        ) === 1;

        $results[] = $this->result(
          status: $valid ? 'pass' : 'fail',
          message: $valid
            ? 'Rota valida em ' . $scope . '.php:' . ($lineNumber + 1) . '.'
            : 'Rota invalida em ' . $scope . '.php:' . ($lineNumber + 1) . '.',
        );
      }
    }

    return $results;
  }

  private function checkComponents(): array
  {
    $results = [];
    foreach (['pages', 'plugins'] as $scope) {
      $root = $this->projectPath(path: 'app/components/' . $scope);
      if (!is_dir(filename: $root)) {
        $results[] = $this->result(status: 'fail', message: 'Diretorio ausente: app/components/' . $scope);
        continue;
      }

      foreach ($this->phpFiles(root: $root) as $file) {
        if (!str_ends_with(haystack: $file, needle: '.blueprint.php')) {
          continue;
        }

        $source = $this->readFile(path: $file);
        $results = array_merge($results, $this->checkBlueprintTemplate(file: $file, source: $source));
        $results = array_merge($results, $this->checkBlueprintAssets(file: $file, source: $source));
      }
    }

    return $results;
  }

  private function checkConfig(): array
  {
    $results = [];
    $requiredFiles = ['app.php', 'rate-limit.php', 'database.php', 'services.php', 'session.php', 'tracy.php', 'index.php'];

    foreach ($requiredFiles as $file) {
      $path = $this->projectPath(path: 'app/config/' . $file);
      $results[] = $this->result(
        status: file_exists(filename: $path) ? 'pass' : 'fail',
        message: 'Arquivo de config ' . $file . (file_exists(filename: $path) ? ' encontrado.' : ' ausente.'),
      );
    }

    $indexPath = $this->projectPath(path: 'app/config/index.php');
    if (file_exists(filename: $indexPath)) {
      $source = $this->readFile(path: $indexPath);
      foreach (['app.php', 'rate-limit.php', 'database.php', 'services.php', 'session.php', 'tracy.php'] as $requiredFile) {
        $results[] = $this->result(
          status: str_contains(haystack: $source, needle: $requiredFile) ? 'pass' : 'fail',
          message: 'Entrypoint de config referencia ' . $requiredFile . (str_contains(haystack: $source, needle: $requiredFile) ? '.' : ' nao encontrado.'),
        );
      }
    }

    $appConfigPath = $this->projectPath(path: 'app/config/app.php');
    if (file_exists(filename: $appConfigPath)) {
      $source = $this->readFile(path: $appConfigPath);
      $hasValidEnvironment = preg_match(pattern: "/const\\s+APP_ENV\\s*=\\s*'(development|production)'\\s*;/", subject: $source) === 1;
      $hasValidUrl = preg_match(pattern: "/const\\s+APP_URL\\s*=\\s*'[^']+'\\s*;/", subject: $source) === 1;

      $results[] = $this->result(
        status: $hasValidEnvironment ? 'pass' : 'fail',
        message: $hasValidEnvironment
          ? 'Constante APP_ENV valida em app/config/app.php.'
          : 'Constante APP_ENV ausente ou invalida em app/config/app.php.',
      );
      $results[] = $this->result(
        status: $hasValidUrl ? 'pass' : 'fail',
        message: $hasValidUrl
          ? 'Constante APP_URL valida em app/config/app.php.'
          : 'Constante APP_URL ausente ou invalida em app/config/app.php.',
      );
    }

    return $results;
  }

  private function checkBlueprintTemplate(string $file, string $source): array
  {
    $results = [];
    if (!preg_match(pattern: "/['\"]template['\"]\\s*=>\\s*['\"](?<template>[^'\"]+)['\"]/", subject: $source, matches: $match)) {
      return $results;
    }

    $templatePath = $this->componentPathToHtml(componentPath: $match['template']);
    $templateExists = file_exists(filename: $templatePath);
    $results[] = $this->result(
      status: $templateExists ? 'pass' : 'fail',
      message: $templateExists
        ? 'Template principal resolvido para ' . $this->relativePath(path: $file) . '.'
        : 'Template principal ausente para ' . $this->relativePath(path: $file) . '.',
    );

    return $results;
  }

  private function checkBlueprintAssets(string $file, string $source): array
  {
    $results = [];
    $lines = preg_split(pattern: '/\R/', subject: $source) ?: [];

    foreach ($lines as $line) {
      $trimmedLine = trim(string: $line);
      if ($trimmedLine === '' || str_starts_with(haystack: $trimmedLine, needle: '//') || str_starts_with(haystack: $trimmedLine, needle: '/*') || str_starts_with(haystack: $trimmedLine, needle: '*')) {
        continue;
      }

      preg_match_all(pattern: "/['\"]([^'\"]+\\.(css|js))['\"]/i", subject: $trimmedLine, matches: $matches);
      foreach ($matches[1] ?? [] as $asset) {
        if (str_starts_with(haystack: $asset, needle: '/')) {
          continue;
        }

        if (str_starts_with(haystack: $asset, needle: 'http://') || str_starts_with(haystack: $asset, needle: 'https://') || str_starts_with(haystack: $asset, needle: '//')) {
          continue;
        }

        $assetPath = dirname(path: $file) . '/' . $asset;
        $assetExists = file_exists(filename: $assetPath);
        $results[] = $this->result(
          status: $assetExists ? 'pass' : 'fail',
          message: $assetExists
            ? 'Asset referenciado em ' . $this->relativePath(path: $file) . ': ' . $asset
            : 'Asset ausente em ' . $this->relativePath(path: $file) . ': ' . $asset,
        );
      }
    }

    return $results;
  }

  private function componentPathToHtml(string $componentPath): string
  {
    $trimmed = trim(string: $componentPath);
    $normalized = trim(string: $trimmed, characters: '/');

    if (str_ends_with(haystack: $trimmed, needle: '/')) {
      $segment = $this->lastSegment(path: $normalized);
      return $this->projectPath(path: 'app/components/' . $normalized . '/' . $segment . '.html');
    }

    return $this->projectPath(path: 'app/components/' . $normalized . '.html');
  }

  private function summarizeReport(array $report): array
  {
    $summary = ['passed' => 0, 'failed' => 0, 'warnings' => 0];

    foreach ($report as $entries) {
      foreach ($entries as $entry) {
        if ($entry['status'] === 'pass') {
          $summary['passed']++;
          continue;
        }

        if ($entry['status'] === 'warn') {
          $summary['warnings']++;
          continue;
        }

        $summary['failed']++;
      }
    }

    return $summary;
  }

  private function textReport(array $report): string
  {
    $lines = [];

    foreach ($report as $scope => $entries) {
      $lines[] = strtoupper(string: $scope);
      foreach ($entries as $entry) {
        $lines[] = '- [' . strtoupper(string: $entry['status']) . '] ' . $entry['message'];
      }
      $lines[] = '';
    }

    $summary = $this->summarizeReport(report: $report);
    $lines[] = 'Resumo: ' . $summary['passed'] . ' pass, ' . $summary['failed'] . ' fail, ' . $summary['warnings'] . ' warn.';

    return implode(separator: PHP_EOL, array: $lines);
  }

  private function jsonReport(array $report, bool $strict): string
  {
    $summary = $this->summarizeReport(report: $report);

    return (string) json_encode(
      value: [
        'strict' => $strict,
        'summary' => $summary,
        'report' => $report,
      ],
      flags: JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
    );
  }

  private function result(string $status, string $message): array
  {
    return [
      'status' => $status,
      'message' => $message,
    ];
  }

  private function tableWidths(array $headers, array $rows): array
  {
    $widths = [];
    foreach ($headers as $index => $header) {
      $widths[$index] = strlen(string: $header);
    }

    foreach ($rows as $row) {
      foreach ($row as $index => $value) {
        $widths[$index] = max($widths[$index], strlen(string: (string) $value));
      }
    }

    return $widths;
  }

  private function formatTableRow(array $values, array $widths): string
  {
    $cells = [];
    foreach ($values as $index => $value) {
      $cells[] = str_pad(string: (string) $value, length: $widths[$index]);
    }

    return implode(separator: ' | ', array: $cells);
  }

  private function formatTableSeparator(array $widths): string
  {
    $cells = [];
    foreach ($widths as $width) {
      $cells[] = str_repeat(string: '-', times: $width);
    }

    return implode(separator: '-+-', array: $cells);
  }

  private function phpFiles(string $root): array
  {
    $files = [];
    $iterator = new \RecursiveIteratorIterator(
      iterator: new \RecursiveDirectoryIterator(
        directory: $root,
        flags: \FilesystemIterator::SKIP_DOTS,
      ),
    );

    foreach ($iterator as $file) {
      if (!$file instanceof \SplFileInfo || !$file->isFile()) {
        continue;
      }

      $files[] = $file->getPathname();
    }

    sort(array: $files);

    return $files;
  }

  private function ensureDirectory(string $path): void
  {
    if (is_dir(filename: $path)) {
      return;
    }

    mkdir(directory: $path, permissions: 0775, recursive: true);
  }

  private function writeFile(string $path, string $contents): void
  {
    $directory = dirname(path: $path);
    if (!is_dir(filename: $directory)) {
      $this->ensureDirectory(path: $directory);
    }

    file_put_contents(filename: $path, data: $contents, flags: LOCK_EX);
  }

  private function readFile(string $path): string
  {
    $contents = file_get_contents(filename: $path);

    return $contents === false ? '' : $contents;
  }

  private function projectPath(string $path): string
  {
    return $this->projectRoot . '/' . ltrim(string: $path, characters: '/');
  }

  private function relativePath(string $path): string
  {
    return ltrim(string: str_replace(search: $this->projectRoot, replace: '', subject: $path), characters: '/');
  }

  private function lastSegment(string $path): string
  {
    $segments = explode(separator: '/', string: trim(string: $path, characters: '/'));
    return $segments[count(value: $segments) - 1];
  }

  private function humanizeName(string $name): string
  {
    $label = str_replace(search: ['-', '_'], replace: ' ', subject: $name);
    return ucfirst(string: strtolower(string: $label));
  }

  private function write(string $message): void
  {
    fwrite(stream: STDOUT, data: $message);
  }

  private function writeLine(string $message): void
  {
    $this->write(message: $message . PHP_EOL);
  }

  private function mainHelp(): string
  {
    return <<<TEXT
Quazymodo CLI v0.1

Uso:
  php qzy <comando> [opcoes]

Comandos:
  make:component   Gera um componente page ou plugin
  make:controller  Gera um controller e adiciona uma rota
  route:list       Lista as rotas conhecidas do projeto
  check            Executa checagens locais de estrutura
TEXT;
  }

  private function makeComponentHelp(): string
  {
    return <<<TEXT
Uso:
  php qzy make:component [nome] [--type=page|plugin] [--shortcut=yes|no] [--shortcut-name=camelCase] [--no-interaction]
TEXT;
  }

  private function makeControllerHelp(): string
  {
    return <<<TEXT
Uso:
  php qzy make:controller [nome] [--route-file=web|api|dev|test] [--http-method=GET] [--path=/rota] [--action=index] [--no-interaction]
TEXT;
  }

  private function routeListHelp(): string
  {
    return <<<TEXT
Uso:
  php qzy route:list
TEXT;
  }

  private function checkHelp(): string
  {
    return <<<TEXT
Uso:
  php qzy check [--only=routes|components|config] [--strict] [--format=text|json]
TEXT;
  }
}
