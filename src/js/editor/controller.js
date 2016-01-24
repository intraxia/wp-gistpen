var Backbone = require('backbone');
var DescriptionView = require('./description');
var SettingsView = require('./settings');
var PropsView = require('./props');
var CodeView = require('./code');
var File = require('./gistpen/file');
var $ = require('jquery');

module.exports = Backbone.View.extend({
    /**
     * Element #id
     */
    id: 'wpgp-editor',

    /**
     * Render the view to the DOM.
     *
     * @returns {this}
     */
    render: function () {
        var views = [];

        var description = new DescriptionView({model: this.model});
        var $row = description.render().$('[data-wpgp-row]');

        var settings = new SettingsView({model: this.model});
        this.listenTo(settings, 'click:add', this.handleSettingsClickAdd);
        this.listenTo(settings, 'click:update', this.handleSettingsClickUpdate);

        $row.append(settings.render().el);

        var props = new PropsView({model: this.model});
        $row.append(props.render().el);

        var fragment = document.createDocumentFragment();

        fragment.appendChild(description.el);

        this.model.zip.get('files').each(function (file) {
            views.push(new CodeView({
                model: {
                    file: file,
                    user: this.model.user
                }
            }));
        }, this);

        _.each(views, function (view) {
            fragment.appendChild(view.render().el);
        });

        this.$el.html(fragment);

        return this;
    },

    /**
     * Handles adding a new file to the Gistpen.
     */
    handleSettingsClickAdd: function () {
        var file = new File({});
        var view = new CodeView({model: {
            file: file,
            user: this.model.user
        }});
        this.model.zip.get('files').add(file);

        this.$el.append(view.render().el);

        $('html, body').animate({scrollTop: view.$el.offset().top});
    },

    /**
     * Handles updating the Gistpen to the server.
     *
     * @param view
     */
    handleSettingsClickUpdate: function (view) {
        view.enableSpinner();

        this.model.zip.save()
            .done(this.displaySuccessMessage.bind(this))
            .done(view.disableSpinner.bind(view))
            .fail(this.displayErrorMessage.bind(this));
    },

    /**
     * Displays a message when an update succeeds.
     */
    displaySuccessMessage: function () {
        var $message = $('<div class="updated"><p>Gistpen ID '+ this.model.zip.id + ' successfully updated.</p></div>');

        this.displayMessage($message);
    },

    /**
     * Displays an error message when an update fails.
     */
    displayErrorMessage: function (xhr) {
        var $message = $('<div class="error"><p>Gistpen ID '+ this.model.zip.id + ' failed to update with message: ' + xhr.statusText + '.</p></div>');

        this.displayMessage($message);
    },

    /**
     * Briefly display then hide the provided error message.
     *
     * @param {jQuery} $message
     */
    displayMessage: function ($message) {
        $message.hide()
            .prependTo(this.$el)
            .slideDown('slow')
            .delay('2000')
            .slideUp('slow');
    }
});
