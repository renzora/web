<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if($auth) {

?>

<div class='fixed top-0 right-0 mt-2 z-10 flex items-center'>
<button class="green_button p-3 text-xs rounded shadow float-right mr-2" onclick="game.roomData = assets.load('roomData');">load room 1</button>
      <button class="green_button p-3 text-xs rounded shadow float-right mr-2" onclick="game.roomData = assets.load('roomData2');">load room 2</button>
</div>

    <div class='fixed bottom-0 left-0 m-3 z-10 flex items-center'>

    <div class="fixed top-1/2 left-0 transform -translate-y-1/2 rounded-r-xl shadow p-3 mt-2 bg-opacity-80 bg-black z-10">
 
    <div class="py-2 cursor-pointer">
      <div onclick="ui.modal('servers')" aria-label="Servers" class="icon globe hint--right"></div>

    </div>


    <div class="py-2 relative cursor-pointer">
    <span id="market_notif" class="absolute top-0 left-0 transform -translate-x-1/2 -translate-y-1/2 badge rounded bg-red-600 border border-gray-900 shadow-md mt-3 ml-1 p-1 text-white text-xs hidden" style="z-index: 1;"></span>

      <div onclick="ui.modal('market')" aria-label="Market" class="icon gift hint--right"></div>
    </div>

    <div class="py-2 cursor-pointer">
      <div onclick="ui.modal('settings')" aria-label="Game Settings" class="icon settings hint--right"></div>
    </div>

    </div>
  </div>

<?php
}
?>