<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

if($auth) {

    $id = $_POST['id'];

    // Find the item in the market
    $find_item = $db->prepare("SELECT * FROM market WHERE id = :id");
    $find_item->execute([':id' => $id]);
    $item = $find_item->fetch(PDO::FETCH_OBJ);

    if($item) {
        // Get the user's current coin balance
        $get_user_coins = $db->prepare("SELECT coins FROM users WHERE id = :uid");
        $get_user_coins->execute([':uid' => $user->id]);
        $user_coins = $get_user_coins->fetch(PDO::FETCH_OBJ)->coins;

        // Check if the user can afford the item
        if ($user_coins >= $item->price) {
            // Check if the user already has the item in their inventory
            $check_inventory = $db->prepare("SELECT * FROM inventory WHERE item_id = :item_id AND uid = :uid");
            $check_inventory->execute([':item_id' => $item->item_id, ':uid' => $user->id]);
            $inventory_item = $check_inventory->fetch(PDO::FETCH_OBJ);

            if ($inventory_item) {
                // If item exists, update the quantity
                $new_quantity = $inventory_item->quantity + 1;
                $update = $db->prepare("UPDATE inventory SET quantity = :quantity WHERE item_id = :item_id AND uid = :uid");
                $update->execute([':quantity' => $new_quantity, ':item_id' => $item->item_id, ':uid' => $user->id]);
            } else {
                // If item does not exist, insert it into the database
                $insert = $db->prepare("INSERT INTO inventory (item_id, uid, quantity) VALUES(:item_id, :uid, 1)");
                $insert->execute([':item_id' => $item->item_id, ':uid' => $user->id]);
            }

            // Deduct the price from the user's coins
            $deduct_coins = $db->prepare("UPDATE users SET coins = coins - :price WHERE id = :uid");
            $deduct_coins->execute([':price' => $item->price, ':uid' => $user->id]);

        } else {
            echo 'cant_afford';
        }
    } else {
        echo 'not_exist';
    }
}
?>