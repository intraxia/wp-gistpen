var Backbone = require('backbone');
var _ = require('underscore');
var $ = require('jquery');

module.exports = Backbone.View.extend({
    /**
     * Backbone view events.
     */
    events: {
        'change [data-wpgp-theme]': 'handleThemeChange',
        'click [data-wpgp-add]': 'handleAddClick',
        'click [data-wpgp-update]': 'handleUpdateClick'
    },

    /**
     * Compiled mustache template.
     */
    template: require('./template.hbs'),

    /**
     * Renders the view to the DOM.
     */
    render: function() {
        var data = _.extend({}, this.model.user.toJSON(), Gistpen_Settings);
        this.setElement($(this.template(data)));

        this.$('[data-wpgp-theme]').val(this.model.user.get('ace_theme'));

        this.$spinner = this.$('[data-wpgp-spinner]');

        return this;
    },

    /**
     * Updates the user's theme when the input value changes.
     */
    handleThemeChange: function (event) {
        this.model.user.set('ace_theme', event.target.value);
    },

    /**
     * Emits an event to all listeners when the add button is clicked.
     */
    handleAddClick: function(e) {
        e.preventDefault();

        this.trigger('click:add', this);
    },

    /**
     * Emits an event to all listeners when the update button is clicked.
     */
    handleUpdateClick: function (e) {
        e.preventDefault();

        this.trigger('click:update', this);
    },

    /**
     * Adds a class to show the spinner gif.
     */
    enableSpinner: function () {
        this.$spinner.addClass('is-active');
    },

    /**
     * Removes a class to hide the spinner gif.
     */
    disableSpinner: function () {
        this.$spinner.removeClass('is-active');
    }
});