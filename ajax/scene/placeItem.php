<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include 'calculateCollision.php';

if($auth) {
    $room = $_POST['room'];
    $newItemData = json_decode($_POST['item'], true);

    // Load items.json file
    $itemsJson = file_get_contents('../../assets/json/items.json');
    $items = json_decode($itemsJson, true);

    // Retrieve item_id from newItemData
    $item_id = $newItemData['id'];

    // Check if item_id exists in items.json
    if(isset($items[$item_id])) {
        // Retrieve the room information
        $query = $db->prepare("SELECT items, numX, numY, uid FROM rooms WHERE id = :room_id");
        $query->execute([':room_id' => $room]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Check if the current user is authorized
            if ($result['uid'] == $user->id) {
                // User is authorized
                $currentItems = json_decode($result['items'], true);

                // Add new item data to the current items
                $currentItems['items'][] = $newItemData;

                // Convert updated items to JSON
                $updatedItemsJson = json_encode($currentItems);
                $updatedCollision = updateRoomWithItems($updatedItemsJson, $result['numX'], $result['numY']);
                $updatedCollisionJson = json_encode($updatedCollision);


                // Update the database
                $updateQuery = $db->prepare("UPDATE rooms SET items = :items, collision = :collision WHERE id = :room_id && uid = :uid");
                $updateQuery->execute([':items' => $updatedItemsJson, ':room_id' => $room, ':collision' => $updatedCollisionJson, ':uid' => $user->id ]);

                echo json_encode([
                    "status" => "success",
                    "message" => "Item added successfully",
                    "updatedRoom" => $currentItems,
                    "collisionMap" => $updatedCollision
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "User not authorized to modify this room"
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Room not found"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Item ID not found in items.json"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "User not authenticated"
    ]);
}
?>