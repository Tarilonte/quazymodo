<?php

$content = [
  "page-title" => $_ENV["SITE_NAME"],
  "body" => new pangaTemplater\Component("page/home",[],"htmlOnly"),
  "logo" => new pangaTemplater\Component("quasi-logo", [],"htmlOnly"),
  "quasi-logo-classes" => "w-32 fill-primary m-auto",
];

$page = new pangaTemplater\Component("page-base", $content);
die($page->render()->html);