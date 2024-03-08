<?php
header('Content-type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ajax/helpers/inputCheck.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ajax/helpers/generateServerKeys.php';

if($auth) {

    $ip = clean($_POST['ip']);
    $port = clean($_POST['port']);

    if(isPublicIP()) {

        $check_server = $db->prepare("SELECT * FROM servers WHERE ip = :ip && port = :port");
        $check_server->execute([ ':ip' => $ip ]);
    
        if($check_server->rowCount() == 0) {
            
            $create_server = $db->prepare("INSERT INTO servers (ip, port, secretKey, owner, online, category, code) VALUES(:server_name, :ip, :port, :secretKey, :owner, :online, :category, :code)");
    
            $create_server->execute([
                ':ip' => $ip,
                ':port' => $port,
                ':secretKey' => generateSecureKey(),
                ':owner' => $user->id,
                ':online' => 0,
                ':category' => 27,
                ':code' => generateServerCode()
            ]);
    
            $json = array(
                "uno" => $show_server->ip,
                "dos" => $show_server->port,
                "tres" => generateSecureKey(),
                "message" => "server_created"
            );
            echo json_encode($json);
    
        } else {
            $json = array(
                "message" => "server_duplicate_exists"
            );
            echo json_encode($json);
        }

    } else {
        $json = array(
            "message" => "ip_not_valid"
        );
        echo json_encode($json);
    }

}