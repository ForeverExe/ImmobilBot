<?php
  require_once('bot_api/bot-api.php');

  try{
    $bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");
    
    var_dump($bot->getMe());
    echo("<br/> <br/>");
    var_dump($bot->getUpdates());
    echo("<br/> <br/>");
    var_dump($bot->setWebhook("https://b650-5-170-5-16.ngrok.io"));
  }
  catch(ErrorException $e){
    echo $e->getMessage();
  }

?>