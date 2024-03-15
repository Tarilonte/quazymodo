<?php

$page = new pangaTemplater\Component(
  "page-base",
  [
    "page-title" => "Less is More",
    "body" => new pangaTemplater\Component(
      componentName:"page/welcome",
      componentType:"simpleTemplate"      
    )
  ]
  );
die($page->render()->html);