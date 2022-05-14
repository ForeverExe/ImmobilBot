<?php
  require_once('bot_api/bot-api.php');

  try{   
    var_dump($bot->getMe());
    echo("<br/> <br/>");
    //var_dump($bot->getUpdates());
    echo("<br/> <br/>");
    var_dump($bot->setWebhook("https://1968-5-170-128-18.ngrok.io/ImmobilBot/bot_api/json_handler.php"));
    echo("<br/> <br/>");
    //var_dump($bot->getWebhookInfo());
    echo("<br/> <br/>");
  }
  catch(ErrorException $e){
    echo $e->getMessage();
  }

?>