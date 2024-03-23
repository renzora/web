<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
?>
<div data-window='inventory_window' class='window window_bg' style='width: 600px; background: #bba229; display: flex;'>

<!-- Left Menu -->
<div class='menu-container' style='width: 200px; background: #1a1a1a;'>
    <!-- Add your left menu content here -->
</div>

<!-- Right Grid -->
<div class='right-container' style='flex-grow: 1;'>
    <div data-part='handle' class='window_title' style='background-image: radial-gradient(#a18b21 1px, transparent 0) !important;'>
        <div class='float-right'>
            <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
        </div>
        <div data-part='title' class='title_bg window_border' style='background: #bba229; color: #ede8d6;'>Blank inventory</div>
    </div>
    <div class='clearfix'></div>
    <div class='relative' style='display: flex;'>
        <!-- Search Input -->
        <input type="text" id="searchInput" placeholder="Search items..." class="w-100 p-2 border rounded mb-4">

        <!-- Inventory Grid -->
        <div id="inventory_window_items" class='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 container text-light window_body p-2' style='flex-grow: 1;'>
            <!-- Items will be dynamically added here -->
        </div>
    </div>


    <script>
var inventory_window = {
    itemsData: assets.load("items"),
    tileset: assets.load("tileset"),
    tileSize: 16, // Assuming tile size is 32x32 pixels
    tilesPerRow: 150, // Number of tiles per row in the tileset
    inventoryContainer: document.getElementById('inventory_window_items'), // Get the inventory container element

    start: function() {
        this.renderItems(this.itemsData);
    },
    unmount: function() {
        // Implement if needed
    },
    renderItems: function(itemsData) {
        this.clearInventory();
        Object.keys(itemsData).forEach(function(itemId) {
            var itemLayout = itemsData[itemId];
            var itemCanvas = document.createElement('canvas');
            var itemWidth = Math.max.apply(null, itemLayout.map(function(tile) { return tile.a; })) + 1;
            var itemHeight = Math.max.apply(null, itemLayout.map(function(tile) { return tile.b; })) + 1;
            itemCanvas.width = itemWidth * this.tileSize;
            itemCanvas.height = itemHeight * this.tileSize;
            var ctx = itemCanvas.getContext('2d');
            itemLayout.forEach(function(tile) {
                var srcX = Math.floor(tile.t % this.tilesPerRow) * this.tileSize;
                var srcY = Math.floor(tile.t / this.tilesPerRow) * this.tileSize;
                var posX = tile.a * this.tileSize;
                var posY = tile.b * this.tileSize;
                ctx.drawImage(this.tileset, srcX, srcY, this.tileSize, this.tileSize, posX, posY, this.tileSize, this.tileSize);
            }, this);
            this.inventoryContainer.appendChild(itemCanvas);
        }, this);
    },
    clearInventory: function() {
        while (this.inventoryContainer.firstChild) {
            this.inventoryContainer.removeChild(this.inventoryContainer.firstChild);
        }
    },
    filterItems: function(searchQuery) {
    var filteredItems = {};
    Object.keys(this.itemsData).forEach(function(itemId) {
        if (itemId.includes(searchQuery.toLowerCase())) {
            filteredItems[itemId] = this.itemsData[itemId];
        }
    }, this);
    this.renderItems(filteredItems);
}
};

inventory_window.start();

document.getElementById('searchInput').addEventListener('input', function() {
    var searchQuery = this.value;
    inventory_window.filterItems(searchQuery);
});
    </script>

    <div class='resize-handle'></div>
  </div>
<?php
}
?>