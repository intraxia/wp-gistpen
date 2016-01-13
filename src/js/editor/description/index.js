var Backbone = require('backbone');
var $ = require('jquery');

module.exports = Backbone.View.extend({
    /**
     * Backbone view events.
     */
    events: {
        'click [data-wpgp-description-label]': 'handleLabelClick',
        'blur [data-wpgp-description-input]': 'handleInputBlur',
        'focus [data-wpgp-description-input]': 'handleInputFocus',
        'keydown [data-wpgp-description-input]': 'handleInputKeydown',
        'change [data-wpgp-description-input]': 'handleDescriptionChange'
    },

    /**
     * Compiled mustache template.
     */
    template: require('./template.hbs'),

    /**
     * Render the description view to the DOM.
     */
    render: function () {
        this.setElement($(this.template(this.model.zip.toJSON())));

        this.$label = this.$('[data-wpgp-description-label]');
        this.$input = this.$('[data-wpgp-description-input]');

        if (this.model.zip.get('description')) {
            this.$label.addClass('screen-reader-text');
        }

        return this;
    },

    /**
     * Place the description input in focus when the label is clicked.
     */
    handleLabelClick: function () {
        this.$label.addClass('screen-reader-text');
        this.$input.focus();
    },

    /**
     * Display the placeholder when the input is blurred.
     */
    handleInputBlur: function () {
        if ('' === this.$input.val()) {
            this.$label.removeClass('screen-reader-text');
        }
    },

    /**
     * Hide the placeholder when the input is focused.
     */
    handleInputFocus: function () {
        this.$label.addClass('screen-reader-text');
    },

    /**
     * Hide the placeholder when the input is keyed down.
     */
    handleInputKeydown: function () {
        this.$label.addClass('screen-reader-text');
    },

    /**
     * Updates the zip description when the input value changes.
     */
    handleDescriptionChange: function () {
        this.model.zip.set('description', this.$input.val());
    }
});
