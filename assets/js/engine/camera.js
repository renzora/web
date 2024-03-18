var camera = {
    x: 0,
    y: 0,
    
    update: function() {
        // Calculate the scaled window dimensions
        var scaledWindowWidth = window.innerWidth / game.zoomLevel;
        var scaledWindowHeight = window.innerHeight / game.zoomLevel;
        
        // Center the camera on the sprite, considering the scaled window size
        this.x = sprite.x + sprite.size / 2 - scaledWindowWidth / 2;
        this.y = sprite.y + sprite.size / 2 - scaledWindowHeight / 2;
        
        // Ensure the camera doesn't go outside the world bounds
        this.x = Math.max(0, Math.min(this.x, game.worldWidth - scaledWindowWidth));
        this.y = Math.max(0, Math.min(this.y, game.worldHeight - scaledWindowHeight));
    }
};