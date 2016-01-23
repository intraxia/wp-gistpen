var Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    /**
     * Set the model's identifier attribute.
     */
    idAttribute: 'ID',

    /**
     * Model defaults.
     */
    defaults: {
        slug: "",
        code: "",
        ID: null,
        language: ""
    }
});
