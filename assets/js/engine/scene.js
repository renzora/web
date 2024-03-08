// Scene utility
var scene = {
    start: function(id) {
        return new Promise((resolve, reject) => {

        ui.load({
            outputType: 'json',
            method: 'GET',
            url: 'ajax/scene/fetchData.php',
            data: 'id=' + id,
            success: function(data) {
                this.roomId = null;
                this.pixelSize = 1;
                this.tileSize = 16;
                this.tileWidth = this.tileSize * this.pixelSize;
                this.tileHeight = this.tileSize * this.pixelSize;
                this.x = null;
                this.y = null;
                this.previousMs = undefined;
                this.stepTime = 1 / 60;
                this.spriteScale = 1;
                this.collisionData = data.collision;
                this.objectData = data.items;
                this.renData = data.renscript;
                this.numCol = data.numX;
                this.numRow = data.numY;
                this.roomId = parseInt(id, 10);
                game.render();

                var message = {
                    command: 'serverChange',
                    room: scene.roomId,
                    token: network.getToken('renaccount')
                };
                network.send(message);

            }.bind(this),
                error: function(error) {
                console.error("Error loading room data:", error);
                reject(error);
            }
        });

       input.init(this);

       console.log('new scene initiated');

    resolve();
    console.log("promise resolved and scene started");

});

    },

    tick: function(timestampMs) {
        if (this.previousMs === undefined) {
            this.previousMs = timestampMs;
            this.lastFps = 60; // Initialize with a reasonable default FPS
        }
    
        if (!this.fpsCounterStart) {
            this.fpsCounterStart = timestampMs; // Start of FPS counting period
            this.frameCount = 0; // Initialize frame count
            this.fps = 0; // Initialize FPS
        }
    
        this.frameCount++;
    
        // Calculate delta time in seconds
        this.delta = (timestampMs - this.previousMs) / 1000;
    
        // Cap the delta time to a maximum value, e.g., 0.1 seconds (100 ms)
        // This prevents the game from trying to catch up with a huge backlog of updates
        // if the tab was inactive and then becomes active again.
        const maxDelta = 0.1; // Maximum delta time in seconds
        if (this.delta > maxDelta) {
            this.delta = maxDelta;
        }
    
        // Process game logic updates in fixed steps
        while (this.delta >= this.stepTime) {
            sprite.render();
            this.delta -= this.stepTime;
        }
    
        // Ensure stepTime is defined and not zero to avoid NaN in calculations
        if (typeof this.stepTime === 'undefined' || this.stepTime <= 0) {
            this.stepTime = 1/60; // Default stepTime to 60 FPS equivalent if not properly initialized
        }
    
        // Update FPS every frame
        let frameTime = timestampMs - this.previousMs;
        if (frameTime > 0) {
            this.fps = 1000 / frameTime;
            // Smooth the FPS calculation over a few frames
            this.fps = (this.fps + this.lastFps) / 2;
        } else {
            this.fps = this.lastFps; // Use last FPS if frameTime is 0
        }
    
        this.previousMs = timestampMs;
        this.lastFps = this.fps; // Store the last calculated FPS for smoothing
    
        debug.fps();
    
        // Request the next frame
        requestAnimationFrame((timestamp) => this.tick(timestamp));
    }
};

