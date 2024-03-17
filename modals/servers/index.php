<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
?>
<div data-window='servers_window' class='window window_bg' style='width: 600px; background: #26487d;'>

    <!-- Window Title -->
    <div data-part='handle' class='window_title' style='background-image: radial-gradient(#192d4b 1px, transparent 0) !important;'>
        <div class='float-right'>
            <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
        </div>
        <div data-part='title' class='title_bg window_border' style='color: #d2dde3; background: #26487d;'>Renzora Servers</div>
    </div>
    <div class='clearfix'></div>

    <!-- Window Body -->
    <div class='window_body'>
        <div class='p-3'> <!-- Padding added -->
            <div class="grid grid-cols-3 gap-4"> <!-- Three-column grid layout -->
                
                <!-- Left column for search and categories -->
                <div class="col-span-1">
                <input id="servers_window_search" onkeyup="servers_window.search();" type="text" class="w-full light_input text-sm border border-black rounded p-2 mb-3" placeholder="Search">
                    <div class="clearfix"></div>
                    <ul id="servers_window_category" onclick="servers_window.category_change(event);" data-selected="10" class="list-group list-group-flush rounded shadow border border-dark cursor-pointer" style="overflow-y: auto; max-height: 400px;font-size: 16px;">
                        <?php
                        $find_cat = $db->prepare("SELECT * FROM server_category ORDER BY weight ASC");
                        $find_cat->execute();

                        while ($server_cat = $find_cat->fetch(PDO::FETCH_OBJ)) {
                            echo '<li class="list-group-item px-2" data-category="'.$server_cat->id.'">'.ucfirst($server_cat->name).'</li>';
                        }
                        ?>
                    </ul>
                </div>

                <!-- Right column for search results and create server button -->
                <div class="col-span-2">
                    <div id="servers_window_search_result" class="grid grid-cols-1 gap-4 bg-white shadow rounded p-1">
                        <!-- Search results will be loaded here -->
                    </div>
                    <button class="green_button p-3 text-xs rounded shadow float-right mt-4" onclick="servers_window.createServerCallback();">Create Server</button>
                    <button class="green_button p-3 text-xs rounded shadow float-right mt-4 mr-3" onclick="servers_window.joinServerCallback();">Join Server</button>
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
    var servers_window = {
        start: function() {
            this.search();
        },
        search: function() {
            var servers_window_search = document.getElementById('servers_window_search').value;
            var servers_window_category = document.getElementById('servers_window_category').getAttribute('data-selected');
            ui.load({
                url: 'modals/servers/ajax/search.php',
                data: 'search=' + encodeURIComponent(servers_window_search) + '&category=' + encodeURIComponent(servers_window_category),
                success: function(data) {
                    ui.html('#servers_window_search_result', data, 'html');

                }.bind(this) // Ensure this refers to servers_window
            });
        },
        createServerCallback:function() {
            ui.modal('servers/create.php', 'servercreate_window');
            ui.hideModal('servers_window');
        },
        joinServerCallback:function() {
            ui.modal('servers/join.php', 'serverjoin_window');
            ui.hideModal('servers_window');
        },

        category_change: function(event) {
            var clickedLi = event.target.closest('li');
            var servers_window_category = clickedLi.getAttribute('data-category');
            document.querySelectorAll('#servers_window_category li').forEach(function(li) {
                if (li !== clickedLi) {
                    li.classList.remove('bg-primary', 'text-light');
                }
            });
            clickedLi.classList.add('bg-primary', 'text-light');

            document.getElementById('servers_window_category').setAttribute('data-selected', servers_window_category);

            this.search();
        },
        search_clear: function() {
            document.getElementById('servers_window_search').value = '';
            this.search();
        },
    unmount: function() {

    }
    };
    servers_window.start();
    </script>
</div>
<?php
}
?>