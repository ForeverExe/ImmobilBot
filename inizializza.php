<?php
  require_once('bot_api/bot-api.php');

  try{
    $bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");
    
    var_dump($bot->getMe());
    echo("<br/> <br/>");
    //var_dump($bot->getUpdates());
    echo("<br/> <br/>");
    var_dump($bot->setWebhook("https://2f7e-5-170-108-213.ngrok.io/ImmobilBot/bot_api/json_handler.php"));
    echo("<br/> <br/>");
    var_dump($bot->getWebhookInfo());
    echo("<br/> <br/>");

    //$json = json_encode(array("primo" => 1));
    //$bot->setStatus(2312310, "primo", $json);
    //16-17 worka

    $var = $bot->getVars(2312310);
    $jasone = json_decode($var, false);
    echo($jasone->primo);
    //3 stringe MUST per ricevere variabili

  }
  catch(ErrorException $e){
    echo $e->getMessage();
  }

?>