<?php

namespace quazyFunctions;

function show(mixed $stuff, string $nome = "Não informado")
{
	if (is_array($stuff)) {
		$stuff = escapeArrayValues($stuff);
	} else {
		$stuff = decorateStuff($stuff);
	}

  echo '<div class="mockup-code"><pre class="p-6"><code>';
	echo "[Showing: $nome]" . PHP_EOL;
  print_r($stuff);
  echo '</code></pre></div>';;
}

// função para escapar os valores de um array
function escapeArrayValues($array) 
{
	foreach ($array as $key => $value) {
			// Se o valor for um array, chama a função recursivamente
			if (is_array($value)) {
					$array[$key] = escapeArrayValues($value);
			} else {
					// Aplica htmlspecialchars ao valor
					$array[$key] = decorateStuff($value);
			}
	}
	return $array;
}

function decorateStuff(mixed $stuff): string
{
	$stuffType = gettype($stuff);
	$stuff = htmlspecialchars($stuff);
	$stuff = "$stuff ( $stuffType:".strlen($stuff)." )";
	return $stuff;
}

function recursiveArraySearch($array, $keyToFind) {
	foreach ($array as $key => $value) {
			if ($key === $keyToFind) {
					return $value;
			} elseif (is_array($value)) {
					$result = recursiveArraySearch($value, $keyToFind);
					if ($result !== null) {
							return $result;
					}
			}
	}
	return null;
}

trait EnumFromName {
  public static function tryFromName(string $name): ?static
  {
      $reflection = new \ReflectionEnum(static::class);
      return $reflection->hasCase($name)
          ? $reflection->getCase($name)->getValue()
          : null;
  }
}

$shortDate = new \IntlDateFormatter(
	'pt_BR',
	\IntlDateFormatter::SHORT,
	\IntlDateFormatter::NONE,
	'America/Sao_Paulo',
	\IntlDateFormatter::GREGORIAN,
	$pattern = 'dd MMM yy'
);

function dateFormat($dateInput, $pattern) {
	$formatter = new \IntlDateFormatter(
		'pt_BR', 
		\IntlDateFormatter::FULL, 
		\IntlDateFormatter::FULL, 
		'America/Sao_Paulo',
		\IntlDateFormatter::GREGORIAN, 
		$pattern
	);
	$date = new \DateTime($dateInput);
	return $formatter->format($date);
}


$getDigits = fn($string) => preg_replace('/\D/', '', $string);