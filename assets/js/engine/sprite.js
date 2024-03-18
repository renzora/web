var sprite = {
    x: 180,
    y: 200,
    size: 32,
    scale: 0.7,
    speed: 1.5,
    currentFrame: 0,
    direction: 'S',
    animationSpeed: 0.1,
    frameCounter: 0,
    directionMap: {
        'N': 0,
        'NE': 1,
        'E': 2,
        'SE': 3,
        'S': 4,
        'SW': 5,
        'W': 6,
        'NW': 7
    },
    directions: {},

    draw: function() {
        let image = assets.load('sprite');
        if (!image) return;
        let directionRow = this.directionMap[this.direction] ?? 4;
        let frameColumn = Math.floor(this.currentFrame);
    
        let sx = frameColumn * this.size;
        let sy = directionRow * this.size;
    
        game.ctx.save();
        game.ctx.translate(this.x, this.y);
        game.ctx.scale(this.scale, this.scale);
        game.ctx.drawImage(image, sx, sy, this.size, this.size, 0, 0, this.size, this.size);
        game.ctx.restore();
    
        this.frameCounter += this.animationSpeed;
        if (this.frameCounter >= 1) {
            this.currentFrame = (this.currentFrame + 1) % 5;
            this.frameCounter = 0;
        }
    },
    

    addDirection: function(direction) {
        this.directions[direction] = true;
        this.updateDirection();
    },
    
    removeDirection: function(direction) {
        delete this.directions[direction];
        this.updateDirection();
    },
    
    updateDirection: function() {
        if (this.directions['up']) this.direction = 'N';
        if (this.directions['down']) this.direction = 'S';
        if (this.directions['left']) this.direction = 'W';
        if (this.directions['right']) this.direction = 'E';
        if (this.directions['up'] && this.directions['right']) this.direction = 'NE';
        if (this.directions['down'] && this.directions['right']) this.direction = 'SE';
        if (this.directions['down'] && this.directions['left']) this.direction = 'SW';
        if (this.directions['up'] && this.directions['left']) this.direction = 'NW';
    },

    update: function() {
        let moving = Object.keys(this.directions).length > 0;
    
        let proposedX = this.x + (this.directions['right'] ? this.speed : 0) - (this.directions['left'] ? this.speed : 0);
        let proposedY = this.y + (this.directions['down'] ? this.speed : 0) - (this.directions['up'] ? this.speed : 0);
    
        // Prepare for checking collision with dynamic tiles that are not w
        let collisionWithDynamic = false;
        if (game.roomData && game.roomData.items) {
            collisionWithDynamic = game.roomData.items.some(roomItem => {
                // Only consider tiles marked as non-w
                return roomItem.p.some(position => {
                    if (position.w === 0) { // Check if the tile is not walkable
                        const tileRect = {
                            x: parseInt(position.x, 10) * 16,
                            y: parseInt(position.y, 10) * 16,
                            width: 16,
                            height: 16
                        };
                        return game.isColliding(
                            {x: proposedX, y: proposedY, width: this.size * this.scale, height: this.size * this.scale},
                            tileRect,
                            10 * game.zoomLevel
                        );
                    }
                    return false; // If the tile is walkable, ignore it
                });
            });
        }
    
        // Apply movement if there's no collision detected
        if (!collisionWithDynamic && moving) {
            this.x = Math.max(0, Math.min(proposedX, game.worldWidth - (this.size * this.scale)));
            this.y = Math.max(0, Math.min(proposedY, game.worldHeight - (this.size * this.scale)));
    
            this.frameCounter += this.animationSpeed;
            if (this.frameCounter >= 1) {
                this.currentFrame = (this.currentFrame + 1) % 5;
                this.frameCounter = 0;
            }
        } else if (!moving) {
            this.frameCounter = 0;
            this.currentFrame = 0;
        }
        console.log(this.x, this.y);
    }
};
