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
    render: function () {
        var data = _.extend({}, this.model.zip.toJSON(), Gistpen_Settings);
        data.checked = data.sync === 'on';
        this.setElement($(this.template(data)));

        this.$('[data-wpgp-zip-status]').val(this.model.zip.get('status'));

        return this;
    },

    /**
     * Update the zip's sync state.
     */
    handleSyncChange: function (event) {
        this.model.zip.set('sync', event.target.checked ? 'on' : 'off');
    },

    /**
     * Update the zip's status.
     */
    handleStatusChange: function (event) {
        this.model.zip.set('status', event.target.value);
    }
});