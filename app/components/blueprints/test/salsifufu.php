<?php

$effects = ['rubberBand', 'backInDown', 'bounceInDown', 'heartBeat', 'flip', 'lightSpeedInLeft', 'zoomInUp','jackInTheBox'];
$effect = $effects[array_rand($effects)];

return [
  'template' => 'salsifufu',
  'css' => [
    'salsifufu.css',
    'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
  ],
  'js' => 'salsifufu.js',
  'inserts' => [
    'effect' => $effect
  ],
];