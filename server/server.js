const fs = require('fs');
const https = require('https');
const WebSocket = require('ws');

// Prepare the server options with your SSL certificate paths
const serverOptions = {
  cert: fs.readFileSync('./certs/cert.pem'),
  key: fs.readFileSync('./certs/key.pem')
};

// Create an HTTPS server with the SSL options
const server = https.createServer(serverOptions);

// Initialize the WebSocket server instance tied to the HTTPS server
const wss = new WebSocket.Server({ server });

// Object to track registered servers
const servers = {};

console.log("WebSocket Server running on port 3000. Accepting connections...");

wss.on("connection", ws => {
    console.log("Client connected");

    ws.on("message", message => {
        try {
            const data = JSON.parse(message);
            console.log("Received message:", data);

            switch (data.type) {
                case "registerServer":
                    // Handle game server registration
                    registerServer(data, ws);
                    break;

                case "joinGame":
                    // Handle client request to join a game
                    sendServerInfo(ws, data.serverId);
                    break;
                default:
                    console.log(`Unknown message type: ${data.type}`);
            }
        } catch (e) {
            console.error("Error handling message:", e.message);
        }
    });

    ws.on('close', () => {
        console.log('Client disconnected');
        // Optionally, clean up any associated information with this connection
    });
});

function registerServer(data, ws) {
    const serverId = data.serverId || "default";
    servers[serverId] = { ...data, ws };
    console.log(`Server registered: ${serverId}`);
}

function sendServerInfo(ws, serverId) {
    if (servers[serverId]) {
        const serverInfo = servers[serverId];
        ws.send(JSON.stringify({ type: 'serverInfo', address: serverInfo.address }));
        console.log(`Server info sent for: ${serverId}`);
    } else {
        ws.send(JSON.stringify({ type: 'error', message: 'Server not found' }));
        console.log(`Server not found for: ${serverId}`);
    }
}

// Listen on the specified port
server.listen(3000, () => {
  console.log('Server is listening on port 3000');
});