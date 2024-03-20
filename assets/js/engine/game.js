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
        if(game.worldWidth < scaledWindowWidth || game.worldHeight < scaledWindowHeight) {
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
                return roomItem.p.some(position => {
                    if(Array.isArray(position.w) && position.w.length === 4) {
                        // Directly use the boundary values (N,E,S,W) from the array
                        const [nOffset, eOffset, sOffset, wOffset] = position.w;
    
                        const tileRect = {
                            x: parseInt(position.x, 10) * 16,
                            y: parseInt(position.y, 10) * 16,
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
        this.ctx.clearRect(0, 0, this.worldWidth, this.worldHeight);
        this.ctx.setTransform(1, 0, 0, 1, 0, 0);
        this.ctx.scale(this.zoomLevel, this.zoomLevel);
        this.ctx.translate(-Math.round(this.cameraX), -Math.round(this.cameraY));
        this.grid();

        let renderQueue = [];
    
        if(this.roomData && this.roomData.items && this.roomData.items.length > 0) {
            let positionGroups = {};

            this.roomData.items.forEach(roomItem => {
                const itemDef = assets.load('items')[roomItem.id];
                if(itemDef) {
                    roomItem.p.forEach((position, index) => {
                        if(index < itemDef.layout.length && index < itemDef.tiles.length) {
                            const layout = itemDef.layout[index];
                            let zValue = Array.isArray(layout.z) ? layout.z[2] : layout.z || position.z;

                            const posX = parseInt(position.x, 10);
                            const posY = parseInt(position.y, 10);

                            renderQueue.push({
                                tileIndex: itemDef.tiles[index],
                                posX: posX,
                                posY: posY,
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
    
            Object.values(positionGroups).forEach(group => {
                group.sort((a, b) => a.z - b.z);
                renderQueue = renderQueue.concat(group);
            });

            renderQueue.push({
                z: 1,
                draw: function() {
                    sprite.draw();
                }
            });
        
            renderQueue.sort((a, b) => a.z - b.z);

            renderQueue.forEach(tile => {
                tile.draw();
            });
        }
    
        this.ctx.imageSmoothingEnabled = false;
    }
};
