if (typeof hlDPan === 'undefined') hlDPan = {};
if (typeof hlDPan.button === 'undefined') hlDPan.button = {
    createMenu: false,
    register: function () {
        document.querySelector('[src$="/js/debugpanbutton"]').outerHTML = '';
        this.createButton();
    },
    createButton: function () {
        var btn = document.createElement('div');
        btn.id = 'hl-DEBUGPAN-button';
        btn.innerHTML = this.buttonContent();
        btn.classList.add('notranslate');
        var th = this;
        document.body.appendChild(btn);
        document.getElementById('hl-DEBUGPAN-button-close').onclick = function () {
            document.getElementById('hl-DEBUGPAN-button').outerHTML = '';
            var menu = document.getElementById('hl-DEBUGPAN-menu');
            if (menu) {
                menu.outerHTML = '';
            }
        };
        document.getElementById('hl-DEBUGPAN-button-menu-open').onclick = function () {
            th.openMenu();
        };
        if (hlDPan.script.scriptData.closed === 0) {
            th.openMenu();
        }
    },
    openMenu: function () {
        if (!this.createMenu) {
            hlDPan.script.loadJs(hlDPan.script.getPath() + '/js/debugpanterminal');
            hlDPan.script.loadJs(hlDPan.script.getPath() + '/js/debugpantemplate');
        }
        this.insertMenu();
    },
    buttonContent: function () {
        var code = hlDPan.script.scriptData.system.code;
        var status = code.status;
        var cl = 'x';
        switch(hlDPan.script.scriptData.system.code.type) {
            case 3: cl = '3x'; break
            case 4: cl = '4x'; break
            case 5: cl = '5x'; break
        }
        cl = code.status === 200 ? '0' : cl;

        return '<span id="hl-DEBUGPAN-button-menu-open" title="'
            + this.buttonTitle() +
            '">HL</span><div class="hl-bg-color-' + cl + '">' + code.status +
            '</div><span id="hl-DEBUGPAN-button-close" title="Close">X</span>';
    },
    buttonTitle: function () {
        var data = hlDPan.script.scriptData;
        return ' Phphleb/Debugpan ' + data.version + "\r\n" +
            'HTTP status code: ' + data.system.code.status + "\r\n" +
            'Speed: ' + data.system.time + ' sec.' + "\r\n" +
            'Memory: ' + data.system.memory + ' MB';
    },
    insertMenu: function () {
        if (this.createMenu) {
            var menu = document.getElementById('hl-DEBUGPAN-menu');
            if (menu) {
                menu.style.display = 'block';
            }
            return;
        }
        var th = this;
        var interval = setInterval(function(){
            if (typeof hlDPan.template !== 'undefined') {
                clearInterval(interval);
                var menu = hlDPan.template.createMenu();
                th.createMenu = true;
                menu.style.display = 'block';
            }
        }, 200)
    }
}
hlDPan.button.register();