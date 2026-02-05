<?php

sleep(1); // Simulate a delay for testing purposes

use Quazymodo\CSPManager;

$effects = ['rubberBand', 'backInDown', 'bounceInDown', 'heartBeat', 'flip', 'lightSpeedInLeft', 'zoomInUp','jackInTheBox'];
$effect = $effects[array_rand($effects)];
$nonce = CSPManager::getNonce();

return [
  'template' => '/pages/test-pages/htmx/salsifufu/salsifufu',
  'css' => [
    'salsifufu.css',
    ASSET_ANIMATECSS
  ],
  'js' => [
    'https://confettijs.org/confetti.min.js [nonce="'.$nonce.'"]',
    'salsifufu.js',
  ],
  'inserts' => [
    'effect' => $effect
  ],
];