var sprite = {
    x: 500, // Initial position
    y: 500,
    size: 32,
    color: 'red',
    speed: 3, // Movement speed pixels per frame

    draw: function(ctx, x, y) {
        ctx.fillStyle = this.color;
        ctx.fillRect(x, y, this.size, this.size);
    },
    

    addDirection: function(direction) {
        this.directions[direction] = true;
    },

    removeDirection: function(direction) {
        delete this.directions[direction];
    },

    update: function(worldWidth, worldHeight, staticObjects) {
        let proposedX = this.x + (this.directions['right'] ? this.speed : 0) - (this.directions['left'] ? this.speed : 0);
        let proposedY = this.y + (this.directions['down'] ? this.speed : 0) - (this.directions['up'] ? this.speed : 0);
    
        let collisionDetected = game.staticObjects.some(obj => {
            return game.isColliding(
                {x: proposedX, y: proposedY, width: this.size, height: this.size},
                obj,
                4 // Include buffer size
            );
        });
    
        if (!collisionDetected) {
            this.x = Math.max(0, Math.min(proposedX, worldWidth - this.size));
            this.y = Math.max(0, Math.min(proposedY, worldHeight - this.size));
        }
        // Handle collisions as needed, depending on game mechanics
    },


    directions: {},
};
