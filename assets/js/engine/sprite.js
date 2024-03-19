var sprite = {
    x: 180,
    y: 200,
    size: 32,
    scale: 0.7,
    speed: 1.5,
    currentFrame: 11,
    direction: 'S',
    animationSpeed: 0.2,
    frameCounter: 0,
    moving: false,
    stopping: false,
    isStopping: false,
    movementFrameCounter: 0,
    deaccelerationThreshold: 90,
    deaccelerationRate: 0.88,
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
        if(!image) return;

        let directionRow = this.directionMap[this.direction] ?? 4;
        let frameColumn = Math.floor(this.currentFrame);
        let sx = frameColumn * this.size;
        let sy = directionRow * this.size;
        let shadowWidth = this.size * this.scale * 0.3;
        let shadowHeight = this.size * this.scale * 0.18;
    
        game.ctx.save();
        game.ctx.translate(this.x, this.y);
        game.ctx.shadowBlur = 15;  
        game.ctx.fillStyle = 'rgba(0, 0, 0, 0.15)'; 
        game.ctx.beginPath();
        game.ctx.ellipse(11, 20, shadowWidth, shadowHeight, 0, 0, 2 * Math.PI);
        game.ctx.fill();
        game.ctx.scale(this.scale, this.scale);
        game.ctx.drawImage(image, sx, sy, this.size, this.size, 0, 0, this.size, this.size);
        game.ctx.restore();
    },

    addDirection: function(direction) {
        this.directions[direction] = true;
        this.updateDirection();
        this.moving = true;
        this.stopping = false;
    },
    
    removeDirection: function(direction) {
        delete this.directions[direction];
        this.updateDirection();
        if(Object.keys(this.directions).length === 0) {
            this.stopping = true;
        }
    },
    
    updateDirection: function() {
        if(this.directions['up']) this.direction = 'N';
        if(this.directions['down']) this.direction = 'S';
        if(this.directions['left']) this.direction = 'W';
        if(this.directions['right']) this.direction = 'E';
        if(this.directions['up'] && this.directions['right']) this.direction = 'NE';
        if(this.directions['down'] && this.directions['right']) this.direction = 'SE';
        if(this.directions['down'] && this.directions['left']) this.direction = 'SW';
        if(this.directions['up'] && this.directions['left']) this.direction = 'NW';
    },

    animate: function() {
        if(this.moving) {
            this.frameCounter += this.animationSpeed;
            if(this.stopping) {
                if(this.currentFrame < 9 || this.currentFrame > 11) {
                    this.currentFrame = 9;
                } else if(this.frameCounter >= 1) {
                    this.currentFrame = Math.min(this.currentFrame + 1, 11);
                    this.frameCounter = 0;
                }
            } else if(this.currentFrame <= 2 || this.currentFrame >= 9) {
                this.currentFrame = 3; // Start loop animation
            } else if(this.frameCounter >= 1) {
                if(this.currentFrame < 8) {
                    this.currentFrame++;
                } else {
                    this.currentFrame = 3; // Loop back to the start of the loop animation
                }
                this.frameCounter = 0;
            }
        } else if(this.stopping && this.frameCounter >= 1) {
            if(this.currentFrame < 11) {
                this.currentFrame++;
            } else {
                this.stopping = false; // Stop animation completed
            }
            this.frameCounter = 0;
        }
        console.log(this.currentFrame);
        
    },

    update: function() {
        let dx = 0;
        let dy = 0;
    
        if(this.directions['right']) dx += this.speed;
        if(this.directions['left']) dx -= this.speed;
        if(this.directions['down']) dy += this.speed;
        if(this.directions['up']) dy -= this.speed;
    
        if(dx !== 0 || dy !== 0) {
            this.movementFrameCounter++;
            this.isStopping = false;
        } else {
            if(this.movementFrameCounter > this.deaccelerationThreshold) {
                this.isStopping = true;
            }
        }
    
        if(dx !== 0 && dy !== 0) {
            dx /= Math.sqrt(2);
            dy /= Math.sqrt(2);
        }
    
        if(!this.isStopping) {
            this.vx = dx;
            this.vy = dy;
        } else {
            this.vx *= this.deaccelerationRate;
            this.vy *= this.deaccelerationRate;
    
            if(Math.abs(this.vx) < 0.01 && Math.abs(this.vy) < 0.01) {
                this.vx = 0;
                this.vy = 0;
                this.isStopping = false;
                this.movementFrameCounter = 0;
            }
        }
    
        let x = this.x + this.vx;
        let y = this.y + this.vy;

        if(!game.collision(x, y)) {
            this.x = x;
            this.y = y;
        }
    
        this.x = Math.max(0, Math.min(this.x, game.worldWidth - this.size * this.scale));
        this.y = Math.max(0, Math.min(this.y, game.worldHeight - this.size * this.scale));
    
        this.animate();
    
        if(dx === 0 && dy === 0 && !this.isStopping) {
            this.movementFrameCounter = 0;
        }
    }
};