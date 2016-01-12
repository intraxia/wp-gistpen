var Backbone = require('backbone');
var $ = require('jquery');

module.exports = Backbone.View.extend({
    /**
     * Backbone view events.
     */
    events: {
        'change [data-wpgp-description]': 'handleDescriptionChange'
    },

    /**
     * Compiled mustache template.
     */
    template: require('./template.hbs'),

    /**
     * Render the description view to the DOM.
     */
    render: function () {
        var that = this;

        this.setElement($(this.template(this.model.zip.toJSON())));

        this.$label = this.$('#title-prompt-text');
        this.$input = this.$('#title');

        if (this.model.zip.get('description')) {
            this.$label.addClass('screen-reader-text');
        }

        this.$label.click(function(){
            that.$label.addClass('screen-reader-text');
            that.$input.focus();
        });

        // @todo all these bindings should be done on the events hash
        // then we can remove the view properties and switch to
        // event.target instead.
        this.$input.blur(function(){
            if ( '' === this.value ) {
                that.$label.removeClass('screen-reader-text');
            }
        }).focus(function(){
            that.$label.addClass('screen-reader-text');
        }).keydown(function(e){
            that.$label.addClass('screen-reader-text');
        });

        return this;
    },

    /**
     * Updates the zip description when the input value changes.
     */
    handleDescriptionChange: function () {
        this.model.zip.set('description', this.$input.val());
    }
});
