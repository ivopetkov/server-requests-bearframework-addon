/*
 * Server requests addon for Bear Framework
 * https://github.com/ivopetkov/server-requests-bearframework-addon
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

var ivoPetkov = ivoPetkov || {};
ivoPetkov.bearFrameworkAddons = ivoPetkov.bearFrameworkAddons || {};
ivoPetkov.bearFrameworkAddons.serverRequests = ivoPetkov.bearFrameworkAddons.serverRequests || (function () {

    var url = null;

    var sendRequest = function (name, data, onSuccess, onFail) {
        if (url === null) {
            throw 'ivoPetkov.bearFrameworkAddons.serverRequests not initialized';
        }
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function ()
        {
            if (xmlhttp.readyState === 4)
            {
                if (xmlhttp.status === 200) {
                    try {
                        var response = JSON.parse(xmlhttp.responseText);
                    } catch (e) {
                        var response = {};
                    }
                    if (typeof response.status !== 'undefined' && response.status === '1') {
                        if (typeof onSuccess !== 'undefined') {
                            onSuccess(response.text);
                        }
                        return;
                    }
                }
                if (typeof onFail !== 'undefined') {
                    onFail();
                }
            }
        };
        var params = [];
        for (var key in data) {
            params.push(key + '=' + encodeURIComponent(data[key]));
        }
        params = params.join('&');
        xmlhttp.open('POST', url + '?n=' + name, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(params);
    };

    var send = function (url, data) {
        if (typeof data === 'undefined') {
            data = {};
        }
        Promise = window.Promise || function (callback) {
            var thenCallbacks = [];
            var catchCallback = null;
            this.then = function (f) {
                thenCallbacks.push(f);
                return this;
            };
            this.catch = function (f) {
                if (catchCallback === null) {
                    catchCallback = f;
                }
                return this;
            };
            var resolve = function () {
                for (var i in thenCallbacks) {
                    thenCallbacks[i].apply(null, arguments);
                }
            };
            var reject = function () {
                if (catchCallback !== null) {
                    catchCallback.apply(null, arguments);
                }
            };
            window.setTimeout(function () {
                callback(resolve, reject);
            }, 16);
        };
        return new Promise(function (resolve, reject) {
            sendRequest(url, data, resolve, reject);
        });
    };

    var initialize = function (data) {
        if (typeof data[0] !== 'undefined') {
            url = data[0];
        }
    };

    return {
        'initialize': initialize,
        'send': send
    };

}());