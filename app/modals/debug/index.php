<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if($auth) {
?>
  <div data-window='debug_window' class='window position-fixed bottom-12 right-2' style='width: 250px;height: 370px; background: #bba229;'>
  
    <div data-part='handle' class='window_title' style='background-image: radial-gradient(#a18b21 1px, transparent 0) !important;'>
    <div class='float-right'>
        <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
      </div>
      <div data-part='title' class='title_bg window_border' style='background: #bba229; color: #ede8d6;'>Scene Debug</div>
    </div>
    <div class='clearfix'></div>
    <div class='position-relative'>
      <div class='container text-light window_body p-2'>
        <div id="gameFps"></div>
        <div class="clearfix mt-2"></div>
      </div>
    </div>

    <script>
      var debug_window = {
        start: function() {

      },
        unmount: function() {
        },
      }
      debug_window.start();
    </script>
  </div>
<?php
}
?>