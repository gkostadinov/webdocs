var DOC_ID = '1';
var changes = [];

function Socket(onMessage) {
    this.ws = new WebSocket(WEBSOCKET_IP);
    this.ws.onmessage = onMessage;

    this.send(JSON.stringify({'document_id': DOC_ID}))
}

Socket.prototype.send = function(message, callback) {
    var that = this;
    this.waitForConnection(function () {
        that.ws.send(message);
        if (typeof callback !== 'undefined') {
            callback();
        }
    }, 1000);
};

Socket.prototype.waitForConnection = function(callback, interval) {
    if (this.ws.readyState === 1) {
        callback();
    } else {
        var that = this;
        setTimeout(function() {
            that.waitForConnection(callback, interval);
        }, interval);
    }
};

window.on('load', function() {
    ws = new Socket(function(msg){
        var data = JSON.parse(msg.data);
        console.log(data);
        if (data.delta) {
            var retain = 0;
            console.log(JSON.stringify(data.delta));
            data.delta.forEach(function(op) {
                console.log(op);

                quill.updateContents({ops: [op]});
            });
        }
    });

    var quill = new Quill('#editor');
    quill.focus();

    _.q = quill; // for debug

    quill.on('text-change', function(delta, oldDelta, source) {
        if (source == 'user') {
            delta.ops.forEach(function(op) {
                changes.push(op);
            });
        }
    });

    sendChanges(ws);
});

function sendChanges(ws) {
    if (changes.length > 0) {
        ws.send(JSON.stringify({'delta': changes, 'document_id': DOC_ID}));
        changes = [];
    }
    setTimeout(function() { sendChanges(ws); }, 1000);
}
