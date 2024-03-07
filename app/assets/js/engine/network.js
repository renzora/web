var network = {
  ws_uri: "wss://localhost:3000", // Main server URI
  socket: null, // Main server WebSocket
  gameSocket: null, // Game server WebSocket

  init: function() {
      this.socket = new WebSocket(this.ws_uri);

      this.socket.onopen = (e) => {
          this.open(e);
      };

      this.socket.onmessage = (e) => {
          this.message(e);
      };

      window.onbeforeunload = (e) => {
          this.beforeUnload(e);
      };

      this.socket.onclose = (e) => {
          this.close(e);
      };
  },

  open: function(e) {
      if(!this.getToken('renaccount')) {
          ui.modal('auth/index.php', 'auth_window');
      }

      ui.loadGui();
      game.init();
      console.log("Connected to Main renzora server");
  },

  message: function(e) {
      var json = JSON.parse(e.data);
      document.dispatchEvent(new CustomEvent(json.command, { detail: json }));
  },

  beforeUnload: function(event) {
      if (this.socket) {
          this.socket.close();
      }
      if (this.gameSocket) {
          this.gameSocket.close();
      }
  },

  close: function(e) {
      ui.removeGui();
      ui.modal("servers/errors/mainServer.php", "error_window");
  },

  send: function(message) {
      if (this.socket && this.socket.readyState === WebSocket.OPEN) {
          this.socket.send(JSON.stringify(message));
          console.log(JSON.stringify(message));
      } else {
          console.error("WebSocket is not open. Message not sent.");
      }
  },

  getToken: function(name) {
      var value = "; " + document.cookie;
      var parts = value.split("; " + name + "=");
      if (parts.length == 2) return parts.pop().split(";").shift();
  },

  connectToGameServer: function(ip, port) {
    this.isConnectingToNewServer = true;

    if (this.gameSocket) {
        this.gameSocket.close();
    }

    this.hideServerStatus();
    ui.hideModal('serverjoin_window');
    ui.hideModal('servers_window');
    ui.modal('servers/ajax/connectionAttempt.php', 'serverConnectionAttempt_window');

    this.gameSocket = new WebSocket(`wss://${ip}:${port}`);

    var handleConnectionFailure = () => {
        ui.closeModal('serverConnectionAttempt_window');
        this.hideServerStatus();
        this.isConnectingToNewServer = false;
    };

    var connectionAttemptTimeout = setTimeout(() => {
        if (this.gameSocket && this.gameSocket.readyState !== WebSocket.OPEN) {
            this.gameSocket.close();
            handleConnectionFailure();
        }
    }, 5000); // 5 seconds

    this.gameSocket.onopen = () => {
        clearTimeout(connectionAttemptTimeout);
        ui.closeModal('serverConnectionAttempt_window');
        this.showServerStatus();
        ui.notif("Successfully connected to server");
        this.isConnectingToNewServer = false;
    };

    this.gameSocket.onmessage = (event) => {
        var json = JSON.parse(event.data);
    };

    this.gameSocket.onerror = (event) => {
        console.error("Game server connection error:", event);
        this.gameSocket.close();
        handleConnectionFailure();
    };

    this.gameSocket.onclose = (event) => {
        console.log("Disconnected from the game server.");
        if(!this.isConnectingToNewServer) {
            ui.closeModal('serverConnectionAttempt_window');
            this.hideServerStatus();
            if(!this.intentionalDisconnect) {
                ui.modal('servers/errors/serverTimeout.php', 'servertimeout_window');
            }
        }
        this.intentionalDisconnect = false;
        this.isConnectingToNewServer = false;
    };
},
leaveServer: function() {
    this.intentionalDisconnect = true;
    if (this.gameSocket) {
        this.gameSocket.close();
        ui.showModal('servers_window');
        this.gameSocket = null;
        this.hideServerStatus();
    }
},
  hideServerStatus: function() {
    const serverStatus = document.getElementById('serverStatus');
    if (!serverStatus.classList.contains('hidden')) {
        serverStatus.classList.add('hidden');
    }
},
showServerStatus: function() {
    const serverStatus = document.getElementById('serverStatus');
    if (serverStatus.classList.contains('hidden')) {
        serverStatus.classList.remove('hidden');
    }
}
};