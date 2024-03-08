<?php
header('Content-type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ajax/helpers/inputCheck.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if(!$auth) {

  $login_username = clean($_POST['login_username']);
  $login_password = clean($_POST['login_password']);

  if($login_username == '' or $login_password == '') {

    $json = array("message" => "error_1");
    echo json_encode($json);

  } else {

    $check_username = $db->prepare("SELECT * FROM users WHERE username = :username or email = :email");
    $check_username->execute([ ':username' => $login_username, ':email' => $login_username ]);
    $show_username = $check_username->fetch(PDO::FETCH_OBJ);

    if($check_username->rowCount() == 0) {
      $json = array("message" => "user_not_found");
      echo json_encode($json);
    } else {

      if(password_verify($login_password, $show_username->password)) {

        $payload = [
          'id' => $show_username->id,
          'username' => $show_username->username
        ];
        
        $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], 'HS256');
        setcookie("renaccount", $jwt, 2147483647, '/');

        $json = array("message" => "login_complete");
        echo json_encode($json);

      } else {
        $json = array("message" => "incorrect_info");
        echo json_encode($json);
      }
    }
  }
}
