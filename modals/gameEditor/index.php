<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
?>
  <div data-window='game_editor_window' class='window window_bg' style='width: 50%; background: #232f33;'>

<div data-part='handle' class='window_title' style='background-image: radial-gradient(#455357 1px, transparent 0) !important;'>
  <div class='float-right'>
    <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
  </div>
  <div data-part='title' class='title_bg window_border' style='background: #232f33; color: #ede8d6;'>Game Editor</div>
</div>
<div class='clearfix'></div>
<div class='relative'>
  <div class='container text-light window_body p-2 flex'>

    <!-- Menu Section -->
    <div class='w-1/4 p-4'>
    <div class="flex flex-col">
    <span class="text-2xl font-semibold text-gray-200 mb-4">Menu</span>
    <a href="#" class="text-xl text-white hover:text-white mb-3" onclick="ui.modal('gameEditor/items/index.php', 'game_editor_items_window');">Items</a>
  </div>
    </div>

    <!-- Main Content Section -->
    <div class='w-3/4 p-4'>
    <div id="guiContainer" class="text-light p-2 flex-grow"></div>

    </div>

  </div>
</div>

    <script>
      var game_editor_window = {
        start: function() {


        },
        unmount: function() {

        }
      }

      game_editor_window.start();
    </script>

    <div class='resize-handle'></div>
  </div>
<?php
}
?>