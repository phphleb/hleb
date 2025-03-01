if (typeof hlDPan === 'undefined') hlDPan = {};
if (typeof hlDPan.terminal === 'undefined') hlDPan.terminal = {
    isAjax: false,
    cursorInterval: null,
    register: function () {
        document.querySelector('[src$="/js/debugpanterminal"]').outerHTML = '';
    },
    getPanel: function() {
        return this.getButtons() + '<div id="hl-DEBUGPAN-menu-terminal"><div id="hl-DEBUGPAN-menu-terminal-cursor">&#9646;</div></div>';
    },
    getButtons: function() {
        return this.getButton('ping') +
            this.getButton('help') +
            this.getButton('version') +
            this.getButton('routes') +
            this.getButton('log-level');
    },
    getButton: function(name){
        return ' <span id="hl-DEBUGPAN-terminal-btn-' + name +'" class="hl-debugpan-terminal-button"><u>' + name + '</u></span>';
    },
    setActions: function() {
        var th = this;
        document.getElementById('hl-DEBUGPAN-terminal-btn-ping').onclick = function() {
            th.runCommand('ping');
        }
        document.getElementById('hl-DEBUGPAN-terminal-btn-help').onclick = function() {
            th.runCommand('help');
        }
        document.getElementById('hl-DEBUGPAN-terminal-btn-version').onclick = function() {
            th.runCommand('version');
        }
        document.getElementById('hl-DEBUGPAN-terminal-btn-routes').onclick = function() {
            th.runCommand('routes');
        }
        document.getElementById('hl-DEBUGPAN-terminal-btn-log-level').onclick = function() {
            th.runCommand('log-level');
        }
        this.initCursor();
    },
    runCommand: function(name) {
        if (hlDPan.template.sendAjax) {
            return;
        }
        var terminal = document.getElementById('hl-DEBUGPAN-menu-terminal');
        this.clearCursor();
        terminal.innerHTML += '<div><b>php console --' + name + '</b></div>';
        hlDPan.template.sendAjaxRequest('/app/terminal?query=' + name, [], 'GET', 'text/plain', 'terminalCommand');
    },
    afterRequest: function(data) {
        document.getElementById('hl-DEBUGPAN-menu-terminal').innerHTML += '<div><pre>' + data + '</pre></div>';
        this.addCursor();
    },
    clearCursor: function() {
        document.getElementById('hl-DEBUGPAN-menu-terminal-cursor').outerHTML = '';
        clearInterval(this.cursorInterval);
    },
    addCursor: function() {
        this.updateNodes();
        var element = document.getElementById('hl-DEBUGPAN-menu-terminal');
        element.innerHTML += '<div id="hl-DEBUGPAN-menu-terminal-cursor">&#9646;</div>';
        element.scrollTop = element.scrollHeight;
        this.initCursor();
    },
    initCursor: function() {
        this.cursorInterval = setInterval(function() {
            var cursor = document.getElementById('hl-DEBUGPAN-menu-terminal-cursor');
            if (!cursor) {
                return;
            }
            if (cursor.style.opacity == 1) {
                cursor.style.opacity = '0';
            } else {
                cursor.style.opacity = '1';
            }
        }, 700);
    },
    updateNodes: function() {
        var element = document.getElementById('hl-DEBUGPAN-menu-terminal');
        var nodes = element.querySelectorAll('div');
        if (nodes.length > 20) {
            var num = 0;
            for (var i in nodes) {
                if (num >= 5) {
                    break;
                }
                nodes[i].outerHTML = '';
                num++;
            }
        }
    },
}
hlDPan.terminal.register();
