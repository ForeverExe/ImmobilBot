<?php
  $json = file_get_contents('php://input');
  
  $request = json_decode($json, true);

  file_put_contents("hook.log", $json, FILE_APPEND);
?>