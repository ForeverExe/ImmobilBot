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

    public function checkStatus($chatID){
      $db = new mysqli("localhost", "root", "", "telegrambot");
      $sql = "SELECT 'fase' FROM 'status' WHERE 'chatid' = $chatID";
      $rs = $db->query($sql);
      if($rs->num_rows != 0){
        $str = $rs->fetch_assoc();
        return $result = explode(":", $str['fase']);
      }else{
        return null;
      }
      $db->close();
    }

    public function setStatus($chatID, $fase, $vars = null){
      $db = new mysqli("localhost", "root", "", "telegrambot");
      $sql = "INSERT INTO status('chatid', 'fase', 'variabili') VALUES ($chatID, $fase, $vars)";
      if($db->query($sql) == false){
        fetch($this->_getApiMethodUrl("sendMessage"), 'POST', array(
          "chat_id" => $chatID,
          "text" => "Errore nell'inserimento della fase, riprovare."
        ));
      }


      $db->close();
    }
  }

?>