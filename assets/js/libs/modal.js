class Draggable {
  static modals = [];
  static baseZIndex;

  constructor(selector, options) {
    this.element = document.querySelector(selector);
    if (!this.element) {
      console.error(`No element found for selector: ${selector}`);
      return;
    }

    this.options = options;

    // Initialize baseZIndex the first time an instance is created
    if (Draggable.modals.length === 0) {
      Draggable.baseZIndex = Draggable.topZIndex() + 1;
    }

    // Initialize draggable functionality
    this.initDraggable();

    // Assign z-index for the new modal
    const highestZIndex = Draggable.baseZIndex + Draggable.modals.length;
    this.element.style.zIndex = highestZIndex.toString();

    // Add this modal to the array of modals
    Draggable.modals.push(this);

    // Set up event listener to bring this modal to the front when clicked
    this.element.addEventListener('click', () => this.bringToFront());
  }
  
  bringToFront() {
    // Move this modal to the end of the list
    Draggable.modals = Draggable.modals.filter(modal => modal !== this);
    Draggable.modals.push(this);
  
    // Reassign z-index for all modals
    Draggable.modals.forEach((modal, index) => {
      modal.element.style.zIndex = (Draggable.baseZIndex + index).toString();
    });
  }

    centerElement() {
        if(this.element.offsetWidth === 0 || this.element.offsetHeight === 0) {
            window.requestAnimationFrame(this.centerElement.bind(this));
            return;
        }
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const rect = this.element.getBoundingClientRect();
        const centerX = (viewportWidth - rect.width) / 2;
        const centerY = (viewportHeight - rect.height) / 2;
      
        this.element.style.position = 'absolute';
        this.element.style.left = `${centerX}px`;
        this.element.style.top = `${centerY}px`;
    }
  
    initDraggable() {
        let isDragging = false;
        let originalX, originalY, translateX = 0, translateY = 0, mouseX, mouseY;
  
        const onMouseDown = (e) => {
        if(e.target.closest('.window_body')) {
            return;
        }

        if (e.target.closest('.resize-handle')) {
          return;
      }
        
        isDragging = true;
        originalX = this.element.offsetLeft;
        originalY = this.element.offsetTop;
    
        mouseX = e.clientX;
        mouseY = e.clientY;
        document.onselectstart = function() { return false; };
        document.body.style.userSelect = 'none';
        this.bringToFront();
    
        if(this.options.stack) {
            this.element.style.zIndex = this.options.stack();
        }
        if(this.options.start) {
            this.options.start.call(this.element, e);
        }
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    };
  
    const onMouseMove = (e) => {
        if(!isDragging) return;
  
        let dx = e.clientX - mouseX;
        let dy = e.clientY - mouseY;
        let newLeft = originalX + dx;
        let newTop = originalY + dy;
  
        const windowBoundaries = {
            left: window.scrollX,
            top: window.scrollY,
            right: window.scrollX + window.innerWidth,
            bottom: window.scrollY + window.innerHeight
        };
  
        const rect = this.element.getBoundingClientRect();
  
        newLeft = Math.max(windowBoundaries.left, Math.min(newLeft, windowBoundaries.right - rect.width));
        newTop = Math.max(windowBoundaries.top, Math.min(newTop, windowBoundaries.bottom - rect.height));
  
        this.element.style.left = `${newLeft}px`;
        this.element.style.top = `${newTop}px`;
  
        if(this.options.drag) {
            this.options.drag.call(this.element, e);
        }
    };
  
    const onMouseUp = (e) => {
        isDragging = false;
        document.onselectstart = null;
        document.body.style.userSelect = '';
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
        if(this.options.stop) {
          this.options.stop.call(this.element, e);
        }
      };
  
      this.element.addEventListener('mousedown', onMouseDown);
    }

    static topZIndex() {
      const highestZIndex = Array.from(document.querySelectorAll('*'))
        .map(el => parseFloat(window.getComputedStyle(el).zIndex))
        .filter(zIndex => !isNaN(zIndex))
        .reduce((max, zIndex) => Math.max(max, zIndex), 0);
    
      return highestZIndex;
    }

  }