var sprite = {
    x: 180,
    y: 250,
    size: 32,
    scale: 0.7,
    speed: 90,
    currentFrame: 11,
    direction: 'S',
    animationSpeed: 0.2,
    frameCounter: 0,
    moving: false,
    stopping: false,
    isStopping: false,
    movementFrameCounter: 0,
    deaccelerationThreshold: 2,
    deaccelerationRate: 0.0001,
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
        let shadowWidth = this.size * this.scale * 0.22;
        let shadowHeight = this.size * this.scale * 0.15;
    
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
        
    },

    update: function() {
        let deltatime = game.deltaTime / 1000;
    
        let dx = 0;
        let dy = 0;
    
        if(this.directions['right']) dx += this.speed * deltatime;
        if(this.directions['left']) dx -= this.speed * deltatime;
        if(this.directions['down']) dy += this.speed * deltatime;
        if(this.directions['up']) dy -= this.speed * deltatime;
    
        // Normalize diagonal speed
        if(dx !== 0 && dy !== 0) {
            const norm = Math.sqrt(dx * dx + dy * dy);
            dx = (dx / norm) * this.speed * deltatime;
            dy = (dy / norm) * this.speed * deltatime;
        }
    
        // Apply deceleration
        if(dx !== 0 || dy !== 0) {
            this.movementFrameCounter += deltatime;
            this.isStopping = false;
        } else {
            if(this.movementFrameCounter > this.deaccelerationThreshold) {
                this.isStopping = true;
            }
        }
    
        if(!this.isStopping) {
            this.vx = dx;
            this.vy = dy;
        } else {
            this.vx *= Math.pow(this.deaccelerationRate, deltatime);
            this.vy *= Math.pow(this.deaccelerationRate, deltatime);
    
            if(Math.abs(this.vx) < 0.01 && Math.abs(this.vy) < 0.01) {
                this.vx = 0;
                this.vy = 0;
                this.isStopping = false;
                this.movementFrameCounter = 0;
            }
        }
    
        let newX = this.x + this.vx;
        let newY = this.y + this.vy;
    
        // Collision check before applying new position
        if(!game.collision(newX, newY)) {
            this.x = newX;
            this.y = newY;
        }
    
        // Ensure sprite stays within world bounds
        this.x = Math.max(0, Math.min(this.x, game.worldWidth - this.size * this.scale));
        this.y = Math.max(0, Math.min(this.y, game.worldHeight - this.size * this.scale));
    
        this.animate();
    
        if(dx === 0 && dy === 0 && !this.isStopping) {
            this.movementFrameCounter = 0;
        }
    }
};