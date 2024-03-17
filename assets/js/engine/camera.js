var camera = {
    x: 0,
    y: 0,
    
    update: function(sprite, worldWidth, worldHeight) {
        this.x = sprite.x + sprite.size / 2 - window.innerWidth / 2;
        this.y = sprite.y + sprite.size / 2 - window.innerHeight / 2;
        this.x = Math.max(0, Math.min(this.x, game.worldWidth - window.innerWidth));
        this.y = Math.max(0, Math.min(this.y, game.worldHeight - window.innerHeight));
    }
}