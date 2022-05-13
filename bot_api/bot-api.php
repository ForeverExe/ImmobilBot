<?php
  require_once('fetch_api.php');

  define("BOT_HOST", "localhost");
  define("BOT_USER", "root");
  define("BOT_PASS", "");
  define("BOT_DATA", "botTelegram");

  define("IMMO_HOST", "localhost");
  define("IMMO_USER", "root");
  define("IMMO_PASS", "");
  define("IMMO_DATA", "p73e6");

  class TelegramBot {
    protected $botId;
    protected $url;

    function __construct($botId){
      $this->botId = $botId;
    }

    private function  _getApiMethodUrl($methodName){
      return "https://api.telegram.org/bot$this->botId/$methodName";
    }

    public function getMe(){
      return json_decode(fetch($this->_getApiMethodUrl("getMe"), 'POST'));
    }

    public function getUpdates(){
      return json_decode(fetch($this->_getApiMethodUrl("getUpdates"), 'POST'));
    }

    public function setWebhook($argurl){
      $this->url = $argurl;
      return json_decode(fetch($this->_getApiMethodUrl("setWebhook"), 'POST', array(
        "url" => $argurl
      )));
    }

    public function getWebhookInfo(){
      if(isset($this->url)){
        return json_decode(fetch($this->_getApiMethodUrl("getWebhookInfo")));
      }else
        echo("Webhook not set");
    }

    public function sendMessage($chatId, $text){
      fetch($this->_getApiMethodUrl("sendMessage"), 'POST', array(
        "chat_id" => $chatId,
        "text" => $text
      ));
    }
    public function sendHTMLMessage($chatId, $text){
      fetch($this->_getApiMethodUrl("sendMessage"), 'POST', array(
        "chat_id" => $chatId,
        "text" => $text,
        "parse_mode" => "HTML"
      ));
    }

    /**
     * Controlla un determinato status nel DB
     * @return Array Array della fase, [0] = comando [1] = fase
     * @return Null In caso non sia presente uno stato nel DB
     */
    public function checkStatus($chatID){
      $db = new mysqli(BOT_HOST, BOT_USER, BOT_PASS, BOT_DATA);
      $sql = "SELECT * FROM status WHERE chatid = $chatID";
      $rs = $db->query($sql);
      if($rs->num_rows != 0){
        $str = $rs->fetch_assoc();
        $result = explode(":", $str['stato']); //ritorna l'array se e' presente
        if($result[0] != "null")
          return $result;
        else
          return null;
      }else{
        return null; //ritorna null se vuoto
      }
      $db->close();
    }

    /**
     * Imposta uno status nel DB, controlla la presenza e se c'e' aggiorna invece di crearlo
     *  *Attenzione*: $fase e $vars sono null di default, se viene chiamata la funzione senza indicare gli argomenti questi verranno cancellati dal db
     * @param int chatID Id della chat interessata
     * @param string fase Fase da inserire
     * @param string variabili Json codificato in stringa contenente variabili
     */
    public function setStatus($chatID, $fase = "null", $vars = "null"){
      $db = new mysqli(BOT_HOST, BOT_USER, BOT_PASS, BOT_DATA);
      //se presente nel db -> update
      if($db->query("SELECT * FROM status WHERE chatid = $chatID")->num_rows != 0){

        $sql = "UPDATE status SET stato = '$fase', variabili='$vars' WHERE chatid = $chatID";
        if($db->query($sql) == false){
          echo($db->error_log);
        }
      //altrimenti crea
      }else{
        $sql = "INSERT INTO status (chatid, stato, variabili) VALUES($chatID, '$fase', '$vars')";
        if($db->query($sql) == false){
          echo($db->error_log);
        }
      }

      $db->close();
    }

    /**
     * Ritorna le variabili dalla fase, dato una determinata chat. E' una stringa json da fare con encode/decode
     * @param int chatID id della chat interessata
     */
    public function getVars($chatID){
      $db = new mysqli(BOT_HOST, BOT_USER, BOT_PASS, BOT_DATA);
      $rs = $db->query("SELECT * FROM status WHERE chatid = $chatID");
      if($rs->num_rows != 0){
        $result = $rs->fetch_assoc();
        return $result['variabili'];
      }else{
        return null;
      }

      $db->close();
    }

    /**
     * Imposta le variabili della fase data una chat. E' una stringa json da fare con encode/decode
     * *ATTENZIONE* Di default $vars equivale a null, chiamando la funzione senza inserire argomenti cancellera' le variabili attualmente sul DB 
     * @param int chatID id della chat interessata
     * @param string vars stringa univoca contenente stringa da usare come json
     */
    public function setVars($chatID, $vars = null){
      $db = new mysqli(BOT_HOST, BOT_USER, BOT_PASS, BOT_DATA);
      //controllo presenza riga
      if($db->query("SELECT * FROM status WHERE chatid = $chatID")->num_rows == 0){
        fetch($this->_getApiMethodUrl("sendMessage"), 'POST', array(
          "chat_id" => $chatID,
          "text" => "Errore nell'inserimento delle variabili, riprovare."
        ));
        //update in caso di presenza di tupla
      }else{
        $db->query("UPDATE status SET variabili = '$vars' WHERE chatid = $chatID");
      }

      $db->close();
    }
  }

?>