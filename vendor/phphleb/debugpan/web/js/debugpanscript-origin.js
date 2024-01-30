if (typeof hlDPan === 'undefined') hlDPan = {};
if (typeof hlDPan.script === 'undefined') hlDPan.script = {
    scriptData: null,
    register: function () {
        const th = this;
        var intervalId = setInterval(function () {
            if (document.body !== null && typeof hlDPan === 'object' && typeof hlDPan.script === 'object') {
                clearInterval(intervalId);
                th.stateOnload();
            }
        }, 20);
    },
    getPath: function() {
        return '/' + this.scriptData.tag + '/debugpan/' + this.scriptData.version;
    },
    stateOnload: function () {
        var el = document.getElementById('hl-debugpan-init-script');
        this.scriptData = JSON.parse(el.getAttribute('data-list'));
        this.createInfo();
        this.deleteBlock('hl-debugpan-init-script');
        this.loadCss(this.getPath() + '/css/debugpanstyle');
        this.checkProbeAndLoadButton('hl-debugpan-init-probe');
    },
    loadCss: function (file) {
        var css = document.createElement('link');
        css.rel = "stylesheet";
        css.href = file;
        document.head.appendChild(css);
    },
    loadJs: function (file) {
        var script = document.createElement('script');
        script.src = file;
        script.async = true;
        script.type = 'text/javascript';
        document.body.appendChild(script);
    },
    createProbe: function (id) {
        var bl = document.createElement('span');
        bl.id = id;
        document.body.appendChild(bl);
    },
    deleteBlock: function (id) {
        document.getElementById(id).outerHTML = '';
    },
    checkProbeAndLoadButton: function (id) {
        this.createProbe(id);
        var th = this;
        var interval = setInterval(
            function () {
                var bl = document.getElementById(id);
                if (bl.offsetWidth > 10) {
                    th.deleteBlock(id);
                    th.loadButton();
                    clearInterval(interval);
                }
            }, 20
        );
    },
    loadButton: function () {
        this.loadJs(this.getPath() + '/js/debugpanbutton');
    },
    createInfo: function () {
        var data = hlDPan.script.scriptData;
        var mc = '#3CB371';
        switch(hlDPan.script.scriptData.system.code.type) {
            case 3: mc = '#C58904'; break
            case 4: mc = '#4169E1'; break
            case 5: mc = '#B22222'; break
        }
        var async = data.system.async ? ' ASYNC ' : '';
        console.log(
            '%c Phphleb/Debugpan ' + data.version + ' %c %c ' + data.system.code.status +
            ' %c %c Speed: ' + data.system.time + ' sec. Memory: ' + data.system.memory + ' MB ' + async,
            "color:#333; background: #CCC; padding: 2px",
            "color:#FFF; padding: 2px",
            "color:#FFF; background:" + mc + "; padding: 2px",
            "color:#FFF; padding: 2px",
            "color:#333; background: #F5F5F5; padding: 2px",
        );
    },
}
hlDPan.script.register();


