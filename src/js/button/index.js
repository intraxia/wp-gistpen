var Popup = require('../popup');
var $ = require('jquery');

module.exports = function (editor) {
    var popup;

    // Add the Insert Gistpen button
    editor.addButton('wp_gistpen', {
        icon: 'icons dashicons-editor-code',
        tooltip: 'Insert Gistpen',
        cmd: 'wp_gistpen'
    });

    editor.addCommand('wp_gistpen', command);

    /**
     * Trigger the popin window to add the gistpen shortcde.
     */
    function command() {
        var id = 'wpgp-insert-dialog';
        var $window = $(window);

        var e = editor.windowManager.open({
            // Modal settings
            title: 'Insert Gistpen',
            width: 400,
            // minus head and foot of dialog box
            height: (300 - 36 - 50),
            inline: 1,
            id: id,
            buttons: [
                {
                    text: 'Insert',
                    id: 'wp-gistpen-button-insert',
                    class: 'insert',
                    onclick: insert
                },
                {
                    text: 'Cancel',
                    id: 'wp-gistpen-button-cancel',
                    onclick: 'close'
                }
            ]
        });

        popup = new Popup({el: e.$el[0]});
        popup.render();
    }

    /**
     * Insert the shortcode and close the popin.
     */
    function insert() {
        editor.insertContent( '[gistpen id="' + popup.getID() + '"]' );
        editor.windowManager.close();
    }
};
