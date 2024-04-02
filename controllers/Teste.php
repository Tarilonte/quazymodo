<?php


$page = new pangaTemplater\Component(
  "page/404",
  [
    "requested-url" => "oi",
    "js" => "card.js",
    "css" => "base.css",
  ],
  "htmlOnly"
);

die($page->flush_assets()->render()->html);