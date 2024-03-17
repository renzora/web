document.addEventListener('DOMContentLoaded', (e) => { input.loaded(e) });

var input = {
    pressedDirections: [],
    keys: {
        'ArrowUp': "up",
        'ArrowLeft': "left",
        'ArrowRight': "right",
        'ArrowDown': "down",
    },

    init: function() {
        document.addEventListener("keydown", (e) => this.keyDown(e));
        document.addEventListener("keyup", (e) => this.keyUp(e));
        document.addEventListener('mousedown', (e) => this.mouseDown(e));
        document.addEventListener('mousemove', (e) => this.mouseMove(e));
        document.addEventListener('mouseup', (e) => this.mouseUp(e));
        document.addEventListener('wheel', (e) => this.mouseWheelScroll(e), { passive: false });
        document.addEventListener('click', (e) => this.leftClick(e));
        document.addEventListener('dblclick', (e) => this.doubleClick(e));
        document.addEventListener('contextmenu', (e) => this.rightClick(e));
        window.addEventListener('resize', (e) => game.resizeCanvas(e));
    },


    loaded: function(e) {
        this.init();
        network.init();
    },

    keyDown: function(e) {

        if(e.altKey) {
            if(e.key === 'c') { ui.modal('mishell/index.php', 'mishell_window')}
        } else {
            const dir = this.keys[e.code];
            if (dir) {
                sprite.addDirection(dir);
            }
        }
    },
    
    keyUp: function(e) {
        if(e.keyCode === 27) { // ESC key
            let maxZIndex = -Infinity;
            let maxZIndexElement = null;
            let attributeName = null;
        
            document.querySelectorAll('[data-window]').forEach(function(element) {
                let zIndex = parseInt(window.getComputedStyle(element).zIndex, 10);
                if(zIndex > maxZIndex) {
                    maxZIndex = zIndex;
                    maxZIndexElement = element;
                    attributeName = element.getAttribute('data-window');
                }
            });
        
            if(maxZIndexElement) {
                ui.closeModal(attributeName);
            }
        } else {
            const dir = this.keys[e.code];
            if(dir) {
                sprite.removeDirection(dir);
            }
        }
    },

    mouseDown: function(e) {

        // middle mouse button pressed
        if(e.button === 1) {

        }
    },
    
    mouseUp: function(e) {

    },

    mouseMove: function(e) {

    },

    mouseWheelScroll: function(e) {
        
    },

    leftClick: function(e) {
        console.log("left button clicked");
        if(e.target.matches('[data-close], [data-esc]')) {
            console.log("data close clicked");
            var parent = ui.closestDataWindow(e.target);
            ui.closeModal(parent);
          }
    },
    
    rightClick: function(e, x, y) {
        e.preventDefault();
    },

    doubleClick: function(e, x, y) {

    }
};
