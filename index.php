<?php

  require_once('bot_api/bot-api.php');

  try{
    $bot = new TelegramBot("5291672767:AAEMtHikTfCm3fLPBIb5kAYPooCtmsuEB1A");
    
    var_dump($bot->getMe());
  }
  catch(ErrorException $e){
    echo $e->getMessage();
  }

?>