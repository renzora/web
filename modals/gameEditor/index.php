<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
?>
  <div data-window='game_editor_window' class='window window_bg' style='width: 70%; height: 80%; background: #232f33;'>

<div data-part='handle' class='window_title' style='background-image: radial-gradient(#455357 1px, transparent 0) !important;'>
  <div class='float-right'>
    <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
  </div>
  <div data-part='title' class='title_bg window_border' style='background: #232f33; color: #ede8d6;'>Game Editor</div>
</div>
<div class='clearfix'></div>
<div class='relative'>
  <div class='container text-light window_body p-4' style='display: grid; grid-template-columns: 1fr 3fr; gap: 20px;'>

    <div class='left-menu' style='background: #1b2428; padding: 10px; display: flex; flex-direction: column; border-radius: 5px;'>
      <ul class="tab-list cursor-pointer">
        <li class="tab-link active" data-tab="items">Items</li>
        <li class="tab-link" data-tab="tilesheets">Tilesheets</li>
        <li class="tab-link" data-tab="categories">Categories</li>
        <li class="tab-link" data-tab="settings">Settings</li>
      </ul> 
    </div>

    <div class='right-content' style='display: flex; flex-direction: column; align-items: center;'>
      <div id="items" class="tab-content active" style='width: 100%; max-width: 400px; text-align: center;'>
        Items
        <div id="dataDisplay" style='width: 100%; max-width: 400px; text-align: center;'></div>
        <div class="canvas-container" style="position: relative; width: 400px; height: 500px; margin-top: 20px;">
          <canvas id="gameCanvas" width="400px" height="500px"></canvas>
          <canvas id="lineCanvas" width="400px" height="500px" style="position: absolute; left: 0; top: 0;"></canvas>
        </div>
      </div> 
      
      <div id="tilesheets" class="tab-content" style='width: 100%; text-align: center;'>
        add 2 column layout here
      </div> 
      
      <div id="categories" class="tab-content" style='width: 100%; text-align: center;'>
      add 2 column layout here
    </div>
      
      <div id="settings" class="tab-content" style='width: 100%; text-align: center;'>add 2 column layout</div>
    </div>

  </div>

</div>

</div>

<style>
.tab-list { 
  list-style: none; 
  padding: 0;
  margin: 0; 
}

.tab-link { 
  display: block; 
  padding: 10px;
  color: #ede8d6;
  text-decoration: none;
  margin-bottom: 10px;
  background-color: #2a3439;
  border-radius: 5px; 
}

.tab-link.active {
  background-color: #3a4a51; /* Slightly darker background indicates the active tab*/
} 

/* Tab Content */
.tab-content { 
  display: none; 
}

.tab-content.active {
  display: block;
}
</style>

    <script>
var game_editor_window = {
  canvas: null,
  ctx: null,
  lineCanvas: null,
  lineCtx: null,
  tileset: null,
  tileSize: 16,
  zoomLevel: 1,
  currentItemId: null,
  dotPositions: [],
  intersectedTileKeys: [],

  start: function() {
    this.canvas = document.getElementById('gameCanvas');
              this.ctx = this.canvas.getContext('2d');
              this.lineCanvas = document.getElementById('lineCanvas');
              this.lineCtx = this.lineCanvas.getContext('2d');
              this.tileset = assets.load('tileset'); 
              this.displayData(assets.load('items'));
              this.setZoomLevel(2); 
              this.lineCanvas.addEventListener('click', this.drawPoint.bind(this));
    this.initTabs(); // Initialize the tab functionality 
},

unmount: function() {
  const tabLinks = document.querySelectorAll('.tab-link');
  tabLinks.forEach(link => {
      link.removeEventListener('click', this.handleTabClick);
  });

  if (this.lineCanvas && this.boundDrawPoint) {
      this.lineCanvas.removeEventListener('click', this.boundDrawPoint);
  }
},

initTabs: function() {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();

            // Deactivate current tab
            const currentActiveTabLink = document.querySelector('.tab-link.active');
            currentActiveTabLink.classList.remove('active');
            const currentActiveContent = document.querySelector('.tab-content.active');
            currentActiveContent.classList.remove('active');

            // Activate clicked tab
            const targetTab = e.target.dataset.tab;
            e.target.classList.add('active');
            document.getElementById(targetTab).classList.add('active');

        });
    });
},

  setZoomLevel: function(zoomLevel) {
    this.zoomLevel = zoomLevel;
    this.ctx.setTransform(zoomLevel, 0, 0, zoomLevel, 0, 0); 
  },

  loadTileset: function(src) {
    this.tileset = new Image();
    this.tileset.src = src;
  },

  displayData: function(jsonData) {
    const displayDiv = document.getElementById('dataDisplay');
    displayDiv.innerHTML = ''; 

    let selectBox = document.createElement('select');
    selectBox.id = 'itemSelector';
    selectBox.classList.add('form-control', 'px-2');

    let defaultOption = document.createElement('option');
    defaultOption.textContent = 'Select an item';
    defaultOption.value = '';
    selectBox.appendChild(defaultOption);

    Object.keys(jsonData).forEach(key => {
      let option = document.createElement('option');
      option.textContent = key; 
      option.value = key; 
      selectBox.appendChild(option);
    });

    selectBox.addEventListener('change', function() {
      game_editor_window.currentItemId = this.value; // Store the selected item ID
      game_editor_window.displayItemsForCategory(jsonData, this.value);
    });

    displayDiv.appendChild(selectBox);

    let itemsDetailsDiv = document.createElement('div');
    itemsDetailsDiv.id = 'itemsDetails';
    displayDiv.appendChild(itemsDetailsDiv);
  },

  displayItemsForCategory: function(jsonData, selectedCategory) {
    if (!this.tileset.complete) {
      console.error('Tileset image has not loaded yet.');
      return;
    }

    const items = jsonData[selectedCategory];
    if (!items) return;

    this.lineCtx.clearRect(0, 0, this.lineCanvas.width, this.lineCanvas.height); 
    this.dotPositions = []; 

    this.ctx.imageSmoothingEnabled = false;
    const tilesPerRow = Math.floor(this.tileset.naturalWidth / this.tileSize);
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height); 

    items.forEach(item => {
      const t = item.t;
      const srcX = Math.floor(t % tilesPerRow) * this.tileSize;
      const srcY = Math.floor(t / tilesPerRow) * this.tileSize;
      let posX = item.a * this.tileSize * this.zoomLevel;
      let posY = item.b * this.tileSize * this.zoomLevel;
      let tileSizeZoomed = this.tileSize * this.zoomLevel;
      posX = Math.round(posX);
      posY = Math.round(posY);
      tileSizeZoomed = Math.round(tileSizeZoomed);
      this.ctx.drawImage(this.tileset, srcX, srcY, this.tileSize, this.tileSize, posX, posY, tileSizeZoomed, tileSizeZoomed);      
    });

    this.drawGrid();
  },

  getMousePos: function(e) {
    let rect = this.lineCanvas.getBoundingClientRect();
    return [e.clientX - rect.left, e.clientY - rect.top];
  },

  drawPoint: function(e) {
    let [x, y] = this.getMousePos(e); 
    const clickedDot = this.dotPositions.find(dot => this.closeEnough(dot, [x, y]));
    if (clickedDot) {
      this.drawShape(); 
    } else {
      this.dotPositions.push([x, y]); 
      this.lineCtx.beginPath();
      this.lineCtx.arc(x, y, 2, 0, 2 * Math.PI); 
      this.lineCtx.fillStyle = 'red';
      this.lineCtx.fill();
    }   
  },

  drawGrid: function() {
    let gridColor = '#000'; // Grid line color
    let gridSize = this.tileSize * this.zoomLevel; // Grid size adapted to zoom level

    this.ctx.beginPath();
    this.ctx.strokeStyle = gridColor;

    // Drawing vertical lines
    for (let x = 0; x <= this.canvas.width; x += gridSize) {
        this.ctx.moveTo(x, 0);
        this.ctx.lineTo(x, this.canvas.height);
    }

    // Drawing horizontal lines
    for (let y = 0; y <= this.canvas.height; y += gridSize) {
        this.ctx.moveTo(0, y);
        this.ctx.lineTo(this.canvas.width, y);
    }

    this.ctx.stroke();
},

  closeEnough: function(dot1, dot2, tolerance = 5) { 
    const dx = dot1[0] - dot2[0];
    const dy = dot1[1] - dot2[1];
    return Math.sqrt(dx * dx + dy * dy) <= tolerance;
  },

  drawShape: function() {
  if (this.dotPositions.length < 3) return; // Need at least 3 points for a shape

  // Draw the lines for visual representation
  this.lineCtx.beginPath();
  this.lineCtx.moveTo(...this.dotPositions[0]); 
  for (let i = 1; i < this.dotPositions.length; i++) {
    this.lineCtx.lineTo(...this.dotPositions[i]); 
  }
  this.lineCtx.closePath(); 
  this.lineCtx.strokeStyle = 'red';
  this.lineCtx.lineWidth = 2;
  this.lineCtx.stroke(); 

  // help me write this function
  this.generateCollisionMap(this.dotPositions, assets.load('items')[this.currentItemId]);
    this.dotPositions = []; 
},


generateCollisionMap: function(shapePoints, jsonData) {
  const intersectedTiles = {}; // Object to store tiles that intersect the shape

  // Ensure the correct item data is used
  const itemData = jsonData;
  if (!itemData) { 
    console.error("No item data found");
    return;
  }

  const tilesPerRow = Math.floor(this.tileset.naturalWidth / this.tileSize);

  // Loop through each tile in the item data
  itemData.forEach(tile => {
    const t = tile.t;
    const srcX = Math.floor(t % tilesPerRow) * this.tileSize;
    const srcY = Math.floor(t / tilesPerRow) * this.tileSize;
    let tileX = tile.a * this.tileSize * this.zoomLevel;
    let tileY = tile.b * this.tileSize * this.zoomLevel;

    // Adjust positions for zoom and rounding
    tileX = Math.round(tileX); 
    tileY = Math.round(tileY); 

    // Calculate tile bounding box
    const tileBoundingBox = {
      left: tileX,
      top: tileY,
      right: tileX + this.tileSize * this.zoomLevel,
      bottom: tileY + this.tileSize * this.zoomLevel
    };

    // Check if any shape point falls within the tile bounding box
    for (const shapePoint of shapePoints) {
      if (this.pointInRect(shapePoint, tileBoundingBox)) {
        intersectedTiles[t] = true; // Mark tile as intersected
        break; // No need to check other points for this tile
      }
    }
  });

  console.log("points:", shapePoints);
  console.log("Intersected Tiles:", intersectedTiles);
},

// Helper function to check if a point is inside a rectangle
pointInRect: function(point, rect) {
  return (point[0] >= rect.left && point[0] <= rect.right &&
          point[1] >= rect.top && point[1] <= rect.bottom);
}


}


game_editor_window.start();
    </script>

    <div class='resize-handle'></div>
  </div>
<?php
}
?>