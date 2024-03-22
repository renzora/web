<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
?>
<div data-window='servercreate_window' class='window window_bg' style='width: 400px; background: #26487d;'>
    <!-- Constant Window Title -->
    <div class='window_title' style='background-image: radial-gradient(#192d4b 1px, transparent 0) !important;'>
        <div class='float-right'>
            <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
        </div>
        <div class='title_bg window_border' style='color: #d2dde3; background: #26487d;'>Create Renzora Server</div>
    </div>
    <div class='clearfix'></div>

       <!-- Step 1 Content -->
       <div id="step1" class='window_body p-3' style='display: block;'>
        <!-- Step 1: Server IP and Port Configuration -->
        <input type="text" id="server_ip_setup" class='light_input shadow appearance-none border rounded text-2xl w-full p-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2' placeholder="IP Address of your server" required="required" autocomplete="off" />
        
        <input type="text" id="server_port_setup" class='light_input shadow appearance-none border rounded text-2xl w-full p-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2' placeholder="Port [defaults to 8122]" required="required" autocomplete="off" />

        <button onclick="servercreate_window.nextStep(2);" class="green_button p-3 text-xs rounded shadow float-right">Next</button>

        <button onclick="servercreate_window.nextStep(2);" class="green_button p-3 text-xs rounded shadow float-right mr-3">How to setup</button>
    </div>

    <!-- Step 2 Content (hidden initially) -->
    <div id="step2" class='window_body p-3' style='display: none;'>
        <!-- Content for Step 2 -->
        <!-- Add your inputs or information for Step 2 here -->
        step 2

        <button onclick="servercreate_window.nextStep(3);" class="green_button p-3 text-xs rounded shadow float-right">Next</button>

        <button onclick="servercreate_window.previousStep(1);" class="green_button p-3 text-xs rounded shadow float-right mr-3">Back</button>
    </div>

    <!-- Step 3 Content (hidden initially) -->
    <div id="step3" class='window_body p-3' style='display: none;'>
        <!-- Content for Step 3 -->
        <!-- Add your inputs or information for Step 3 here -->
        last step

        <button onclick="servercreate_window.publishData();" class="green_button p-3 text-xs rounded shadow float-right">Publish</button>

        <button onclick="servercreate_window.previousStep(2);" class="green_button p-3 text-xs rounded shadow float-right mr-3">Back</button>
    </div>

<script>
var servercreate_window = {
    data: {}, // Holds data across steps

    nextStep: function(nextStepId) {
        var currentStepId = nextStepId - 1; // Identifying the current step based on the intended next step

// Specifically checking if transitioning from Step 1 to Step 2
if (currentStepId == 1) {
    this.data.serverIp = document.getElementById('server_ip_setup').value;
    this.data.serverPort = document.getElementById('server_port_setup').value || '8122';

    // Show error modal if server IP is not provided and prevent further execution
    if (!this.data.serverIp) {
        modal.load('servers/errors/createServerError.php?code=no_ip', 'createServerError_window');
        return; // Prevent transitioning to the next step
    }

    return;
} else if (currentStepId == 3) {
    ui.ajax({
                    dataType: 'json'
                    url: 'modals/servers/ajax/createServer.php',
                    method: 'POST',
                    data: 'ip=' + this.data.serverIp + '&port=' + this.data.serverPort,
                    success: function(data) {

                    }
                });

    return;
}

// Transition logic
document.getElementById('step' + currentStepId).style.display = 'none';
document.getElementById('step' + nextStepId).style.display = 'block';

        // Add similar data collection for other steps as needed
    },

    previousStep: function(prevStepId) {
        // Logic to show the previous step and hide the current one
        var currentStepId = prevStepId + 1;
        document.getElementById('step' + currentStepId).style.display = 'none';
        document.getElementById('step' + prevStepId).style.display = 'block';
        // Optionally, handle any necessary cleanup or data rollback if needed
    },

    publishData: function() {
        // Process or submit the collected data
        console.log(this.data); // Example action: log the collected data for demonstration
        // Here, you can make an AJAX call to submit the collected data, for instance

    },
    unmount: function() {
        modal.show('servers_window');
    }
}
</script>
</div>
<?php
}
?>