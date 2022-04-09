<?php
  require_once('fetch_api.php');

  class TelegramBot {

    protected $chatId;
    protected $botId;
    protected $url;

    function __construct($botId){
      $this->botId = $botId;
      $this->chatId = $this->getChatId();
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

    /**
     * Prende un json dell'update e prende la sottostringa contenete l'id della chat,
     * da avviare idealmente in ogni /start da parte dell'utente
     */
    public function getChatId(){
      $str = $this->getUpdates();
      
    }
  }

?>