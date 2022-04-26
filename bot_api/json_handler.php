<?php
  require_once "./fetch_api.php";
  require_once "./bot-api.php";

  $bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");

  //salvo in una variabile cio che mi arriva dalle richieste
  $json = file_get_contents('php://input');
  //salvo i messaggi che arrivano
  $result = file_put_contents("hook.log", $json, FILE_APPEND);
  file_put_contents("last.json", $json);

  //ottengo l'oggetto json da utilizzare
  $request = json_decode($json, false); //Se lo metti a falso, ritorna un oggeto, se lo metti a vero ritorna un array associativo
  
  //imposto l'id della chat ed il messaggio che ho in questo momento
  $chatID = $request->message->chat->id;
  $text = $request->message->text;
  
  //varie casistiche dove poi ci saranno i vari comandi,
  //controllo per ogni richiesta se ci sono dei comandi in sospeso nella chat, ritorna il primo valore
  //dell'array per il comando, il secondo e' la fase
  $status = $bot->checkStatus($chatID);


  //struttura di gestione, prima controla che ci siano dei comandi in sospeso, altrimenti vai con i comandi in
  //fase iniziale
  if($status != null){
    switch($status[0]){
      case "/somma":{
        switch($status[1]){
          case "primoN":{
            
            $bot->setStatus($chatID, "/somma:primoN");
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