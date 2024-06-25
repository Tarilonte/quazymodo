<?php

$content = [
  "page-title" => $_ENV["SITE_NAME"] . " - Teste Modal",
  "body" => [
            new quazyTemplater\Component("modal-01"),
            new quazyTemplater\Component("test/modal", [], "htmlOnly")
  ],
  "js" => ["teste-modal.js"]
];

$page = new quazyTemplater\Component("page-base", $content);

die($page->render()->html);