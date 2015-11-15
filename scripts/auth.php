<?php  

  function get_guid(){
    return file_get_contents("https://www.uuidgenerator.net/api/guid");
  }

  function get_random_password(){
    $result = file_get_contents("https://passwd.me/api/1.0/get_password.json?type=random&length=8&charset=LOWERCASEALPHANUMERIC");
    $json = json_decode($result);
    $password = $json->password;
    return $password;
  }
  
?>