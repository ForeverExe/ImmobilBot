<?php
  require_once "./fetch_api.php";
  require_once "./bot-api.php";

  $bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");

  $json = file_get_contents('php://input');
  
  $result = file_put_contents("hook.log", $json, FILE_APPEND);
  var_dump($result);
  $request = json_decode($json, true);

  $bot->sendMessage($request->message->chat->id , $request->message->text);

?>