<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include 'calculateCollision.php';

if($auth) {
    $x = $_POST['x'];
    $y = $_POST['y'];
    $itemId = $_POST['item_id'];
    $room_id = $_POST['room_id'];

    // Find the room
    $find_room = $db->prepare("SELECT * FROM rooms WHERE id = :id AND uid = :uid");
    $find_room->execute([':id' => $room_id, ':uid' => $user->id]);

    if($find_room->rowCount() == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Room does not exist']);
    } else {
        $room = $find_room->fetch(PDO::FETCH_ASSOC);
        $itemsData = json_decode($room['items'], true);

        // Initialize variable to store the item_id of the picked-up item
        $pickedUpItemId = null;

        // Search for the item with the specified position and remove it
        foreach ($itemsData['items'] as $key => $item) {
            // Check if the current item's id matches the provided item_id
            if ($item['id'] == $itemId) {
                foreach ($item['position'] as $position) {
                    if ($position['x'] == $x && $position['y'] == $y) {
                        $pickedUpItemId = $item['id']; // Capture the item_id
                        unset($itemsData['items'][$key]);
                        $itemsData['items'] = array_values($itemsData['items']);
                        break 2; // Break both foreach loops
                    }
                }
            }
        }

        // Update the database
        $updatedData = json_encode($itemsData);

        $updatedCollision = updateRoomWithItems($updatedData, $room['numX'], $room['numY']);
        $updatedCollisionJson = json_encode($updatedCollision);

        $update_room = $db->prepare("UPDATE rooms SET items = :items, collision = :collision WHERE id = :id");
        $update_room->execute([':items' => $updatedData, ':collision' => $updatedCollisionJson, ':id' => $room_id]);

        // Check if the picked-up item exists in the inventory
        if ($pickedUpItemId !== null) {
            $check_inventory = $db->prepare("SELECT * FROM inventory WHERE item_id = :item_id AND uid = :uid");
            $check_inventory->execute([':item_id' => $pickedUpItemId, ':uid' => $user->id]);

            if ($check_inventory->rowCount() > 0) {
                // If item exists, increment quantity
                $inventory_item = $check_inventory->fetch(PDO::FETCH_ASSOC);
                $new_quantity = $inventory_item['quantity'] + 1;
                $update_inventory = $db->prepare("UPDATE inventory SET quantity = :quantity WHERE id = :id");
                $update_inventory->execute([':quantity' => $new_quantity, ':id' => $inventory_item['id']]);
            } else {
                // If item does not exist, insert new record
                $insert_inventory = $db->prepare("INSERT INTO inventory (item_id, uid, quantity) VALUES (:item_id, :uid, 1)");
                $insert_inventory->execute([':item_id' => $pickedUpItemId, ':uid' => $user->id]);
            }
        }

        echo json_encode(array(
            'status' => 'item_removed',
            'updatedRoom' => $itemsData,
            'collisionMap' => $updatedCollision
        ));
    }
}
?>