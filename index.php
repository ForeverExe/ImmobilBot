<?php
  require_once('bot_api/bot-api.php');

  try{
    $bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");
    
    var_dump($bot->getMe());
    echo("<br/> <br/>");
    //var_dump($bot->getUpdates());
    echo("<br/> <br/>");
    var_dump($bot->setWebhook("https://a4cc-5-170-0-25.eu.ngrok.io/ImmobilBot/bot_api/json_handler.php"));
    echo("<br/> <br/>");
    var_dump($bot->getWebhookInfo());
    echo("<br/> <br/>");

    if(isset($_POST['sendChat'])){
      
    }
  }
  catch(ErrorException $e){
    echo $e->getMessage();
  }

?>