var $ = require('jquery');
var Zip = require('./editor/gistpen/zip');
var User = require('./editor/user');
var Controller = require('./editor/controller');

$.ajaxSetup({
    beforeSend: function(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', Gistpen_Settings.nonce);
    }
});

$(document).ready(function ($) {
    var zip = new Zip({ID: parseInt($('#post_ID').val(), 10)});
    var user = new User();
    var $post = $('#post');

    $.when(zip.fetch(), user.fetch()).done(start);

    /**
     * Start up the application.
     */
    function start() {
        var app = new Controller({
            model: {
                zip: zip,
                user: user
            }
        });

        $post.html(app.render().el);
    }
});
