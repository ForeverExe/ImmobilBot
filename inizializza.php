<?php
  require_once('bot_api/bot-api.php');

  try{
    $bot = new TelegramBot("");
    
    var_dump($bot->getMe());
    echo("<br/> <br/>");
    //var_dump($bot->getUpdates());
    echo("<br/> <br/>");
    var_dump($bot->setWebhook("https://6350-78-138-33-188.ngrok.io/ImmobilBot/bot_api/json_handler.php"));
    echo("<br/> <br/>");
    //var_dump($bot->getWebhookInfo());
    echo("<br/> <br/>");

    //$json = json_encode(array("primo" => 1));
    //$bot->setStatus(2312310, "primo", $json);
    //16-17 worka

    $var = $bot->getVars(798028646);
    $jasone = json_decode($var, false);
    echo($jasone->num1);
    //3 stringe MUST per ricevere variabili

    var_dump($bot->checkStatus(798028646));
    echo("<br/> <br/>");
  }
  catch(ErrorException $e){
    echo $e->getMessage();
  }

?>