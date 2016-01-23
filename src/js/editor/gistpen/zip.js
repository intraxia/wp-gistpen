var Backbone = require('backbone');
var _ = require('underscore');
var File = require('./file');

module.exports = Backbone.Model.extend({
    /**
     * Generate the url for the model by its ID.
     *
     * @returns {string}
     */
    urlRoot: Gistpen_Settings.root + 'zip',

    /**
     * Set the model's identifier attribute.
     */
    idAttribute: 'ID',

    /**
     * Sets the model's defaults.
     */
    defaults: {
        description: "",
        ID: null,
        status: "",
        password: "",
        sync: "off",
        files: new Backbone.Collection(null, {model: File})
    },

    /**
     * Transforms the object's attributes into a plain object.
     *
     * @returns {Object}
     */
    toJSON: function() {
        var data = Backbone.Model.prototype.toJSON.call(this);
        data.files = data.files.toJSON();

        return data;
    },

    /**
     * Translates the API response into model attributes.
     *
     * @param response
     * @returns {Object}
     */
    parse: function (response) {
        if ('Auto Draft' === response.description) {
            response.description = '';
        }

        if ('auto-draft' === response.status) {
            response.status = 'draft';
        }

        if ('on' !== response.sync) {
            response.sync = 'off';
        }

        if (response.files && response.files.length) {
            var collection = this.get('files');

            _.each(response.files, function(file) {
                /**
                 * Each file will come with an ID value, even null.
                 * The problem is when a file is created on the FE, it
                 * has no ID, but returns from the API with one. In order
                 * to find it in the collection, we need to search it
                 * without the ID.
                 */
                delete file.ID;

                var model = collection.findWhere(file);

                if (model) {
                    model.set(file);
                } else {
                    collection.add(file);
                }
            }, this);

            response.files = collection;
        }

        return response;
    }
});
