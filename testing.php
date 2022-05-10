<?php 
require_once "./bot_api/fetch_api.php";
require_once "./bot_api/bot-api.php";

$bot = new TelegramBot("5237718388:AAGZBi5qCrLIH6KgT8P2jYi3ZZ69R71HCjk");

$bot->setStatus($chatID, "/somma:primoN", "{num1:5}");

var_dump($bot->getVars($chatID))
?>