<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php'; 

if($auth) {
    $collection = $db->rooms;

    $insertResult = $collection->insertOne([
         'name' => $name,
         'info' => $description,
         'active' => 1,
         'verified' => 0,
         'category' => $category,
         'items' => $items,
         'uid' => $user->id,
         'renscript' => $renscript,
         'created' => time()
    ]);

    echo $insertResult->getInsertedId();
}
