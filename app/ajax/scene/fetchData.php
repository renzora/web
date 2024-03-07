<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

header('Content-Type: application/json');

try {
    $id = $_GET['id'];
    $find_room = $db->prepare("SELECT name, numX, numY, items, collision, renscript FROM rooms WHERE id = :id");
    $find_room->execute([ ':id' => $id ]);

    $room = $find_room->fetch(PDO::FETCH_OBJ);

    if ($room) {
        $numX = json_decode($room->numX);
        $numY = json_decode($room->numY);
        $items = json_decode($room->items);
        $collision = json_decode($room->collision);
        $renscript = json_decode($room->renscript);

        echo json_encode([
            'name' => $room->name,
            'numX' => $numX,
            'numY' => $numY,
            'collision' => $collision,
            'items' => $items,
            'renscript' => $renscript
        ]);
    } else {
        echo json_encode(['error' => 'not_found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>