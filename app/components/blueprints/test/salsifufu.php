<?php

sleep(1); // Simulate a delay for testing purposes

use Quazymodo\CSPManager;

$effects = ['rubberBand', 'backInDown', 'bounceInDown', 'heartBeat', 'flip', 'lightSpeedInLeft', 'zoomInUp','jackInTheBox'];
$effect = $effects[array_rand($effects)];
$nonce = CSPManager::getNonce();

return [
  'template' => 'salsifufu',
  'css' => [
    'salsifufu.css',
    'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
  ],
  'js' => [
    'https://confettijs.org/confetti.min.js [nonce="'.$nonce.'"]',
    'salsifufu.js',
  ],
  'inserts' => [
    'effect' => $effect,
    'nonce' => $nonce ,
  ],
];