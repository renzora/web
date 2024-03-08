<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ajax/helpers/inputCheck.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if(!$auth) {

  $register_username = clean($_POST['register_username']);
  $register_password = clean($_POST['register_password']);
  $register_email = clean($_POST['register_email']);

  if($register_username == '' or $register_password == '' or $register_email == '') {
    echo 'error_1';
  } else {

    $check_username = $db->prepare("SELECT * FROM users WHERE username = :username");
    $check_username->execute([ ':username' => $register_username ]);

    $check_email = $db->prepare("SELECT * FROM users WHERE email = :email");
    $check_email->execute([ ':email' => $register_email ]);

    if($check_username->rowCount() > 0) {
      echo 'username_exists';
    } else if(!preg_match('/^[a-zA-Z0-9._]+$/', $register_username)) {
      echo 'username_invalid';
    } else if(strlen($register_username) > 20) {
      echo 'username_too_long';
    } else if(strlen($register_username) < 3) {
      echo 'username_too_short';
    } else if(strlen($register_password) < 8) {
      echo 'password_too_short';
    } else if($check_email->rowCount() > 0) {
      echo 'email_exists';
    } else if(!filter_var($register_email, FILTER_VALIDATE_EMAIL)) {
      echo 'email_invalid';
    } else {

      $options = [ 'cost' => 8 ];
      $password_hash = password_hash($register_password, PASSWORD_BCRYPT, $options);

      $insert = $db->prepare("INSERT INTO users (username, password, email, ugroup, created, coins, perms, avatar, active, shadow_ban, ban_expire, 2fa, premium, partner, staff, site_mod) VALUES(:username, :password, :email, :ugroup, :created, :coins, :perms, :avatar, :active, :shadow_ban, :ban_expire, :2fa, :premium, :partner, :staff, :site_mod)");
      $insert->execute([
        ':username' => $register_username,
        ':password' => $password_hash,
        ':email' => $register_email,
        ':ugroup' => 1,
        ':created' => time(),
        ':coins' => 0,
        ':perms' => '',
        'avatar' => '',
        'active' => 1,
        ':shadow_ban' => 0,
        ':ban_expire' => 0,
        ':2fa' => '',
        ':premium' => 0,
        ':partner' => 0,
        ':staff' => 0,
        ':site_mod' => 0
      ]);

      $new_id = $db->lastInsertId();

      $payload = [
        'id' => $new_id,
        'username' => $register_username
      ];
      
      $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], 'HS256');
      setcookie("renaccount", $jwt, 2147483647, '/');

      $insert_note = $db->prepare("INSERT INTO notes (profile_uid, note, author, time) VALUES(:uid, :note, :author, :time)");
      $insert_note->execute([ ':uid' => $new_id, ':note' => 'Registered Account', ':author' => 2, ':time' => time() ]);

      echo 'registration_complete';

    }
  }
}
