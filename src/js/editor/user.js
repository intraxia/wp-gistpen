var Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    /**
     * Override the Backbone `url` method with a single endpoint URL.
     */
    url: Gistpen_Settings.root + 'me',

    /**
     * All updates should be PATCH requests to `/me`.
     * This ensures the model is never "new" and does a POST.
     *
     * @todo overwrite Backbone.sync instead to always PATCH
     *
     * @returns {boolean}
     */
    isNew: function () {
        return false;
    }
});
