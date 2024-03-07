<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if($auth) {
$id = $_GET['id'];
?>
  <div data-window='market_confirm_window' class='window window_bg' style='width: 400px;'>
    <div data-part='handle' class='window_title'>
      <div data-part='title' class='title_bg window_border'>Confirm purchase</div>
    </div>
    <div class='clearfix'></div>
    <div class='position-relative window_body'>
      <div class='container text-white p-3 text-center'>
        <span class="p-5">Are you sure you want to purchase this item?</span>
        <div class="clearfix"></div>
        <div class="text-center">
            <button class="bg-green-500 p-2 rounded text-white mt-2" onclick="market_confirm_window.confirm(<?php echo $id; ?>);">Purchase</button>
            <button class="bg-white p-2 rounded text-black mt-2" data-close>cancel</button>
        </div>
      </div>
    </div>
    <script>
      var market_confirm_window = {
        confirm: function(id) {

            load({
                method: 'POST',
                url: 'modals/market/ajax/purchase_confirm.php',
                data: 'id=' + id,
                success: function(data) {
                    if(data == 'cant_afford') {
                        notif('You do not have enough coins to make this purchase', 'bottom-center');
                    } else if(data == 'not_exist') {
                        notif('this item no longer exists', 'bottom-center');
                    } else {
                        console.log(data);
                        ren.updateNotif('inventory_notif', 1);
                        notif('Purchase successful. Item added to inventory', 'bottom-center');
                        closeModal('market_confirm_window');
                    }
                }
            });
        }
      }
    </script>
  </div>
<?php
}
?>