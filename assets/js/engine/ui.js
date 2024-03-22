window.modalResolves = window.modalResolves || {};

var ui = {
    notificationCount: 0,
    notif: function(message) {
        return new Promise(resolve => {
            let container = document.getElementById('notification');
            if(!container) {
                container = document.createElement('div');
                container.id = 'notification';
                container.className = 'fixed z-10 bottom-0 left-1/2 transform -translate-x-1/2';
                document.body.appendChild(container);
            }
    
            const notification = document.createElement('div');
            notification.className = 'notif text-white text-lg px-4 py-2 rounded shadow-md m-2';
            notification.innerText = message;
            container.prepend(notification);
    
            this.notificationCount++;
    
            setTimeout(() => {
                notification.classList.add('notification-exit');
    
                setTimeout(() => {
                    notification.remove();
                    this.notificationCount--;
    
                    if(this.notificationCount === 0) {
                        container.remove();
                    }
                    resolve();
                }, 1000);
            }, 3000);
        });
    },
    closestDataWindow: function(element) {
        while(element && !element.dataset.window) {
            element = element.parentElement;
        }
        return element ? element.dataset.window : null;
    },  
    modal: function(page, window_name) {
        // Check if the page parameter contains a slash, if not, append '/index.php'
        if (!page.includes('/')) {
            page += '/index.php';
        }
    
        // Dynamically set window_name based on the page parameter if not provided
        if (!window_name) {
            // Extract the first part of the page parameter before the slash
            const pageName = page.split('/')[0];
            window_name = `${pageName}_window`;
        }
    
        return new Promise((resolve, reject) => {
            let existingModal = document.querySelector("[data-window='" + window_name + "']");
            if(existingModal) {
                this.showModal(window_name);
                let draggableInstance = Draggable.modals.find(modal => modal.element === existingModal);
                if(draggableInstance) {
                    draggableInstance.bringToFront();
                } else {
                    console.error("No draggable instance found for existing modal");
                }
 
            } else {
                this.load({
                    url: 'modals/' + page,
                    method: 'GET',
                    success: (data) => {
                        this.html(document.body, data, 'append');
        
                        const draggable = new Draggable("[data-window='" + window_name + "']", {
                            start: function() {},
                            drag: function() {},
                            stop: function() {
                                this.classList.remove('dragging');
                            }
                        });
        
                        window.modalResolves[window_name] = resolve;
                    }
                });
            }
        });
    },   
    showModal(modalId) {
        var modal = document.querySelector("[data-window='" + modalId + "']");
        if(modal && modal.style.display === 'none') {
            modal.style.display = 'block';
        }
    },

    hideModal: function(modalId) {
        var modal = document.querySelector("[data-window='" + modalId + "']");
        if(modal) {
          modal.style.display = 'none';
        }
    },

    modalExists: function(modalId) {
        var modal = document.querySelector("[data-window='" + modalId + "']");
        return modal !== null;
    },
      
    closeModal: function(id) {
        var modalElement = document.querySelector("[data-window='" + id + "']");
        if(modalElement) {
            modalElement.remove();
            ui.unmount(id);
      
            if(window.modalResolves && window.modalResolves[id]) {
                console.log("resolving and removing", window.modalResolves[id]);
                window.modalResolves[id]();
                delete window.modalResolves[id];
            }
        }
    },
      
    showAllModals: function() {
        var modals = document.querySelectorAll("[data-window]");
        modals.forEach(function(modal) {
            modal.style.display = 'block';
        });
    },
    hideAllModals: function() {
        var modals = document.querySelectorAll("[data-window]");
        modals.forEach(function(modal) {
            modal.style.display = 'none';
        });
    },
    closeAllModals: function() {
        var windows = document.querySelectorAll('[data-window]');
        windows.forEach(function(windowElement) {
            var id = windowElement.getAttribute('data-window');
            windowElement.remove();
            ui.unmount(id);
        });
    },
    html: function(selectorOrElement, htmlString, action = 'replace') {
        const element = (typeof selectorOrElement === 'string') ? document.querySelector(selectorOrElement) : selectorOrElement;
      
        if(!element) { return; }
      
        switch(action) {
            case 'append':
                element.insertAdjacentHTML('beforeend', htmlString);
                break;
            case 'prepend':
                element.insertAdjacentHTML('afterbegin', htmlString);
                break;
            case 'html':
            default:
                element.innerHTML = htmlString;
                break;
        }
      
        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = htmlString;
        Array.from(tempContainer.querySelectorAll("script")).forEach(oldScript => {
            const newScript = document.createElement("script");
            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
            newScript.textContent = oldScript.textContent;
            document.body.appendChild(newScript);
        });
    },
    unmount: function(id) {
        if(window[id] && typeof window[id].unmount === 'function') {
          window[id].unmount();
        }
        
        var obj = window[id];
      
        for (var prop in obj) {
          if (typeof obj[prop] === "function") {
            delete obj[prop];
          }
        }
    },
    load: async function({ url, method = 'GET', data = null, outputType = 'text', success, error }) {
        try {
      
          const queryParams = new URLSearchParams(data).toString();
          const fetchUrl = method === 'GET' && data ? `${url}?${queryParams}` : url;
      
          const init = {
            method: method,
            headers: {}
          };
      
          if (data && method !== 'GET') {
            init.headers['Content-Type'] = 'application/x-www-form-urlencoded';
            init.body = data;
          }
      
          const response = await fetch(fetchUrl, init);
      
          if (!response.ok) { throw new Error('Network response was not ok: ' + response.statusText); }
      
          let responseData;
          switch (outputType) {
            case 'json':
              responseData = await response.json();
              break;
            case 'blob':
              responseData = await response.blob();
              break;
            case 'formData':
              responseData = await response.formData();
              break;
            case 'arrayBuffer':
              responseData = await response.arrayBuffer();
              break;
            default:
              responseData = await response.text();
          }
      
          if (success) success(responseData);
      
        } catch (err) {
          if (error) error(err);
        }
      }
}