<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

if($auth) {
    $id = $_POST['id'];

    $marketCollection = $db->market;
    $item = $marketCollection->findOne(['id' => $id]);

    if($item) {
        $usersCollection = $db->users;
        $userDocument = $usersCollection->findOne(['id' => $user->id]);
        $user_coins = $userDocument->coins ?? 0;

        if($user_coins >= $item->price) {
            $inventoryCollection = $db->inventory;
            $inventoryItem = $inventoryCollection->findOne(['item_id' => $item->item_id, 'uid' => $user->id]);

            if($inventoryItem) {
                $new_quantity = $inventoryItem->quantity + 1;
                $inventoryCollection->updateOne(['item_id' => $item->item_id, 'uid' => $user->id], ['$set' => ['quantity' => $new_quantity]]);
            } else {
                $inventoryCollection->insertOne(['item_id' => $item->item_id, 'uid' => $user->id, 'quantity' => 1]);
            }

            $usersCollection->updateOne(['id' => $user->id], ['$inc' => ['coins' => -$item->price]]);
        } else {
            echo 'cant_afford';
        }
    } else {
        echo 'not_exist';
    }
}
?>