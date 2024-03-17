var game = {
    canvas: undefined,
    ctx: undefined,
    lastTime: 0,
    worldWidth: 2400,
    worldHeight: 2400,
    staticObjects: [
        {x: 100, y: 100, width: 50, height: 50, color: 'blue', zIndex: 1, walkable: false},
        {x: 400, y: 300, width: 80, height: 80, color: 'green', zIndex: 2, walkable: true},
        {x: 800, y: 150, width: 120, height: 60, color: 'yellow', zIndex: 0, walkable: false},
    ],
    init: function () {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        document.body.appendChild(this.canvas);

        this.resizeCanvas();

        assets.preload([
            { name: 'sprite', path: 'img/sprites/test_character.png' },
            { name: 'tileset', path: 'img/sprites/items.png' },
            { name: 'items', path: 'json/items.json' }
        ], () => {
            console.log("All assets loaded");
            this.loop();
        });

    },
    resizeCanvas: function() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
        console.log(this.canvas.width, this.canvas.height);
    },

    loop: function(timestamp) {
        var deltaTime = timestamp - this.lastTime;
        this.lastTime = timestamp;

        this.update(deltaTime);
        this.render();

        requestAnimationFrame(this.loop.bind(this));
        console.log('looping');
    },
    update: function(deltaTime) {
        sprite.update(this.worldWidth, this.worldHeight);
        camera.update(sprite, this.worldWidth, this.worldHeight);
    },

    isColliding: function(rect1, rect2, buffer = 4) {
        // Only apply buffer for non-walkable objects
        if (!rect2.walkable) {
            return rect1.x < rect2.x + rect2.width - buffer &&
                   rect1.x + rect1.width > rect2.x + buffer &&
                   rect1.y < rect2.y + rect2.height - buffer &&
                   rect1.y + rect1.height > rect2.y + buffer;
        }
    },
    
    
    render: function() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    
        // Sort static objects by zIndex
        const sortedObjects = this.staticObjects.sort((a, b) => a.zIndex - b.zIndex);
    
        // Render sorted static objects
        sortedObjects.forEach(obj => {
            let drawX = obj.x - camera.x;
            let drawY = obj.y - camera.y;
            this.ctx.fillStyle = obj.color;
            this.ctx.fillRect(drawX, drawY, obj.width, obj.height);
        });
    
        // Draw the sprite
        let spriteX = sprite.x - camera.x;
        let spriteY = sprite.y - camera.y;
        sprite.draw(this.ctx, spriteX, spriteY);
    },
    
    
};
