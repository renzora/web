<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
?>
<div data-window='serverjoin_window' class='window window_bg' style='width: 350px; background: #26487d;'>

    <!-- Window Title -->
    <div data-part='handle' class='window_title' style='background-image: radial-gradient(#192d4b 1px, transparent 0) !important;'>
        <div class='float-right'>
            <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
        </div>
        <div data-part='title' class='title_bg window_border' style='color: #d2dde3; background: #26487d;'>Join server</div>
    </div>
    <div class='clearfix'></div>

    <!-- Window Body -->
    <div class='window_body'>
        <div class='p-3'> <!-- Padding added -->
        <span class="text-white">In order to connect to a server, you will need a server code.</span>
        <input
        type="text"
        id="server_deets" class='mt-3 light_input shadow appearance-none border rounded text-2xl w-full p-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'
        placeholder="Server Code"
        required="required"
        autocomplete="off"
      />

      <button onclick="serverjoin_window.joinServer();" class="green_button text-white font-bold py-3 px-4 rounded w-full mt-2 shadow-md">Connect to server</button>

        </div>
    </div>


    <!-- Script for search and category change functionality -->
    <script>
    var serverjoin_window = {
        start: function() {
            ui.hideModal('servers_window');
            document.getElementById('server_deets').focus();
        },
        joinServer: function() {
            var code = document.getElementById('server_deets').value;
            ui.load({
                outputType: 'json',
                url: 'modals/servers/ajax/serverCode.php',
                method: 'GET',
                data: 'code=' + code,
                success: function(data) {
                    console.log(data);
                    if(data.message == 'server_found') {
                        network.connectToGameServer(data.uno, data.dos)
                    } else {
                        ui.hideModal('serverjoin_window');
                        ui.modal('errors/serverNotFound.php', 'serverNotFound_window');
                    }
                    document.getElementById('server_deets').value = '';
                    document.getElementById('server_deets').focus();
                }, error: function(data) {
                    console.log(data);
                }
            });
        },
        unmount: function() {
            ui.showModal('servers_window');
        }
    };
    serverjoin_window.start();
    </script>
</div>
<?php
}
?>