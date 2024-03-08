var debug = {
    sprite: function() {

    },
    camera: function() {

    },
    fps: function() {
        var debugFPS = document.getElementById('gameFps');
        if(debugFPS) { debugFPS.innerHTML = "FPS: " + scene.fps.toFixed(3); }
    }
}