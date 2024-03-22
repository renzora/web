<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
?>
<div data-window='market_window' class='window window_bg' style='width: 700px; background: #58467f;'>

<div data-part='handle' class='window_title' style='background-image: radial-gradient(#23202d 1px, transparent 0) !important;'>
    <div class='float-right'>
        <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
    </div>
    <div data-part='title' class='title_bg window_border' style='background: #58467f; color: #efeeec;'>Renzora market</div>
</div>
<div class='clearfix'></div>

<div class='window_body'>
    <div class="flex pb-0">

        <div class="w-1/4 p-2">

        <div class="bg-yellow-600 text-white p-2 rounded mb-3">
          <?php
        $get_user_coins = $db->prepare("SELECT coins FROM users WHERE id = :uid");
        $get_user_coins->execute([':uid' => $user->id]);
        $user_coins = $get_user_coins->fetch(PDO::FETCH_OBJ)->coins;
        ?>
          <?php echo number_format($user_coins); ?> Coins
        </div>

        <div onclick="modal.load('purchase');" class="bg-lime-600 pointer text-white p-2 rounded mb-3">
          Buy More Coins
        </div>

            <ul id="market_window_category" class="list-group list-group-flush rounded shadow border border-dark pointer" style="overflow-y: auto; height: 400px;">
                <?php
                $find_market_cat = $db->prepare("SELECT * FROM marketcategory WHERE active = 1 ORDER BY name ASC");
                $find_market_cat->execute();
                while($market_cat = $find_market_cat->fetch(PDO::FETCH_OBJ)) {
                    echo '<li class="list-group-item px-2" onclick="market_window.load_items(\''.$market_cat->name.'\');">'.ucfirst($market_cat->name).'</li>';
                }
                ?>
            </ul>
        </div>

        <div class="w-3/4 p-2">
          <div class="p-0">
          <input id="market_window_search" onkeyup="market_window.search();" type="text" class="w-full text-xl light_input border border-black rounded p-2 mb-3" placeholder="Search market">
              </div>
            <div id="market_window_items" class='pr-2' style="overflow-y: auto; max-height: 440px;"></div>
        </div>
    </div>
</div>

<style>
        .list-group-item:nth-child(odd) {
            background-color: #f5f5f5;
        }
        .list-group-item:nth-child(even) {
            background-color: #ffffff;
        }
        
    </style>

    <script>
    var market_window = {
      load_items: function(id) {
        console.log('clicked on ' + id);
        ui.ajax({
          url: 'modals/market/ajax/items.php',
          data: 'id=' + id,
          success: function(data) {
            ui.html('#market_window_items', data, 'html');

            $('#market_window_items').find('div[data-id]').each(function() {
              var itemId = $(this).data('id');
              var divId = 'market_window_item_' + itemId;
              scene.drawItem(itemId, divId);
            });
          }
        });
      },
      load_profile: function(id) {
        console.log('clicked on ' + id);
        ui.ajax({
          url: 'modals/market/ajax/items.php',
          data: 'item_id=' + id,
          success: function(data) {
            ui.html('#market_window_items', data, 'html');

            $('#market_window_items').find('div[data-id]').each(function() {
              var itemId = $(this).data('id'); // Extract the item ID
              var divId = 'market_window_item_' + itemId; // Construct the div ID
              scene.drawItem(itemId, divId); // Draw the item
            });
          }
        });
      },
      unmount: function() {

      },
    };

    market_window.load_items('wall');
    </script>
  </div>
<?php
}
?>