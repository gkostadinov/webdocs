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

        if (data.delta) {
            data.delta.forEach(function(ops) {
                console.log(ops);
                quill.updateContents({'ops': ops});
            });
        }
    });

    var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],

        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],

        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

        [{ 'color': [] }, { 'background': [] }],
        [{ 'font': [] }],
        [{ 'align': [] }],

        ['clean']
    ];

    var quill = new Quill('#editor',  {
        modules: {
            toolbar: toolbarOptions
        },
        placeholder: 'Start writing something...',
        theme: 'snow'
    });
    quill.focus();

    quill.on('text-change', function(delta, oldDelta, source) {
        if (source == 'user') {
            changes.push(delta.ops);
        }
    });

    _.q = quill; // for debug

    sendChanges(ws);
});

function sendChanges(ws) {
    if (changes.length > 0) {
        ws.send(JSON.stringify({'delta': changes, 'document_id': DOC_ID}));
        changes = [];
    }
    setTimeout(function() { sendChanges(ws); }, 500);
}
