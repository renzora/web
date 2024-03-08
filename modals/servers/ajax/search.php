<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$search = $_GET['search'];
$category = $_GET['category'];

if (isset($search)) {
    $find_servers = $db->prepare("SELECT * FROM servers WHERE (server_name LIKE :name) AND category = :category");
    $find_servers->execute([':name' => "%{$search}%", ':category' => $category]);
} else {
    $find_servers = $db->prepare("SELECT * FROM servers WHERE category = :category");
    $find_servers->execute([':category' => $category]);
}
?>

<ul id="world_window_category" class="list-group list-group-flush rounded-md shadow cursor-pointer">
    <?php
    if ($find_servers->rowCount() > 0) {
        while ($servers = $find_servers->fetch(PDO::FETCH_OBJ)) {
    ?>
        <div class="p-1 pr-1 room-item" data-room-id="<?php echo $servers->id; ?>">

            <button onclick="network.connectToGameServer('<?php echo $servers->ip; ?>','<?php echo $servers->port; ?>');" class="float-right bg-green-600 hover:bg-green-500 text-white p-1 py-0 border border-green-800 hover:border-green-900 rounded" style="font-size: 14px; text-shadow: -1px -1px 0 #189546, 1px -1px 0 #189546, -1px 1px 0 #189546, 1px 1px 0 #189546;">Connect »</button>
            <span style="font-size:16px;"><?php echo $servers->server_name; ?></span>
        </div>
    <?php
        }
    } else {
        echo '<li class="list-group-item text-center" style="font-size: 16px;">No Servers Found</li>';
    }
    ?>
</ul>