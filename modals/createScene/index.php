<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
    $find_cat = $db->prepare("SELECT * FROM roomcategory");
    $find_cat->execute();
?>

<div data-window='createroom_window' class='window window_bg' style='width: 350px; background: #b5443e;'>
    <div data-part='handle' class='window_title' style='background-image: radial-gradient(#85241e 1px, transparent 0) !important'>
        <div class='float-right'>
        <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
        </div>
        <div data-part='title' class='title_bg window_border' style='color: #d8dee1; background: #b5443e;'>Create New Room</div>
    </div>
    <div class='clearfix'></div>
    <div class='position-relative window_body'>
        <div class='container text-light p-3'>
            <div id="createroom_window_step1">
                <input id="createroom_window_name" type="text" class="w-full border rounded-md mx-2 px-3 py-2 shadow mb-2" placeholder="Room Name" />
                <textarea id="createroom_window_description" class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Room Description"></textarea>
                <select id="createroom_window_category" class="w-full form-select form-select-lg border border-dark shadow mb-2" aria-label="Default select example">
                    <option value="select">Select Category</option>
                    <?php 
                    while ($cat = $find_cat->fetch(PDO::FETCH_OBJ)) {
                    ?>
                        <option value="<?php echo $cat->name; ?>"><?php echo $cat->name; ?></option>
                    <?php 
                    }
                    ?>
                </select>
                <div class="text-center">
                    <button class="btn btn-lg btn-light mt-2" onclick="modal.load('world');">Back</button>
                    <button class="btn btn-lg border border-success btn-success shadow mt-2" onclick="createroom_window.createRoom()"><i class="fa-solid fa-check"></i> Create Room</button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <script>
        var createroom_window = {
            start: function() {
                modal.close('world_window');
            },
            unmount: function() {
                console.log('unmounted');
            },
            createRoom: function() {
                console.log('create room called');
                var createroom_window_name = $('#createroom_window_name').val();
                var createroom_window_description = $('#createroom_window_description').val();
                var createroom_window_category = $('#createroom_window_category').val();

                console.log(createroom_window_name, createroom_window_description);

                ui.ajax({
                    method: 'POST',
                    data: 'name=' + encodeURIComponent(createroom_window_name) + '&description=' + encodeURIComponent(createroom_window_description) + '&category=' + encodeURIComponent(createroom_window_category),
                    url: 'modals/createroom/ajax/save.php',
                    success: function(data) {
                        console.log(data);
                        modal.close('createroom_window');
                        ui.loadScene(data);
                    }
                });
            }
        };

        createroom_window.start();
    </script>
</div>

<?php
}
?>