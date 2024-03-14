<?php

$template = $router->match()['params']['template'];


$page = new pangaTemplater\Component(
  componentName:"mailing/$template",
  componentType:"simpleTemplate",
  controllerData:[]
);

die($page->html);