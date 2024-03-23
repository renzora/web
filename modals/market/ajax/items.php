<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$marketCollection = $db->market;

if($auth) {
    if (isset($_GET['item_id'])) {
        $itemId = $_GET['item_id'];

        $item = $marketCollection->findOne(['id' => $itemId]);

        if($item) {
            $imageURL = "inc/roomgen.php?mode=item&item_id=" . $item->item_id;
            ?>

            <button class="light_input text-xl border border-black rounded p-2" onclick="market_window.load_items('<?php echo $item->type; ?>');">Back to Market</button>
            <div class="clearfix mb-3"></div>


            <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col md:flex-row">
                
        <div class="p-4 w-full md:w-1/2 flex flex-col md:flex-row md:items-center">
            <div class="flex-grow">
                <h1 class="text-2xl font-bold text-gray-800 mb-2"><?php echo $item->item_name; ?></h1>
                <p class="text-gray-700 text-base"><?php echo $item->item_info; ?></p>
                <button class="green_button text-white font-bold py-3 px-4 rounded mt-2 shadow-md" onclick="modal('market/ajax/confirm.php?id=<?php echo $item->id; ?>', 'market_confirm_window');">
                Buy <?php echo $item->price; ?>c
            </button>
            </div>
        </div>

        <img src="<?php echo $imageURL; ?>&size=xl&bg=1" class="w-full md:w-1/2 object-cover" />
    </div>

            <?php
        } else {
            echo "<p>Item not found.</p>";
        }
    } else {
        $cat = $_GET['id'];

        $items = $marketCollection->find(['type' => $cat]);
        ?>

        <div class="grid grid-cols-6 gap-2">

        <?php

            foreach ($items as $item) {
            $imageURL = "inc/roomgen.php?mode=item&item_id=" . $items->item_id;
            ?>
            <div class="rounded border border-black shadow-xl cursor-pointer" onclick="market_window.load_profile(<?php echo $items->id; ?>);">
                <div class="relative rounded-sm m-0" style="background-image: url('<?php echo $imageURL; ?>&isize=s&bg=1'); height: 90px; background-size: cover; background-position: center;">
                    <button class="absolute bottom-0 right-0 bg-green-600 hover:bg-green-500 text-white p-1 py-0 m-1 border border-green-800 hover:border-green-900 rounded" style="font-size: 11px; text-shadow: -1px -1px 0 #189546, 1px -1px 0 #189546, -1px 1px 0 #189546, 1px 1px 0 #189546;" onclick="modal('market/ajax/confirm.php?id=<?php echo $items->id; ?>', 'market_confirm_window');">
                        Buy <?php echo $items->price; ?>c
                    </button>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
        <?php
    }
}
?>