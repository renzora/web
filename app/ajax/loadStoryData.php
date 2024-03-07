<?php 
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

if($auth) {

    $id = $_GET['id'];
    $find_story = $db->prepare("SELECT * FROM storymodeChapters WHERE id = :id");
    $find_story->execute([ ':id' => $id ]);

    if($find_story->rowCount() == 0) {
        echo 'not_found';
    } else {
        $story = $find_story->fetch(PDO::FETCH_OBJ);

        $find_progress = $db->prepare("SELECT * FROM storymodeProgress WHERE chapter_id = :chapter_id AND uid = :uid");
        $find_progress->execute([ ':chapter_id' => $story->id, ':uid' => $user->id ]);

        if($find_progress->rowCount() == 0) {
            // insert progress for chapter
            $insert = $db->prepare("INSERT INTO storymodeProgress (uid, chapter_id, progress) VALUES(:uid, :chapter_id, :progress)");
            $insert->execute([ ':uid' => $user->id, ':chapter_id' => $story->id, ':progress' => 'objective1']);
            $objective = 'objective1';

        } else {
            $progress = $find_progress->fetch(PDO::FETCH_OBJ);
            $objective = $progress->progress;
        }

        echo json_encode([
            "status" => "success",
            "id" => $story->id,
            "storyData" => $story->config,
            "progress" => $objective
        ]);
    }
}