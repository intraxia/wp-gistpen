var Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    /**
     * Override the Backbone `url` method with a single endpoint URL.
     */
    url: Gistpen_Settings.root + 'me',

    /**
     * Backbone model defaults.
     */
    defaults: {
        ace_theme: ''
    }
});
