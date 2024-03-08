<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

header('Content-Type: application/json');

$find_sprite = $db->prepare("SELECT * FROM users WHERE id = :id");
$find_sprite->execute([ ':id' => $user->id ]);

if($find_sprite->rowCount() == 0) {
    echo 'not_found';
} else {
    $sprite = $find_sprite->fetch(PDO::FETCH_OBJ);

    echo json_encode([
        'avatar' => $sprite->avatar
    ]);

}
?>