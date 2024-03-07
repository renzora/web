<?php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$dotenvPath = '/server'; // Path to the shared volume
$dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\KEY;

try {

  $db = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_DATABASE'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

} catch(PDOException $e) {
    print "Error: " . $e->getMessage();
    die();
}

if (!isset($_COOKIE['renaccount'])) {
  $auth = FALSE;
} else {
  try {
    $decoded = JWT::decode($_COOKIE['renaccount'], new Key($_ENV['JWT_KEY'], 'HS256'));
    $user = $decoded;
    $auth = TRUE;
  } catch (Exception $e) {
    setcookie('renaccount', null, -1, '/');
    $auth = FALSE;
  }
}
?>
