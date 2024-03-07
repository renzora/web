var game = {
    init: function() {
        assets.preload([
            { name: 'sprite', path: 'img/sprites/test_character.png' },
            { name: 'tileset', path: 'img/sprites/items.png' },
            { name: 'items', path: 'json/items.json' }
        ], function() {
            console.log("All assets loaded");
        });
    },
    render: function() {
        console.log("draw room called");
        console.time("TileDrawingTime"); // Start the timer
    
        // Check if the canvas already exists, if not create and append it
        let sceneCanvas = document.querySelector('.render');
        if(!sceneCanvas) {
            sceneCanvas = document.createElement('canvas'); // Create a new canvas element
            sceneCanvas.className = 'render'; // Assign the class name to the canvas
            document.body.appendChild(sceneCanvas); // Append the canvas to the body
        }

        // Get the container's current size
        const containerWidth = sceneCanvas.parentElement.offsetWidth;
        const containerHeight = sceneCanvas.parentElement.offsetHeight;
    
        // Check if the canvas size matches the container's size
        if (sceneCanvas.width !== containerWidth || sceneCanvas.height !== containerHeight) {
            // Resize the canvas to fill its container
            sceneCanvas.width = containerWidth;
            sceneCanvas.height = containerHeight;
            this.canvasWidth = containerWidth;
            this.canvasHeight = containerHeight;
            // Note: Resizing the canvas clears it, so there's no need for clearRect()
        } else {
            // Clear the canvas if not resizing (since resizing clears automatically)
            this.ctx.clearRect(0, 0, this.canvasWidth, this.canvasHeight);
        }
    
        this.ctx = sceneCanvas.getContext('2d');
        this.ctx.imageSmoothingEnabled = false; // Disable image smoothing
    
    

        // Prepare items for rendering by z-index
        if (scene.objectData && Array.isArray(scene.objectData.items) && scene.objectData.items.length > 0) {
            console.log("there are items to draw");

            // Flatten item positions with z-index, tile index, and position for sorting
            let renderQueue = [];
            scene.objectData.items.forEach(roomItem => {
                const itemDef = assets.load('items')[roomItem.id];
                if (itemDef) {
                    roomItem.position.forEach((position, index) => {
                        const layout = itemDef.layout[index];
                        renderQueue.push({
                            tileIndex: itemDef.tiles[index],
                            posX: position.x,
                            posY: position.y,
                            zindex: layout.zindex
                        });
                    });
                }
            });

            // Sort the render queue by z-index
            renderQueue.sort((a, b) => a.zindex - b.zindex);

            // Render each tile in the sorted render queue
            renderQueue.forEach(tile => {
                const srcX = (tile.tileIndex % tilesPerColumn) * tileSize;
                const srcY = Math.floor(tile.tileIndex / tilesPerColumn) * tileSize;
                const posX = Math.round(tile.posX * displayTileSize);
                const posY = Math.round(tile.posY * displayTileSize);

                this.ctx.drawImage(assets.load('tileset'), srcX, srcY, scene.tileSize, scene.tileSize, posX, posY, displayTileSize, displayTileSize);
            });
        } else {
            console.log("No items to draw - canvas cleared");
        }
        
        console.timeEnd("TileDrawingTime");

        requestAnimationFrame((timestamp) => scene.tick(timestamp));
    },
    grid: function() {
        const newBackgroundSize = scene.tileSize * scene.pixelSize + 'px ' + scene.tileSize * scene.pixelSize + 'px';
    
        // Select the element with the class 'grid_tiles' and update its background size
        const gridTilesElements = document.querySelectorAll('.grid_tiles');
        gridTilesElements.forEach(element => {
            element.style.backgroundSize = newBackgroundSize;
        });
    },
}