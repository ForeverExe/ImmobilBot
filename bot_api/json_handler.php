<?php
  require_once "./fetch_api.php";
  require_once "./bot-api.php";

  $bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");

  //salvo in una variabile cio che mi arriva dalle richieste
  $json = file_get_contents('php://input');
  //salvo i messaggi che arrivano
  $result = file_put_contents("hook.log", $json, FILE_APPEND);
  $last = file_put_contents("last.json", $json);

  //ottengo l'oggetto json da utilizzare
  $request = json_decode($json, false); //Se lo metti a falso, ritorna un oggeto, se lo metti a vero ritorna un array associativo
  //imposto l'id della chat ed il messaggio che ho in questo momento
  $chatID = $request->message->chat->id;
  $text = $request->message->text;
  
  //controllo stato
  $status = $bot->checkStatus($chatID);

  //struttura di gestione, prima controla che ci siano dei comandi in sospeso, altrimenti vai con i comandi in
  //fase iniziale
  if($status != null){
    switch($status[0]){
      case "/somma":{
        switch($status[1]){
          case "primoN":{
            //conversione stringa in numero e controllo tramite intval
            $num = settype($text, "integer");
            $num = intval($num);

            $json = array("primoN"=> $num);
            $bot->setVars($chatID, json_encode($json));
            $bot->setStatus($chatID, "/somma:secondoN");
          }
          case "secondoN":{
            $num2 = settype($text, "integer");
            $num2 = intval($num2);

            $num = json_decode($bot->getVars($chatID));

            $bot->sendMessage($chatID, "La somma di ".$num." e ".$num2."e': ".$num+$num2);
            $bot->setStatus($chatID);
          }
        }
      }
    }
  }else{
    switch($text){
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
        $bot->setStatus($chatID, "/somma:primoN");
        $bot->sendMessage($chatID, "Inserisci il primo numero da sommare");
        break;
      }
      default:{
        $bot->sendMessage($chatID, "Comando non riconosciuto.");
        break;
      }
    }
  }
?>