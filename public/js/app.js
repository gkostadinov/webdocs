function App(docId='1') {
    this.docId = docId;
    this.editorChanges = [];

    this.editor = this.loadEditor({
        modules: {
            toolbar: [
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
            ]
        },
        placeholder: 'Start writing something...',
        theme: 'snow'
    })

    this.ws = new Socket(docId, this.wsReceiveMessage.bind(this));
    this.initEditorChangeObserver()
}

App.prototype.loadEditor = function(editorOptions) {
    var quill = new Quill('#editor', editorOptions);
    quill.on('text-change', this.editorTextChanged.bind(this));
    quill.focus();

    return quill;
}

App.prototype.editorTextChanged = function(delta, oldDelta, source) {
    if (source == 'user') {
        this.editorChanges.push(delta.ops);
    }
}

App.prototype.wsReceiveMessage = function(data){
    if (data.delta) {
        var that = this;
        data.delta.forEach(function(ops) {
            that.editor.updateContents({'ops': ops});
        });
    }
}

App.prototype.initEditorChangeObserver = function() {
    var func = function() {
        if (this.editorChanges.length > 0) {
            this.ws.send({'delta': this.editorChanges, 'document_id': this.docId});
            this.editorChanges = [];
        }

        setTimeout(_.proxy(func, this), 500);
    }

    _.proxy(func, this)();
}

window.on('load', function() {
    _.app = new App();
});
