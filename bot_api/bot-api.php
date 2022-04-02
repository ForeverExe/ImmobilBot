<?php
  require_once('fetch_api.php');

  class TelegramBot {

    protected $botId;

    function __construct($botId){
      $this->botId = $botId;
    }

    private function  _getApiMethodUrl($methodName){
      return "https://api.telegram.org/bot$this->botId/$methodName";
    }

    public function getMe(){
      return json_decode(fetch($this->_getApiMethodUrl("getMe"), 'POST'));
    }

    public function getUpdate(){
      return json_decode(fetch($this->_getApiMethodUrl("getUpdates"), 'POST'));
    }
  }

?>