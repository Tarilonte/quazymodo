<?php

namespace Controller\sse;

use Quazymodo\AbstractController;

class HoracertaSseControler extends AbstractController
{
  public function index()
  {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    while (true) {
      $message = 'event: horacerta' . PHP_EOL;
      $message .= 'data: ' . date('H:i:s') . PHP_EOL . PHP_EOL;
      echo $message;
      ob_flush();
      flush();
      sleep(1);
    }  
  }

}
