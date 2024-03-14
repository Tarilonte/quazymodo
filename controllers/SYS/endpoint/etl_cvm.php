<?php

$start = microtime(true);

use GuzzleHttp\Client;
use League\Csv\Reader;
use League\Csv\Statement;

// Captura o nome do arquivo a ser processado
$fileName = $_GET['file'] ?? die('Nenhum arquivo informado.');

// Busca no sistema as informações sobre o arquivo a ser processado
$fileSpec = (setDatabase('apoio'))->get('VIEW_ultima_atualizacao', '*',['arquivo_nome'=>$fileName]);
if (!$fileSpec) die("Não existe procedimento para tratamento do arquivo $fileName");


// Captura o conteúdo do arquivo CSV a ser processado
$csvString = getCsv($fileSpec);
$csvString = mb_convert_encoding($csvString, 'UTF-8', 'Windows-1252');


// Cria um objeto Reader do League\Csv
$csv = Reader::createFromString($csvString);
$csv->setHeaderOffset(0); 
$csv->setDelimiter(';');
//$stmt = (new Statement())->limit(2);
$stmt = new Statement();
$records = $stmt->process($csv);

/*
pangaFunctions\show($fileSpec);
die();
foreach ($records  as $record) {
  pangaFunctions\show($record);
}
*/

saveRecords();


function getCsv(): string
{
  global $fileSpec;
  if ($fileSpec['zip_nome'] != null) {
    return getCsvFromZip($fileSpec);
  }

  // Acessa a URL e captura a listagem dos arquivos disponíveis
  $client = new Client();
  $response = $client->request('GET', $fileSpec['URL']);
  $body = (string) $response->getBody();

  // Localiza e captura o último arquivo dentro da listagem
  $pattern = '/href="('.$fileSpec['arquivo_nome'].'[_\d]*\.csv)"/i';
  preg_match_all($pattern, $body, $matches);
  $csv_fullName = end($matches[1]);
  $csv_fullURL = $fileSpec['URL'].$csv_fullName;
  $client = new Client();
  $response = $client->request('GET', $csv_fullURL);
  if ($response->getStatusCode() == 200) {
    $fileSpec['current_csv'] = "$fileSpec[arquivo_nome].csv";
    return (string) $response->getBody();
  }
  die("Não foi possível obter o arquivo CSV.\n$csv_fullURL");
}

function getCsvFromZip(): string
{
  global $fileSpec;
  // Acessa a URL e captura a listagem dos arquivos ZIP disponíveis
  $client = new Client();
  $response = $client->request('GET', $fileSpec['URL']);
  $body = (string) $response->getBody();
  
  // Localiza e captura o último arquivo ZIP dentro da listagem
  $pattern = '/href="('.$fileSpec['zip_nome'].'.*\.zip)"/i';
  preg_match_all($pattern, $body, $matches);
  $zip_fullName = end($matches[1]);
  $zip_fullURL = $fileSpec['URL'].$zip_fullName;
  $response = $client->get($zip_fullURL);

  // Descompacta o arquivo ZIP e captura o conteúdo do arquivo CSV desejado
  $zipContent = $response->getBody()->getContents();
  $tempFilePath = tempnam(sys_get_temp_dir(), 'zip');
  file_put_contents($tempFilePath, $zipContent);
  $zip = new ZipArchive;
  if ($zip->open($tempFilePath) === TRUE) {
    $pattern = "/{$fileSpec['arquivo_nome']}[_\d]*\.csv/i";
    for ($i = 0; $i < $zip->numFiles; $i++) {
      $filename = $zip->getNameIndex($i);        
      if (preg_match($pattern, $filename)) {
        $fileContents = $zip->getFromName($filename);
        $fileSpec['current_csv'] = $filename;
      }
    }
    $zip->close();
    unlink($tempFilePath);
    return $fileContents;
  } else {
    die("Não foi possível obter o arquivo ZIP.\n$zip_fullURL");
  }
}

function saveRecords()
{
  global$fileSpec, $records, $getDigits;
  $database = $fileSpec['db_nome'];
  $tabela = $fileSpec['tabela'];
  $DB = setDatabase($database);

  // Apaga os registros preexistentes na tabela, caso o arquivo esteja sendo reprocessado
  if($fileSpec['current_csv'] == $fileSpec['csv_filename']) {
    $DB->delete($tabela, ['id_atualizacao' => $fileSpec['id_ultima_atualizacao']]);
  }

  // Grava os novos registros na tabela
  $batch = [];
  $batchSize = 1000;
  $qtRegistros = 0;
  foreach ($records as $record) {
    foreach ($record as $key => $value) {
      if (substr($key, 0, 4) == 'CNPJ') $value = $getDigits($value);
      $value = (strlen($value) == 0) ? NULL : $value;
      $record[$key] = $value;
    }
    $batch[] = $record;
    if (count($batch) == $batchSize) {
      $insert = medoo_insert($DB, $tabela, $batch);
      $qtRegistros = $qtRegistros + $insert['rowCount'];
      $batch = [];
    }  
  }
  if (count($batch) > 0) {    
    $insert = medoo_insert($DB, $tabela, $batch);
    $qtRegistros = $qtRegistros + $insert['rowCount'];
  }
  updateAcompanhamento($qtRegistros);
}

function updateAcompanhamento($qtRegistros)
{
  global $fileSpec, $start;
  $executionTime = microtime(true) - $start;
  $atualizacaoInfo = [
    'db_nome' => $fileSpec['db_nome'],
    'tabela' => $fileSpec['tabela'],
    'procedimento' => 'atualização',
    'script' => $_SERVER['REQUEST_URI'],
    'data_hora' => date('Y-m-d H:i:s'),
    'qt_registros' => $qtRegistros,
    'tempo_execucao' => intval($executionTime),
    'csv_filename' => $fileSpec['current_csv'],
    'cod_arquivo_externo' => $fileSpec['cod_arquivo_externo']
  ];
  $update = medoo_insert(setDatabase('apoio'), 'atualizacoes_efetuadas', $atualizacaoInfo);
  insert_updateID($update['insertId']);
  $atualizacaoInfo['id_atualizacao'] = $update['insertId'];
  pangaFunctions\show($atualizacaoInfo,'$atualizacaoInfo');
  die();
}

function insert_updateID($updateID)
{
  global $fileSpec;
  $database = $fileSpec['db_nome'];
  $tabela = $fileSpec['tabela'];
  $DB = setDatabase($database);
  $DB->update($tabela,['id_atualizacao' => $updateID],['id_atualizacao' => 0]);
}