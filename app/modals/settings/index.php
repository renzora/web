<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if($auth) {
?>
  <div data-window='settings_window' class='window window_bg' style='width: 330px;'>
    <div data-part='handle' class='window_title window_border'>
    <div class='float-right'>
        <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
      </div>
      <div data-part='title' class='title_bg window_border'>Settings</div>
    </div>
    <div class='clearfix'></div>
    <div class='container text-light window_body p-2'>
      <div class='clearfix mt-3'></div>

      <div class="volume-control mt-4 mb-4">
  <label for="volumeControl" class="block text-white text-lg mb-2">Volume:</label>
  <input type="range" id="volumeControl" class="slider-thumb w-full h-2 rounded-lg cursor-pointer" min="0" max="1" step="0.01" value="0.5">
</div>

      <button data-close onclick='ui.modal("auth/signout.php", "signout_window");'
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full w-full">
        Sign Out
      </button>
    </div>

    <style>

      </style>

    <script>
    var settings_window = {
      start: function() {

      },
      unmount: function() {
 
      }
    };

    settings_window.start();
  </script>
  </div>
<?php
}
?>
