<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';
$queryParam = "%" . $query . "%";

try {
    $stmt = $db->prepare("
        (SELECT 'Rooms' AS result_type, name AS title, info AS description, id AS extra_info FROM rooms WHERE name LIKE :query OR info LIKE :query ORDER BY name ASC LIMIT 5)
        UNION
        (SELECT 'Market Items' AS result_type, item_name AS title, item_info AS description, item_id AS extra_info FROM market WHERE item_name LIKE :query OR item_info LIKE :query ORDER BY item_name ASC LIMIT 5)
        UNION
        (SELECT 'Users' AS result_type, username AS title, '' AS description, avatar AS extra_info FROM users WHERE username LIKE :query ORDER BY username ASC LIMIT 5)
    ");
    $stmt->bindParam(':query', $queryParam, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_OBJ);

    if ($results) {
        $groupedResults = [];
        foreach ($results as $row) {
            $groupedResults[$row->result_type][] = $row;
        }
?>

<div class='search-results'>
    <?php foreach ($groupedResults as $type => $items): ?>
        <h2 class='text-white bg-slate-800 text-lg font-bold rounded-sm p-2 m-1'><?= htmlspecialchars($type) ?></h2>
        <?php foreach ($items as $item): 
            $imgSrc = '';
            if ($type === 'Rooms') {
                $imgSrc = "inc/roomgen.php?mode=scene&id=" . urlencode($item->extra_info) . "&crop";
            } elseif ($type === 'Users') {
                $imgSrc = "inc/spritegen.php?avatar=" . urlencode($item->extra_info) . "&single";
            } elseif ($type === 'Market Items') {
                $imgSrc = "inc/roomgen.php?mode=item&item_id=" . urlencode($item->extra_info) . "&isize=s&bg=1";
            }
        ?>
            <div class='search-result-item flex items-center hover:bg-blue-500 p-1 rounded cursor-pointer m-1 transition duration-150 ease-in-out' onclick="room(<?php echo $item->extra_info; ?>);">
                <div class='flex flex-row'>
                    <?php if ($imgSrc): ?>
                        <div style='background-image: url("<?= $imgSrc ?>"); width: 48px; height: 48px; background-size: cover; background-position: center;' class='mr-2 rounded flex-none'></div>
                    <?php else: ?>
                        <div style='width: 48px; height: 48px;' class='mr-2 flex-none'></div>
                    <?php endif; ?>
                    <div class='flex-grow ml-2 mt-1'>
                        <h3 class='text-white'><?= htmlspecialchars($item->title) ?></h3>
                        <?php if (!empty($item->description)): ?>
                            <p class='text-gray-300'><?= htmlspecialchars($item->description) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>

<?php 
    } else {
        echo "<p class='text-white text-center'>No results found for " . htmlspecialchars($query) . "</p>";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>