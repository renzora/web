var sprite = {
    details: {
        frameWidth: 32,
        frameHeight: 32,
        rows: 6,
        columns: 5,
        directions: {
            up: 0,
            upRight: 1,
            right: 2,
            downRight: 3,
            down: 4,
            downLeft: 5,
            left: 6,
            upLeft: 7
        }
    },
    speed: 3,
    currentFrame: 0,
    facing: 'down',
    animationSpeed: 60, // Time in milliseconds per frame
    lastFrameTime: 0,

    render: function() {
        // Determine new direction based on the last pressed direction
        if (input.pressedDirections.length > 0) {
            this.facing = input.pressedDirections[input.pressedDirections.length - 1];
        }
    
        let newX = scene.x;
        let newY = scene.y;
    
        // Calculate new position based on the current pressed directions
        input.pressedDirections.forEach(direction => {
            const diagonalSpeed = this.speed * 0.7071; // Adjust speed for diagonal movement
        
            // Check if the current direction is a diagonal one
            const isDiagonalMovement = ['upRight', 'downRight', 'downLeft', 'upLeft'].includes(direction);
        
            // Apply adjusted speed if moving diagonally, else use normal speed
            const currentSpeed = isDiagonalMovement ? diagonalSpeed : this.speed;
        
            switch (direction) {
                case 'up': newY -= currentSpeed; break;
                case 'down': newY += currentSpeed; break;
                case 'left': newX -= currentSpeed; break;
                case 'right': newX += currentSpeed; break;
                case 'upRight': newY -= diagonalSpeed; newX += diagonalSpeed; break;
                case 'downRight': newY += diagonalSpeed; newX += diagonalSpeed; break;
                case 'downLeft': newY += diagonalSpeed; newX -= diagonalSpeed; break;
                case 'upLeft': newY -= diagonalSpeed; newX -= diagonalSpeed; break;
            }
        });
    
        // Correctly calculate the maximum x and y values
        const maxX = game.canvasWidth - this.details.frameWidth * scene.pixelSize;
        const maxY = game.canvasHeight - this.details.frameHeight * scene.pixelSize;
    
        // Ensure newX and newY are within the corrected canvas boundaries
        scene.x = Math.max(0, Math.min(newX, maxX));
        scene.y = Math.max(0, Math.min(newY, maxY));
    
        this.updateAnimation();
        game.ctx.clearRect(0, 0, game.canvasWidth, game.canvasHeight); // Make sure to clear the correct area
        this.draw();
    },

    updateAnimation: function() {
        if (input.pressedDirections.length > 0) {
            this.lastFrameTime += scene.delta * 1000; // Convert delta time to milliseconds
            while (this.lastFrameTime >= this.animationSpeed) {
                this.currentFrame = (this.currentFrame + 1) % this.details.columns;
                this.lastFrameTime -= this.animationSpeed;
            }
        } else {
            this.currentFrame = 0;
        }
    },

    draw: function() {
        const row = this.details.directions[this.facing];
        const srcX = this.currentFrame * this.details.frameWidth;
        const srcY = row * this.details.frameHeight;

        // Draw the sprite frame based on the current direction and frame
        game.ctx.drawImage(assets.load('sprite'), srcX, srcY, this.details.frameWidth, this.details.frameHeight, scene.x * scene.pixelSize, scene.y * scene.pixelSize, this.details.frameWidth * scene.pixelSize, this.details.frameHeight * scene.pixelSize);
    },

    addDirection: function(direction) {
        if (!input.pressedDirections.includes(direction)) {
            input.pressedDirections.push(direction);
        }
    },
    
    removeDirection: function(...dirs) {
        input.pressedDirections = input.pressedDirections.filter(dir => !dirs.includes(dir));
    },

    updateDiagonalDirections: function() {
        // Clear existing diagonal directions first
        this.removeDirection('upRight', 'downRight', 'downLeft', 'upLeft');
    
        // Now check and add diagonal directions based on current pressed keys
        const hasUp = input.pressedDirections.includes('up');
        const hasDown = input.pressedDirections.includes('down');
        const hasLeft = input.pressedDirections.includes('left');
        const hasRight = input.pressedDirections.includes('right');
    
        if (hasUp && hasRight) this.addDirection('upRight');
        if (hasDown && hasRight) this.addDirection('downRight');
        if (hasDown && hasLeft) this.addDirection('downLeft');
        if (hasUp && hasLeft) this.addDirection('upLeft');
    }
};