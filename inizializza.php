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
    $bot->setStatus(798028646, "/somma:primoN", "{num1:5}");

  }
  catch(ErrorException $e){
    echo $e->getMessage();
  }

?>