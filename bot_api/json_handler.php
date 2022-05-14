<?php
  require_once "./fetch_api.php";
  require_once "./bot-api.php";

  define("BOT_HOST", "localhost");
  define("BOT_USER", "root");
  define("BOT_PASS", "");
  define("BOT_DATA", "botTelegram");

  define("IMMO_HOST", "localhost");
  define("IMMO_USER", "root");
  define("IMMO_PASS", "");
  define("IMMO_DATA", "p73e6");

  $bot = new TelegramBot("Your API key Here");

  $json = file_get_contents('php://input');
  $result = file_put_contents("hook.log", $json, FILE_APPEND);
  $last = file_put_contents("last.json", $json);

  //ottengo l'oggetto json da utilizzare ed i dati principali
  $request = json_decode($json, false);
  $chatID = $request->message->chat->id;
  $text = $request->message->text;
  
  //controllo dello status con ritorno
  $status = $bot->checkStatus($chatID);
  
  //semplice comando fuori dal "Sistema Status", se viene usato questo comando annulla l'operazione in caso di adempimento
  if($text == "/stop"){
    $bot->sendMessage($chatID, "Operazione Interrotta!");
    $bot->setStatus($chatID);

  }else{
    //struttura di gestione, prima controlla che ci siano dei comandi in sospeso, 
    //altrimenti vai con i comandi in fase iniziale
    if($status != null){
      //sistema di scelte dei comandi e le loro fasi
      switch($status[0]){
        //fasi della somma
        case "/somma":{
          switch($status[1]){
            //crea l'array per il json ed inserisco il numero
            case "primoN":{
              settype($text, "integer");
              $num = intval($text);
              $bot->setStatus($chatID, "/somma:secondoN", json_encode(array("num1" => $num)));
              $bot->sendMessage($chatID, "Scrivi il secondo numero da sommare");
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
        //fasi login
        case "/login":{
          switch($status[1]){
            case "mail":{
              $bot->setStatus($chatID, "/login:passw", json_encode(array("mail" => $text)));
              $bot->sendMessage($chatID, "Inserisci la password:");
              break;
            }
            case "passw":{ //problema nell'if a riga 79
              $mail = json_decode($bot->getVars($chatID));
              $db = new mysqli(IMMO_HOST, IMMO_USER, IMMO_PASS, IMMO_DATA);
              $sql = "SELECT id, nome, cognome FROM p73e6_proprietario WHERE mail = '$mail->mail' AND passwd = '".md5($text)."'";
              $rs = $db->query($sql);
              if($rs->num_rows == 1){
                $result = $rs->fetch_assoc();
                $nome = $result['nome'];
                $cognome = $result['cognome'];
                $bot->sendMessage($chatID, "Sei loggato come: $cognome $nome");

                $sql = "UPDATE p73e6_proprietario SET loggato = $chatID WHERE id = ".$result['id'];
                if(!$db->query($sql)){
                  $bot->sendMessage($chatID, "Errore nell'UPDATE");
                }

                $bot->setStatus($chatID);
              }else{
                $bot->sendMessage($chatID, "Credenziali errate o mancanti, reinserisci la mail e la password.");
                $bot->setStatus($chatID, "/login:mail");
              }
              $db->close();
              break;
            }
          }
          break;
        }
        //fasi elenchi filtrati
        case "/elencoFiltered":{
          switch($text){
            case "/alfa":{
              $db = new mysqli(IMMO_HOST, IMMO_USER, IMMO_PASS, IMMO_DATA);
              $sql = "SELECT i.*, i.nome as Nome_Casa, t.nome as Nome_Tipologia, z.nome as Nome_Zona FROM p73e6_immobile as i, p73e6_tipologia as t, p73e6_zona as z WHERE i.idZona = z.id AND i.idTipologia = t.id ORDER BY Nome_Casa ASC";
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
    
                $bot->sendMessage($chatID, "$nome
                Via: $via, $civico
                Piani: $piani - Metratura: $metratura mq
                Locali: $locali - Zona: $zona - Tipo: $tipo");
              $result = $rs->fetch_assoc();
              }
              $db->close();
              $bot->setStatus($chatID);
              break;
            }
            case "/reddito":{
              $db = new mysqli(IMMO_HOST, IMMO_USER, IMMO_PASS, IMMO_DATA);
              $sql = "SELECT i.*, i.nome AS Nome_Casa, t.nome AS Nome_Tipologia, z.nome AS Nome_Zona, r.prezzo_mq
              FROM p73e6_immobile AS i, p73e6_tipologia AS t, p73e6_zona AS z, p73e6_rendita_annua AS r
              WHERE i.idZona = z.id AND i.idTipologia = t.id AND r.idZona = z.id AND r.idTipologia = t.id AND r.anno = 2022
              ORDER BY r.prezzo_mq ASC";
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
                $reddito = $result['prezzo_mq'];
    
                $bot->sendMessage($chatID, "$nome
                Via: $via, $civico
                Piani: $piani - Metratura: $metratura mq - Prezzo al mq: $reddito
                Locali: $locali - Zona: $zona - Tipo: $tipo");
              $result = $rs->fetch_assoc();
              }
              $db->close();
              $bot->setStatus($chatID);
              break;
            }
            case "/zona":{
              $db = new mysqli(IMMO_HOST, IMMO_USER, IMMO_PASS, IMMO_DATA);
              $sql = "SELECT i.*, t.nome AS Nome_Tipologia, z.nome AS Nome_Zona
              FROM p73e6_immobile AS i, p73e6_tipologia AS t, p73e6_zona AS z
              WHERE i.idZona = z.id AND i.idTipologia = t.id
              ORDER BY z.nome ASC";
              $rs = $db->query($sql);
              $result = $rs->fetch_assoc();
              while($result != null){
                $nome = $result['nome'];
                $via = $result['via'];
                $civico = $result['civico'];
                $piani = $result['piano'];
                $metratura = $result['Metratura'];
                $locali = $result['locali'];
                $tipo = $result['Nome_Tipologia'];
                $zona = $result['Nome_Zona'];
    
                $bot->sendMessage($chatID, "$nome
                Via: $via, $civico
                Piani: $piani - Metratura: $metratura mq - Prezzo al mq: $reddito
                Locali: $locali - Zona: $zona - Tipo: $tipo");
              $result = $rs->fetch_assoc();
              }
              $db->close();
              $bot->setStatus($chatID);
              break;
            }
            case "/tipo":{
              $db = new mysqli(IMMO_HOST, IMMO_USER, IMMO_PASS, IMMO_DATA);
              $sql = "SELECT i.*, t.nome AS Nome_Tipologia, z.nome AS Nome_Zona
              FROM p73e6_immobile AS i, p73e6_tipologia AS t, p73e6_zona AS z
              WHERE i.idZona = z.id AND i.idTipologia = t.id
              ORDER BY t.nome ASC";
              $rs = $db->query($sql);
              $result = $rs->fetch_assoc();
              while($result != null){
                $nome = $result['nome'];
                $via = $result['via'];
                $civico = $result['civico'];
                $piani = $result['piano'];
                $metratura = $result['Metratura'];
                $locali = $result['locali'];
                $tipo = $result['Nome_Tipologia'];
                $zona = $result['Nome_Zona'];
    
                $bot->sendMessage($chatID, "$nome
                Via: $via, $civico
                Piani: $piani - Metratura: $metratura mq - Prezzo al mq: $reddito
                Locali: $locali - Zona: $zona - Tipo: $tipo");
              $result = $rs->fetch_assoc();
              }
              $db->close();
              $bot->setStatus($chatID);
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
      //comandi iniziali
      switch($text){
        case "/start":{
          $bot->sendMessage($chatID, "ImmobilBot V0.5 - By Matteo Besutti 5I
          /elencoFiltered - Scopri gli immobili secondo diversi filtri
          /login - Effettua il login [WIP]
          /logout - Effettua il logout
          /stop - Interrompi un comando a più fasi (ad esempio il login)
          /app - Link all'applicativo
          /source - Link al git del bot
          /somma - Fai la somma di due numeri");
          break;
        }
        case "/login":{
          $bot->sendMessage($chatID, "Login utente - Sei un amministratore? Digita \"/stop\" e successivamente \"/app\" per collegarti all'applicativo dove gestire utenti ed immobili!");
          $bot->sendMessage($chatID, "Scrivi la mail utente:");
          $bot->setStatus($chatID, "/login:mail");
          break;
        }
        case "/logout":{
          $db = new mysqli(IMMO_HOST, IMMO_USER, IMMO_PASS, IMMO_DATA);
          $sql = "SELECT nome, cognome FROM p73e6_proprietario WHERE loggato = $chatID";
          $rs = $db->query($sql);
          if($rs->num_rows > 0){
            $result = $rs->fetch_assoc();
            $nome = $result['nome'];
            $cognome = $result['cognome'];
            $bot->sendMessage($chatID, "Logout effettuato da: \"$nome $cognome\"");

            $sql = "UPDATE p73e6_proprietario SET loggato = NULL WHERE loggato = $chatID";
            if(!$db->query($sql)){
              $bot->sendMessage($chatID, "Errore nel cancellare su 'loggato'");
            }

          }else{
            $bot->sendMessage($chatID, "Hai già effettuato il logout!");
          }
          
          break;
        }
        case "/app":{
          $bot->sendHTMLMessage($chatID, "Copia il link nel tuo browser per entrare nell'applicativo (UI ottimizzata per PC)");
          $bot->sendHTMLMESSAGE($chatID, "<pre>http://localhost/webdeveloping/p73e6</pre>");
          break;
        }
        case "/somma":{
          $bot->sendMessage($chatID, "Scrivi il primo numero da sommare");
          $bot->setStatus($chatID, "/somma:primoN");
          break;
        }
        case "/elencoFiltered":{
          $bot->sendMessage($chatID, "Ricerca Filtrata. Seleziona una delle opzioni:
          - /alfa Alfabetico
          - /reddito Per Reddito (In caso ne abbiano uno)
          - /zona Per Zona
          - /tipo Per Tipo");
          $bot->setStatus($chatID, "/elencoFiltered");
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