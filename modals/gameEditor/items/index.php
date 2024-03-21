<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($auth) {
?>
  <div data-window='game_editor_items_window' class='window window_bg' style='width: 800px; background: #232f33;'>

<div data-part='handle' class='window_title' style='background-image: radial-gradient(#455357 1px, transparent 0) !important;'>
  <div class='float-right'>
    <button class="icon close_dark mr-1 hint--left" aria-label="Close (ESC)" data-close></button>
  </div>
  <div data-part='title' class='title_bg window_border' style='background: #232f33; color: #ede8d6;'>Game Items</div>
</div>
<div class='clearfix'></div>
<div class='relative'>

<button class="green_button p-3 text-xs rounded shadow float-right mt-2 mr-2" onclick="ui.modal('gameEditor','game_editor_window');">Import Items</button>

<button class="green_button p-3 text-xs rounded shadow float-right mt-2 mr-2" onclick="ui.modal('gameEditor','game_editor_window');">Categories</button>

  <div class='container text-light window_body p-4'>
  <div id="dataDisplay"></div>

  <div class="mt-4"></div>
  <div class="canvas-container" style="position: relative; width: 400px; height: 500px;">
    <canvas id="gameCanvas" width="400px" height="500px"></canvas>
    <canvas id="lineCanvas" width="400px" height="500px" style="position: absolute; left: 0; top: 0;"></canvas>
  </div>


  </div>
</div>

    <script>
var game_editor_items_window = {
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
    selectBox.classList.add('form-control');

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
      game_editor_items_window.currentItemId = this.value; // Store the selected item ID
      game_editor_items_window.displayItemsForCategory(jsonData, this.value);
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
    console.log(shapePoints);
},

isPointInsidePolygon: function(point, polygon) {
  let [x, y] = point;
  let inside = false;

  for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
    let [xi, yi] = polygon[i];
    let [xj, yj] = polygon[j];

    // Check if the point is inside the polygon
    const intersect = ((yi > y) != (yj > y))
      && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);

    if (intersect) inside = !inside;
  }

  return inside;
}


}


game_editor_items_window.start();
    </script>

    <div class='resize-handle'></div>
  </div>
<?php
}
?>