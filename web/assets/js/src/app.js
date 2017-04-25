var sess;
var defaultChannels = ['channel:btcticker'];
var wampServer = "ws://btc-ticker.dev:8085";

$(function() {
    /** Connect to WAMP server */
    ab.connect(
        wampServer,
        function (session) {
            sess = session;
            on_connect()
        },
        function (code, reason) {
            notify(reason, 'error');
        }
    );

    on_connect = function() {
        $.each(defaultChannels, function (i, el) {
            subscribe_to(el);
        });
    };

    subscribe_to = function (chan) {
        sess.subscribe(chan, function (channel, event) {
            notify(event);
        });
        notify("Connected. Waiting for rates...", 'success');
        return true;
    };

    publish = function(channel, message) {
        sess.publish(channel, message);
    };

    notify = function (message) {
        var notificationElement = $('#notify');
        notificationElement.text(message);
    };
});