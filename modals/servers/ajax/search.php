<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$serversCollection = $db->servers;
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$options = ['sort' => ['server_name' => 1]];

if (!empty($search)) {
    $findCriteria = [
        '$and' => [
            ['server_name' => new MongoDB\BSON\Regex($search, 'i')],
            ['category' => $category]
        ]
    ];
} else {
    $findCriteria = ['category' => $category];
}

$serversCursor = $serversCollection->find($findCriteria, $options);
?>

<ul id="world_window_category" class="list-group list-group-flush rounded-md shadow cursor-pointer">
    <?php
    $serversFound = false;
    foreach ($serversCursor as $servers) {
        $serversFound = true;
        ?>
        <div class="p-1 pr-1 room-item" data-room-id="<?php echo $servers->_id; ?>">

            <button onclick="network.connectToGameServer('<?php echo $servers->ip; ?>','<?php echo $servers->port; ?>');" class="float-right bg-green-600 hover:bg-green-500 text-white p-1 py-0 border border-green-800 hover:border-green-900 rounded" style="font-size: 14px; text-shadow: -1px -1px 0 #189546, 1px -1px 0 #189546, -1px 1px 0 #189546, 1px 1px 0 #189546;">Connect »</button>
            <span style="font-size:16px;"><?php echo $servers->server_name; ?></span>
        </div>
        <?php
    }
    if (!$serversFound) {
        echo '<li class="list-group-item text-center" style="font-size: 16px;">No Servers Found</li>';
    }
    ?>
</ul>