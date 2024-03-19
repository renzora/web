var game = {
    canvas: undefined,
    ctx: undefined,
    lastTime: 0,
    worldWidth: 480,
    worldHeight: 480,
    zoomLevel: 6,
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

    isColliding: function(rect1, rect2, baseBuffer = 4) {
        const adjustedBuffer = baseBuffer / this.zoomLevel * sprite.scale;
        if (!rect2.w) {
            return rect1.x < rect2.x + rect2.width - adjustedBuffer &&
                   rect1.x + rect1.width > rect2.x + adjustedBuffer &&
                   rect1.y < rect2.y + rect2.height - adjustedBuffer &&
                   rect1.y + rect1.height > rect2.y + adjustedBuffer;
        }
    },
    
    render: function() {
        this.ctx.clearRect(0, 0, this.worldWidth, this.worldHeight);
        this.ctx.setTransform(1, 0, 0, 1, 0, 0);
        this.ctx.scale(this.zoomLevel, this.zoomLevel);
        this.ctx.translate(-Math.round(this.cameraX), -Math.round(this.cameraY));
    
        let renderQueue = [];
    
        if(this.roomData && this.roomData.items && this.roomData.items.length > 0) {
            let positionGroups = {};
    
            this.roomData.items.forEach(roomItem => {
                const itemDef = assets.load('items')[roomItem.id];
                if(itemDef) {
                    roomItem.p.forEach((position, index) => {
                        if(index < itemDef.layout.length && index < itemDef.tiles.length) {
                            const layout = itemDef.layout[index];
                            const zValue = layout.z || position.z;
                
                            renderQueue.push({
                                tileIndex: itemDef.tiles[index],
                                posX: parseInt(position.x, 10),
                                posY: parseInt(position.y, 10),
                                z: zValue,
                                walkable: position.w,
                                draw: function() {
                                    const srcX = (this.tileIndex % 150) * 16;
                                    const srcY = Math.floor(this.tileIndex / 150) * 16;
                                    game.ctx.drawImage(assets.load('tileset'), srcX, srcY, 16, 16, this.posX * 16, this.posY * 16, 16, 16);
                                }
                            });
                        }
                    });
                }
            });
    
            // Sort each group by z and then flatten the groups into renderQueue
            Object.values(positionGroups).forEach(group => {
                group.sort((a, b) => a.z - b.z); // Sort by z within each group
                // Merge the sorted group back into renderQueue
                renderQueue = renderQueue.concat(group);
            });

            // Add the sprite to the renderQueue with a z-index of 1
            renderQueue.push({
                z: 1, // Sprite's z-index
                draw: function() {
                    sprite.draw();
                }
            });
        
            // Sort the entire renderQueue by z-index
            renderQueue.sort((a, b) => a.z - b.z);

            renderQueue.forEach(tile => {
                tile.draw();
            });
            
        }
    
        this.ctx.imageSmoothingEnabled = false;
    }
};
