<?php
  $json = file_get_contents('php://input');
  
  $result = file_put_contents("hook.log", $json, FILE_APPEND);
  var_dump($result);
  $request = json_decode($json, true);
?>