<?php

return [
  'template' => 'animatedBackground',
  'css' => 'animatedBackground.css',
  'inserts' => [
    [
      'slot' => 'animatedBackground-class',
      'content' => 'bg-gradient-to-b from-secondary/40 to-accent/30',
    ],
    [
      'slot' => 'body',
      'content' => 'TESTE',
    ]
  ] 
];