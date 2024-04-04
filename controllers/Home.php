<?php

$content = [
  "page-title" => $_ENV["SITE_NAME"],
  "body" => new quazyTemplater\Component("page-home",[])
];

$page = new quazyTemplater\Component("page-base", $content);

die($page->render()->html);