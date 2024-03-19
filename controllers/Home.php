<?php

$cards = [];
for ($i=1; $i <= 5; $i++) { 
  $card = new pangaTemplater\Component("card",["slot" => $i]);
  $cards[] = $card;
}

$div = new pangaTemplater\Component("div", ["slot" => $cards], "htmlOnly");

$page = new pangaTemplater\Component(
  "page-base",
  [
    "page-title" => "Less is More",
    "body-class" => "flex flex-col gap-6 p-10",
    "body" => [$div]
  ]
  );
die($page->render()->html);