<?php
header('Content-type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ajax/helpers/inputCheck.php';

if($auth) {

    $code = clean($_GET['code']);

    $find_code = $db->prepare("SELECT * FROM servers WHERE code = :code");
    $find_code->execute([ ':code' => $code ]);

    if($find_code->rowCount() >0) {
        $show_server = $find_code->fetch(PDO::FETCH_OBJ);

        $json = array(
            "uno" => $show_server->ip,
            "dos" => $show_server->port,
            "message" => "server_found"
        );
        echo json_encode($json);

    } else {
        $json = array(
            "message" => "server_not_found"
        );
        echo json_encode($json);
    }

}