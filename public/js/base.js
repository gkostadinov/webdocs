NodeList.prototype.forEach = []['forEach']

Node.prototype.on = function(eventName, func) {
    this.addEventListener(eventName, func, false);

    return this;
}

NodeList.prototype.on = function(eventName, func) {
    this.forEach(function(el) {
        el.on(eventName, func);
    });

    return this;
}

Node.prototype.trigger = function(eventName, data) {
    var event = new CustomEvent(eventName, {detail: data} || {});
    this.dispatchEvent(event);

    return this;
}

NodeList.prototype.trigger = function(eventName, data) {
    this.forEach(function(el) {
        el.trigger(eventName, data);
    });

    return this;
}

function _(selector) {
    var el = document.querySelectorAll(selector);

    return el.length == 1 ? el[0] : el;
}

_._ = document.createElement('_');
_.on = Node.prototype.on.bind(_._);
_.trigger = Node.prototype.trigger.bind(_._);

_.ajax = function(type, url, callback, options={}) {
    var xhr = new XMLHttpRequest();

    xhr.onload = function () {
        callback.call(xhr, null, JSON.parse(xhr.response));
    };

    xhr.onerror = function () {
        callback.call(xhr, true);
    };

    if (type === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/json');
    }

    xhr.open(type, url);
    xhr.send(options ? JSON.stringify(options) : null);
}
_.get = _.ajax.bind(this, 'GET')
_.post = _.ajax.bind(this, 'POST')
