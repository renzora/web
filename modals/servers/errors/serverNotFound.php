<div data-window='serverNotFound_window' class='window window_bg' style='width: 350px;'>
  <div data-part='handle' class='window_title' style='background-image: radial-gradient(#151b25 1px, transparent 0) !important;'>
      <div data-part='title' class='title_bg window_border' style='color: #b1b9c7;background: #202835;'>Server Error</div>
    </div>
    <div class='clearfix'></div>
    <div class='container rounded mx-auto'>
      <div class='clearfix mt-1'></div>

      <div class="text-light text-center text-white p-2">
        Couldn't find server. Please make sure you're using the correct server code.
      </div>

      <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mx-auto block login_error_window_close mb-3" data-close>Okay</button>

    </div>
</div>

<script>
    var serverNotFound_window = {

      unmount: function() {
        modal.show('serverjoin_window');
      }
    };
  
  </script>
  </div>