<?php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use MongoDB\Client as MongoClient;

try {
    $mongoClient = new MongoClient("mongodb://" . $_ENV['DB_HOST'] . ":" . $_ENV['DB_PORT'], [
        "username" => $_ENV['MONGO_INITDB_ROOT_USERNAME'],
        "password" => $_ENV['MONGO_INITDB_ROOT_PASSWORD']
    ]);

    $db = $mongoClient->selectDatabase($_ENV['DB_DATABASE']);
} catch (Exception $e) {
    print "Error: " . $e->getMessage();
    die();
}

if (!isset($_COOKIE['renaccount'])) {
    $auth = FALSE;
} else {
    try {
        $decoded = JWT::decode($_COOKIE['renaccount'], new \Firebase\JWT\Key($_ENV['JWT_KEY'], 'HS256'));
        $user = $decoded;
        $auth = TRUE;
    } catch (Exception $e) {
        setcookie('renaccount', null, -1, '/');
        $auth = FALSE;
    }
}
?>