<?php

/**
 * Monta os cards de resumo
 */
require_once '../data/Model/Resumo.php';
$resumos = Model\Resumo::getLast(15);
foreach ($resumos as $resumo) {
  $resumo['doc_categoria'] = ($resumo['doc_categoria'] == 'Comunicado ao Mercado') ? 'Comunicado' : $resumo['doc_categoria'];
  $resumo['card-css'] = ($resumo['doc_categoria'] == 'Fato Relevante') ? 'primary' : 'secondary';
  $resumo['doc_data_entrega'] = pangaFunctions\dateFormat($resumo['doc_data_entrega'],'dd MMM yyyy'); 
  $cards[] = new pangaTemplater\Component(
    "card-resumo",
    $resumo
  );
}

/**
 * Monta a página principal
 */
$page = new pangaTemplater\Component(
  "page-base",
  [
    "page-title" => "mundofii ",
    "body" => [ new pangaTemplater\Component('navbar-01',[]),
                new pangaTemplater\Component('page/home',[],'simpleTemplate')],
    "body-class" => "flex flex-col min-h-dvh",
    "resumos" => new pangaTemplater\Component('carousel-Homepage',["slides" => $cards]),
  ]
  );
die($page->render()->html);