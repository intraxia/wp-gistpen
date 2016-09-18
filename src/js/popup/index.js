var $ = require('jquery');
var Backbone = require('backbone').noConflict();

module.exports = Backbone.View.extend({
    events: {
        'click [data-wpgp-search-btn]': 'handleSearchClick'
    },
    /**
     * Compiled Handlebars template.
     */
    template: require('./template.hbs'),

    /**
     * Render the View to the DOM.
     */
    render: function() {
        var fragment = document.createDocumentFragment();

        fragment.appendChild($.parseHTML(this.template())[0]);

        // @todo add editor to popin

        this.$spinner = $(fragment.querySelector('.spinner'));

        this.$body = this.$('#wpgp-insert-dialog-body');

        this.$body.append(fragment);

        this.$input = this.$('[data-wpgp-search-input]');
        this.$results = this.$('[data-wpgp-search-results]');

        this.search();

        return this;
    },

    /**
     * Search for the provided search term and
     * append the results to the list.
     *
     * @param {string} term - Search term.
     */
    search: function(term) {
        $.ajax({
                method: 'GET',
                url: Gistpen_Settings.root + 'search',
                data: {
                    s: term || ''
                },
                context: this
            })
            .done(this.loadResults);
    },

    /**
     * Triggers a search when the search button is clicked.
     *
     * @param {jQuery.Event} e - Event object.
     */
    handleSearchClick: function(e) {
        e.preventDefault();

        this.$spinner.addClass('is-active');
        this.search(this.$input.val());
    },

    /**
     * Load the search results into the DOM.
     *
     * @param {Array} response - Array of Gistpens.
     */
    loadResults: function(response) {
        this.$results.empty();
        response.forEach(this.appendResult.bind(this));
    },

    /**
     * Add an individual search result to the results list.
     *
     * @param {Object} gistpen - Gistpen result.
     */
    appendResult: function(gistpen) {
        this.$results.append('<li><div class="gistpen-radio"><input type="radio" name="gistpen_id" value="' + gistpen.ID + '"></div><div class="gistpen-title">' + (gistpen.slug || gistpen.description || '') + '</div></li>');
    },

    /**
     * Retrieves the currently checked gistpen from the popup.
     *
     * @returns int
     */
    getID: function () {
        return parseInt(this.$('input[name="gistpen_id"]:checked').val(), 10);
    }
});
