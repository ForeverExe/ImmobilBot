<?php
  require_once('fetch_api.php');
  
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
    public function sendMessageReply($chatId, $text){
      fetch($this->_getApiMethodUrl("sendMessage"), 'POST', array(
        "chat_id" => $chatId,
        "text" => $text,
        "reply_markup" => "ForceReply"
      ));
    }

    /**
     * Controlla un determinato status nel DB
     * @return Array Array della fase, [0] = comando [1] = fase
     * @return Null In caso non sia presente uno stato nel DB
     */
    public function checkStatus($chatID){
      $db = new mysqli("localhost", "root", "", "botTelegram");
      $sql = "SELECT stato FROM status WHERE chatid = $chatID";
      $rs = $db->query($sql);
      if($rs->num_rows != 0){
        $str = $rs->fetch_all();
        return $result = explode(":", $str); //ritorna l'array se e' presente
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
    public function setStatus($chatID, $fase = null, $vars = null){
      $db = new mysqli("localhost", "root", "", "botTelegram");
      
      //se presente nel db -> update
      if($db->query("SELECT * FROM status WHERE chatid")->num_rows != 0){
        $sql = "UPDATE status SET variabili = '$vars' AND stato = '$fase' WHERE chatid = $chatID";
        if($db->query($sql) == false){
          fetch($this->_getApiMethodUrl("sendMessage"), 'POST', array(
            "chat_id" => $chatID,
            "text" => "Errore nell'aggiornamento nella fase '$fase', riprovare."
          ));
        }
      //altrimenti crea
      }else{
        $sql = "INSERT INTO status(chatid, stato, variabili) VALUES ($chatID, $fase, $vars)";
        if($db->query($sql) == false){
          fetch($this->_getApiMethodUrl("sendMessage"), 'POST', array(
            "chat_id" => $chatID,
            "text" => "Errore nell'inserimento della fase '$fase', riprovare."
          ));
        }
      }

      $db->close();
    }

    /**
     * Ritorna le variabili dalla fase, dato una determinata chat. E' una stringa json da fare con encode/decode
     * @param int chatID id della chat interessata
     */
    public function getVars($chatID){
      $db = new mysqli("localhost", "root", "", "botTelegram");
      $rs = $db->query("SELECT variabili FROM status WHERE chatid = $chatID");
      if($rs->num_rows != 0){
        return $rs->fetch_all();
      }else{
        return null;
      }
    }

    /**
     * Imposta le variabili della fase data una chat. E' una stringa json da fare con encode/decode
     * *ATTENZIONE* Di default $vars equivale a null, chiamando la funzione senza inserire argomenti cancellera' le variabili attualmente sul DB 
     * @param int chatID id della chat interessata
     * @param string vars stringa univoca contenente stringa da usare come json
     */
    public function setVars($chatID, $vars = null){
      $db = new mysqli("localhost", "root", "", "botTelegram");
      //controllo presenza riga
      if($db->query("SELECT * FROM status WHERE chatid = $chatID")->num_rows < 1){
        fetch($this->_getApiMethodUrl("sendMessage"), 'POST', array(
          "chat_id" => $chatID,
          "text" => "Errore nell'inserimento delle variabili, riprovare."
        ));
        //update in caso di presenza di tupla
      }else{
        $db->query("UPDATE status SET variabili = $vars WHERE chatid = $chatID");
      }
    }
  }

?>