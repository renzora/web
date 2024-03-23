<?php
header('Content-type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ajax/helpers/inputCheck.php';

if($auth) {

    $code = clean($_GET['code']);

    $show_server = $serversCollection->findOne(['code' => $code]);

    if($show_server) {
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