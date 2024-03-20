var game = {
    canvas: undefined,
    ctx: undefined,
    lastTime: 0,
    worldWidth: 480,
    worldHeight: 480,
    zoomLevel: 5,
    cameraX: 0,
    cameraY: 0,
    roomData: undefined,

    init: function () {

        assets.preload([
            { name: 'sprite', path: 'img/sprites/test_character2.png' },
            { name: 'tileset', path: 'img/sprites/items.png' },
            { name: 'items', path: 'json/items.json' },
            { name: 'roomData', path: 'json/roomData.json' },
        ], () => {
            console.log("All assets loaded");
            this.canvas = document.createElement('canvas');
            this.ctx = this.canvas.getContext('2d');
            document.body.appendChild(this.canvas);
            this.resizeCanvas();
            this.roomData = assets.load('roomData');
            this.loop();
        });

    },

    resizeCanvas: function() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    },

    loop: function(timestamp) {
        var deltaTime = timestamp - this.lastTime;
        this.lastTime = timestamp;
        this.update(deltaTime);
        this.render();
        requestAnimationFrame(this.loop.bind(this));
    },
    
    update: function(deltaTime) {
        sprite.update();

        
        this.camera();
    },

    camera: function() {
        // Calculate the scaled window dimensions
        var scaledWindowWidth = window.innerWidth / game.zoomLevel;
        var scaledWindowHeight = window.innerHeight / game.zoomLevel;
        
        // Check if the world dimensions are smaller than the canvas dimensions
        if (game.worldWidth < scaledWindowWidth || game.worldHeight < scaledWindowHeight) {
            // Calculate the difference and divide by 2 to center
            var xOffset = game.worldWidth < scaledWindowWidth ? (scaledWindowWidth - game.worldWidth) / 2 : 0;
            var yOffset = game.worldHeight < scaledWindowHeight ? (scaledWindowHeight - game.worldHeight) / 2 : 0;
            
            // Adjust camera to center the map
            game.cameraX = -xOffset;
            game.cameraY = -yOffset;
        } else {
            // Center the camera on the sprite, considering the scaled window size
            this.cameraX = sprite.x + sprite.size / 2 - scaledWindowWidth / 2;
            this.cameraY = sprite.y + sprite.size / 2 - scaledWindowHeight / 2;
            
            // Ensure the camera doesn't go outside the world bounds
            this.cameraX = Math.max(0, Math.min(this.cameraX, game.worldWidth - scaledWindowWidth));
            this.cameraY = Math.max(0, Math.min(this.cameraY, game.worldHeight - scaledWindowHeight));
        }
    },

    collision: function(proposedX, proposedY) {
        let collisionDetected = false;
        if(game.roomData && game.roomData.items) {
            collisionDetected = game.roomData.items.some(roomItem => {
                const itemTiles = assets.load('items')[roomItem.id];
                if (!itemTiles) return false;
    
                return roomItem.p.some((position, index) => {
                    const tile = itemTiles[index];
                    if(tile && Array.isArray(tile.w) && tile.w.length === 4) {
                        const [nOffset, eOffset, sOffset, wOffset] = tile.w;
                        const tileRect = {
                            x: parseInt(position.x, 10) * 16, // Absolute X position
                            y: parseInt(position.y, 10) * 16, // Absolute Y position
                            width: 16,
                            height: 16
                        };
                        const spriteRect = {
                            x: proposedX,
                            y: proposedY,
                            width: sprite.size * sprite.scale,
                            height: sprite.size * sprite.scale
                        };
    
                        return spriteRect.x < tileRect.x + tileRect.width - eOffset &&
                               spriteRect.x + spriteRect.width > tileRect.x + wOffset &&
                               spriteRect.y < tileRect.y + tileRect.height - sOffset &&
                               spriteRect.y + spriteRect.height > tileRect.y + nOffset;
                    }
                    return false;
                });
            });
        }
        return collisionDetected;
    },

    grid: function() {
        this.ctx.strokeStyle = 'rgba(204, 204, 204, 0.2)';
        this.ctx.lineWidth = 1;
    
        for(var x = 0; x <= this.worldWidth; x += 16) {
            this.ctx.beginPath();
            this.ctx.moveTo(x, 0);
            this.ctx.lineTo(x, this.worldHeight);
            this.ctx.stroke();
        }
    
        for(var y = 0; y <= this.worldHeight; y += 16) {
            this.ctx.beginPath();
            this.ctx.moveTo(0, y);
            this.ctx.lineTo(this.worldWidth, y);
            this.ctx.stroke();
        }
    },
    
    
    render: function() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.ctx.setTransform(1, 0, 0, 1, 0, 0);
        this.ctx.scale(this.zoomLevel, this.zoomLevel);
        this.ctx.translate(-Math.round(this.cameraX), -Math.round(this.cameraY));
        this.grid();
    
        let renderQueue = [];
    
        // Assuming roomData.items is properly loaded and structured
        this.roomData.items.forEach(roomItem => {
            const itemTiles = assets.load('items')[roomItem.id];
            if (itemTiles) {
                roomItem.p.forEach((position, index) => {
                    const tile = itemTiles[index];
                    if(tile) {
                        const posX = parseInt(position.x, 10) * 16; // Absolute X position
                        const posY = parseInt(position.y, 10) * 16; // Absolute Y position
    
                        renderQueue.push({
                            tileIndex: tile.t,
                            posX: posX,
                            posY: posY,
                            z: tile.z, // Use the tile's z-index
                            draw: function() {
                                const srcX = (this.tileIndex % 150) * 16;
                                const srcY = Math.floor(this.tileIndex / 150) * 16;
                                game.ctx.drawImage(assets.load('tileset'), srcX, srcY, 16, 16, this.posX, this.posY, 16, 16);
                            }
                        });
                    }
                });
            }
        });
    
        // Sprite rendering logic remains unchanged
        renderQueue.push({
            z: 1,
            draw: function() {
                sprite.draw();
            }
        });
    
        // Sort and draw the render queue
        renderQueue.sort((a, b) => a.z - b.z);
        renderQueue.forEach(item => item.draw());
    
        this.ctx.imageSmoothingEnabled = false;
    }
};
