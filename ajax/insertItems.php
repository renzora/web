<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

if ($auth) {
    // Load items.json
    $json = file_get_contents('../assets/json/items.json'); // Update the path to your JSON file
    $items = json_decode($json, true);

    foreach ($items as $item_id => $item_data) {
        // Check if item already exists
        $check = $db->prepare("SELECT * FROM market WHERE item_id = :item_id");
        $check->execute([':item_id' => $item_id]);

        if ($check->rowCount() == 0) {
            // Item does not exist, insert it
            $insert = $db->prepare("INSERT INTO market (item_id, item_name, item_info, price, cat, type, preview_room, active) VALUES(:item_id, :item_name, :item_info, :price, :cat, :type, :preview_room, :active)");
            $insert->execute([
                ':item_id' => $item_id,
                ':item_name' => $item_id,
                ':item_info' => $item_id,
                ':price' => 10,
                ':cat' => $item_data['category'],
                ':type' => $item_data['type'],
                ':preview_room' => 0,
                ':active' => 1
            ]);
        }
    }
}
?>