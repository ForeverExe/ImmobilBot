<?php
  require_once "./fetch_api.php";
  require_once "./bot-api.php";

  $bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");

  $json = file_get_contents('php://input');
  
  $result = file_put_contents("hook.log", $json, FILE_APPEND);
  var_dump($result);
  file_put_contents("last.json", $json);

  $chatID = $request->message->chat->id;

  $request = json_decode($json, false); //Se lo metti a falso, ritorna un oggeto, se lo metti a vero ritorna un array associativo

  switch($request->message->text){
    case "/ehi":{
      $bot->sendMessage($chatID, "ehi");
      break;
    }
    case "/ciao":{
      $bot->sendMessage($chatID, "Ciao!");
      break;
    }
    case "/printa":{
      $bot->sendMessageReply($chatID, "Scrivi la stringa da stampare");
      break;
    }
    case "/somma":{
      if($bot->checkStatus($chatID) == null){
        $bot->sendMessage($chatID, "Inserisci il primo numero da sommare");
        $bot->setStatus($chatID, "somma:primoN");
      }
      break;
    }
  }

?>