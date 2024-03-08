<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if($auth) {
?>
    <div data-window='inventory_window' class='window window_bg' style='width: 300px;background: #3c6aa5;'>

<div data-part='handle' class='window_title' style='background-image: radial-gradient(#314359 1px, transparent 0) !important;'>
    <div class='float-right'>
        <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
    </div>
    <div data-part='title' class='title_bg window_border' style='background: #3c6aa5; color: #d7ebff;'>Inventory</div>
</div>

<div class='clearfix'></div>

<div class='grid grid-cols-12 gap-4 p-2 mt-1 window_body text-light' style="height: 200px;">

    <div class="col-span-12">
        <div class="grid grid-cols-6 gap-3">
            <?php 
            $find_items = $db->prepare("SELECT item_id, SUM(quantity) as total_quantity FROM inventory WHERE uid = :uid GROUP BY item_id");
            $find_items->execute([':uid' => $user->id]);

            if ($find_items->rowCount() == 0) {
            ?>
                <div class="col-span-6 flex flex-col items-center justify-center">
                    <span class="text-white mb-3">You have no items</span>
                    <button class="bg-white p-3 rounded text-black shadow" onclick="modal('store/index.php', 'store_window');">Visit Store</button>
                </div>
            <?php
            } else {
                while($items = $find_items->fetch(PDO::FETCH_OBJ)) {
            ?>

<div class="inventory-item rounded relative m-0" style="background-image: url('inc/roomgen.php?mode=item&item_id=<?php echo $items->item_id; ?>&bg=0'); height: 40px; background-size: cover; background-position: center;" data-item-id="<?php echo $items->item_id; ?>">
        <div class="absolute top-0 right-0 bg-red-500 rounded-full border border-black shadow h-6 w-6 flex items-center justify-center text-white text-xs" style="margin-top: -5px; margin-right: -5px;">
            <?php echo $items->total_quantity; ?>
        </div>
    </div>

            <?php
                }
            }
            ?>
        </div>
    </div>
</div>
    <script>
var inventory_window = {
    init: function() {
        document.querySelectorAll('.inventory-item').forEach(function(item) {
            item.addEventListener('click', function() {
                var itemId = this.getAttribute('data-item-id');

                // Check if the clicked item is already the selected one
                if (scene.selectedItemId === itemId) {
                    // Cancel the clone and deselect the item
                    scene.cancelCloning();
                } else {
                    // Reset classes for all items
                    document.querySelectorAll('.inventory-item').forEach(function(otherItem) {
                        otherItem.classList.remove('bg-green-400'); // Remove selection class
                    });

                    this.classList.add('bg-green-400'); // Change background to indicate selection

                    // Get mouse position
                    const mouseX = event.clientX;
                    const mouseY = event.clientY;

                    // Add the cloned item image to the scene
                    scene.addClonedItemImage(itemId, mouseX, mouseY);
                    hideModal('inventory_window');
                }
            });
        });
        ren.clearNotif('inventory_notif');
    },
    unmount: function() {
      scene.selectedItemId = null;
      scene.cancelCloning();
    }
};

inventory_window.init();
</script>
<div class="resize-handle"></div>
</div>
<?php
}
?>