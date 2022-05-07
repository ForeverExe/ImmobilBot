<?php
  require_once('bot_api/bot-api.php');

  try{
    $bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");
    
    var_dump($bot->getMe());
    echo("<br/> <br/>");
    //var_dump($bot->getUpdates());
    echo("<br/> <br/>");
<<<<<<< HEAD
    var_dump($bot->setWebhook("https://83bb-5-170-140-63.ngrok.io/ImmobilBot/bot_api/json_handler.php"));
=======
    var_dump($bot->setWebhook("https://a15b-78-138-33-188.ngrok.io/ImmobilBot/bot_api/json_handler.php"));
>>>>>>> 9b1bcc224a2557c9519a34d32406479cd618ef0e
    echo("<br/> <br/>");
    var_dump($bot->getWebhookInfo());
    echo("<br/> <br/>");

    //prova db
    $db = new mysqli("localhost", "root", "", "botTelegram");
    $sql = "SELECT stato FROM status WHERE chatid = 798028646";
    $rs = $db->query($sql);
    if($rs->num_rows != 0){
      $str = $rs->fetch_all();
      echo(explode(":", $str));
    }else{
      echo("problema");
    }

  }
  catch(ErrorException $e){
    echo $e->getMessage();
  }

?>