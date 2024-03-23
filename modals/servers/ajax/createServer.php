<?php
header('Content-type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ajax/helpers/inputCheck.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ajax/helpers/generateServerKeys.php';

if($auth) {
    $ip = clean($_POST['ip']);
    $port = clean($_POST['port']);

    if(isPublicIP()) {
        $existingServer = $serversCollection->findOne(['ip' => $ip, 'port' => $port]);

        if(!$existingServer) {
            $secretKey = generateSecureKey();
            $serverCode = generateServerCode();
            $serversCollection->insertOne([
                'ip' => $ip,
                'port' => $port,
                'secretKey' => $secretKey,
                'owner' => $user->id,
                'online' => 0,
                'category' => 27,
                'code' => $serverCode
            ]);

            $json = [
                "uno" => $ip,
                "dos" => $port,
                "tres" => $secretKey,
                "message" => "server_created"
            ];
            echo json_encode($json);
        } else {
            $json = [
                "message" => "server_duplicate_exists"
            ];
            echo json_encode($json);
        }
    } else {
        $json = [
            "message" => "ip_not_valid"
        ];
        echo json_encode($json);
    }
}