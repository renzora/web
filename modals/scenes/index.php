<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
?>
<div data-window='scenes_window' class='window window_bg' style='width: 600px; background: #26487d;'>

    <!-- Window Title -->
    <div data-part='handle' class='window_title' style='background-image: radial-gradient(#192d4b 1px, transparent 0) !important;'>
        <div class='float-right'>
            <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
        </div>
        <div data-part='title' class='title_bg window_border' style='color: #d2dde3; background: #26487d;'>World Map</div>
    </div>
    <div class='clearfix'></div>

    <!-- Window Body -->
    <div class='window_body'>
        <div class='p-3'> <!-- Padding added -->
            <div class="grid grid-cols-3 gap-4"> <!-- Three-column grid layout -->
                
                <!-- Left column for search and categories -->
                <div class="col-span-1">
                <input id="scenes_window_search" onkeyup="scenes_window.search();" type="text" class="w-full light_input text-sm border border-black rounded p-2 mb-3" placeholder="Search">
                    <div class="clearfix"></div>
                    <ul id="scenes_window_category" onclick="scenes_window.category_change(event);" data-selected="10" class="list-group list-group-flush rounded shadow border border-dark cursor-pointer" style="overflow-y: auto; max-height: 400px;font-size: 16px;">
                    </ul>
                </div>

                <!-- Right column for search results and create server button -->
                <div class="col-span-2">
                    <div id="scenes_window_search_result" class="grid grid-cols-1 gap-4 bg-white shadow rounded p-1">
                    <ul id="world_window_category" class="list-group list-group-flush rounded-md shadow cursor-pointer">
    
        <div class="p-1 pr-1 room-item" data-room-id="">
            <button onclick="ui.scene(id);" class="float-right bg-green-600 hover:bg-green-500 text-white p-1 py-0 border border-green-800 hover:border-green-900 rounded" style="font-size: 14px; text-shadow: -1px -1px 0 #189546, 1px -1px 0 #189546, -1px 1px 0 #189546, 1px 1px 0 #189546;">Go »</button>
            <span style="font-size:16px;">map name</span>
        </div>
 
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Additional styles for list items -->
    <style>
        .list-group-item:nth-child(odd) {
            background-color: #f5f5f5;
        }
        .list-group-item:nth-child(even) {
            background-color: #ffffff;
        }
    </style>

    <!-- Script for search and category change functionality -->
    <script>
    var scenes_window = {
        start: function() {

        }
        unmount: function() {

        }
    };
    scenes_window.start();
    </script>
</div>
<?php
}
?>