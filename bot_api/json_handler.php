<?php
  require_once "./fetch_api.php";
  require_once "./bot-api.php";
  //I NULL SONO EQUIVALENTI AI DEFAULT PER I COMANDI NON RICONOSCIUTI

  $bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");

  $json = file_get_contents('php://input');
  $result = file_put_contents("hook.log", $json, FILE_APPEND);
  $last = file_put_contents("last.json", $json);

  //ottengo l'oggetto json da utilizzare
  $request = json_decode($json, false);
  $chatID = $request->message->chat->id;
  $text = $request->message->text;
  
  $status = $bot->checkStatus($chatID);
  
  //semplice comando fuori dal "Sistema Status", se viene usato questo comando annulla l'operazione in caso di adempimento
  if($text == "/stop"){
    $bot->sendMessage($chatID, "Operazione Interrotta!");
    $bot->setStatus($chatID);
  }else{
    //struttura di gestione, prima controla che ci siano dei comandi in sospeso, altrimenti vai con i comandi in
    //fase iniziale
    if($status != null){
      switch($status[0]){
        case "/somma":{
          switch($status[1]){
            //crea l'array per il json ed inserisco il numero
            case "primoN":{
              settype($text, "integer");
              $num = intval($text);
              $bot->setStatus($chatID, "/somma:secondoN", json_encode(array("num1" => $num)));
              $bot->sendMessage($chatID, "Inserisci il secondo numero da sommare");
              break;
            }
            //usa il json e somma
            case "secondoN":{
              settype($text, "integer");
              $num2 = intval($text);

              $json = json_decode($bot->getVars($chatID));

              $bot->sendMessage($chatID, "La somma dei due numeri è ".$num2+$json->num1);
              $bot->setStatus($chatID);
              break;
            }
            default:{
              $bot->sendMessage($chatID, "Fase non esistente.");
              break;
            }
          } 
          break;
        }
        case "/login":{
          switch($status[1]){
            case "mail":{
              $bot->setStatus($chatID, "/login:passw", json_encode(array("mail" => $text)));
              $bot->sendMessage($chatID, "Inserisci la password:");
              break;
            }
            case "passw:":{
              
              break;
            }
          }
          break;
        }
        default:{
          $bot->sendMessage($chatID, "Comando non esistente.");
          break;
        }
      }
    }else{
      switch($text){
        case "/start":{
          $bot->sendMessage($chatID, "ImmobilBot V0.5 - By Matteo Besutti 5I
          /elencaImmobili - Elenca gli immobili presenti sulla piattaforma
          /login - Effettua il login
          /logout - Effettua il logout
          /stop - Interrompi un comando a più fasi (ad esempio il login)
          /source - Link al git del bot");
          break;
        }
        case "/printa":{
          $bot->sendMessage($chatID, "Scrivi la stringa da stampare");
          break;
        }
        case "/login":{
          $bot->sendMessage($chatID, "Inserisci la password utente:");
          $bot->setStatus($chatID, "/login:mail");
          break;
        }
        case "/somma":{
          $bot->sendMessage($chatID, "Inserisci il primo numero da sommare");
          $bot->setStatus($chatID, "/somma:primoN");
          break;
        }
        case "/elencaImmobili":{
          $db = new mysqli("localhost", "root", "", "p73e6");
          $sql = "SELECT i.*, t.nome as Nome_Tipologia, z.nome as Nome_Zona FROM p73e6_immobile as i, p73e6_tipologia as t, p73e6_zona as z WHERE i.idZona = z.id AND i.idTipologia = t.id";
          $rs = $db->query($sql);
          $result = $rs->fetch_assoc();
          while($result != null){
            $nome = $result['Nome_Casa'];
            $via = $result['via'];
            $civico = $result['civico'];
            $piani = $result['piano'];
            $metratura = $result['Metratura'];
            $locali = $result['locali'];
            $tipo = $result['Nome_Tipologia'];
            $zona = $result['Nome_Zona'];

            $bot->sendMessage($chatID, "
            $nome
            Via: $via, $civico
            Piani: $piani - Metratura: $metratura mq
            Locali: $locali - Zona: $zona - Tipo: $tipo");
          $result = $rs->fetch_assoc();
          }

          $db->close();
          break;
        }
        case "/source":{
          $bot->sendMessage($chatID, "https://github.com/ForeverExe/ImmobilBot");
          break;
        }
        case "null":{
          $bot->sendMessage($chatID, "Comando non riconosciuto. Sei in una fase?");
          break;
        }
        default:{
          $bot->sendMessage($chatID, "Comando non riconosciuto.");
          break;
        }
      }
    }
  }
?>