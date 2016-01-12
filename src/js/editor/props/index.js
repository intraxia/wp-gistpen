var Backbone = require('backbone');
var _ = require('underscore');
var $ = require('jquery');

module.exports = Backbone.View.extend({
    /**
     * Backbone view events.
     */
    events: {
        'change [data-wpgp-sync]': 'handleSyncChange',
        'change [data-wpgp-zip-status]': 'handleStatusChange'
    },

    /**
     * Compiled mustache template.
     */
    template: require('./template.hbs'),

    /**
     * Renders the view to the DOM.
     */
    render: function() {
        var data = _.extend({}, this.model.zip.toJSON(), Gistpen_Settings);
        this.setElement($(this.template(data)));

        return this;
    },

    /**
     * Update the zip's sync state.
     */
    handleSyncChange: function() {
        // @todo
    },

    /**
     * Update the zip's status.
     */
    handleStatusChange: function() {
        console.log('implement handleStatusChange');
    }
});