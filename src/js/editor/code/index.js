var Backbone = require('backbone');
var _ = require('underscore');
var $ = require('jquery');

module.exports = Backbone.View.extend({
    /**
     * Element class name for the view.
     */
    className: 'wpgp-ace',

    /**
     * Backbone view events.
     */
    events: {
        'click .switch-text': 'switchToText',
        'click .switch-ace': 'switchToAce',
        'click button': 'deleteFile',
        'change select': 'updateLanguage',
        'keyup input.wpgp-file-slug': 'updateSlug',
        'change textarea.wpgp-code': 'updateCode'
    },

    /**
     * Compiled mustache template
     */
    template: require('./template.hbs'),

    /**
     * Renders the View to the DOM.
     *
     * @returns {this}
     */
    render: function () {
        var data = _.extend({}, this.model.file.toJSON(), Gistpen_Settings);
        this.$el.append($(this.template(data)));

        this.$aceDiv = this.$el.find('.ace-editor');
        this.aceDiv = this.$aceDiv.get()[0];
        this.$textCode = this.$el.find('.wpgp-code');
        this.$wrapDiv = this.$el.find('.wp-editor-wrap');
        this.$langSelect = this.$el.find('.wpgp-file-lang');
        this.$slugInput = this.$el.find('.wpgp-file-slug');

        // Activate Ace editor
        this.aceEditor = ace.edit(this.aceDiv);

        this.aceEditor.getSession().on('change', this.updateTextContent.bind(this));

        if ("" === this.model.file.get('language')) {
            this.model.file.set('language', 'plaintext');
        }

        this.$langSelect.val(this.model.file.get('language'));
        this.aceEditor.setTheme('ace/theme/' + this.model.user.get('ace_theme'));
        this.updateLanguage();

        this.switchToAce();

        this.listenTo(this.model.user, 'change:ace_theme', this.updateTheme);
        this.listenTo(this.model.user, 'change:ace_invisibles', this.toggleInvisibles);
        this.listenTo(this.model.user, 'change:ace_tabs', this.toggleTabs);

        return this;
    },

    /**
     * Switch the editor to use the Ace editor.
     */
    switchToAce: function () {
        this.updateAceContent();
        this.$textCode.hide();
        this.$aceDiv.show();
        this.$wrapDiv.addClass('ace-active').removeClass('html-active');
        this.aceEditor.focus();
    },

    /**
     * Updates the Ace editor's content.
     */
    updateAceContent: function () {
        this.aceEditor.getSession().setValue(this.$textCode.val());
    },

    /**
     * Switch the editor the user the basic text editor.
     */
    switchToText: function () {
        this.$aceDiv.hide();
        this.$textCode.show();
        this.$wrapDiv.removeClass('ace-active').addClass('html-active');
    },

    /**
     * Updates the text editor's content.
     */
    updateTextContent: function () {
        this.$textCode.val(this.aceEditor.getValue());
        this.updateCode();
    },

    /**
     * Delete the file from the Gistpen.
     *
     * @param {jQuery.Event} e
     */
    deleteFile: function (e) {
        e.preventDefault();

        this.$el.remove();
        this.model.file.collection.remove(this.model.file);
    },

    /**
     * Update the file's language.
     */
    updateLanguage: function () {
        var modelLang = this.$langSelect.val();

        // Nothin is set on init, so default to plaintext
        if (!modelLang) {
            modelLang = 'plaintext';
        }

        this.model.file.set('language', modelLang);

        if ('js' === modelLang) {
            this.aceEditor.getSession().setMode('ace/mode/javascript');
        } else if ('bash' === modelLang) {
            this.aceEditor.getSession().setMode('ace/mode/sh');
        } else if ('c' === modelLang || 'cpp' === modelLang) {
            this.aceEditor.getSession().setMode('ace/mode/c_cpp');
        } else if ('coffeescript' === modelLang) {
            this.aceEditor.getSession().setMode('ace/mode/coffee');
        } else if ('php' === modelLang) {
            this.aceEditor.getSession().setMode(({path: "ace/mode/php", inline: true}));
        } else if ('plaintext' === modelLang || 'http' === modelLang) {
            this.aceEditor.getSession().setMode('ace/mode/plain_text');
        } else if ('py' === modelLang) {
            this.aceEditor.getSession().setMode('ace/mode/python');
        } else if ('go' === modelLang) {
            this.aceEditor.getSession().setMode('ace/mode/golang');
        } else if ('git' === modelLang) {
            this.aceEditor.getSession().setMode('ace/mode/diff');
        } else if ('nasm' === modelLang) {
            this.aceEditor.getSession().setMode('ace/mode/assembly_x86');
        } else {
            this.aceEditor.getSession().setMode('ace/mode/' + modelLang);
        }
    },

    /**
     * Update the file's slug.
     */
    updateSlug: function () {
        this.model.file.set('slug', this.$slugInput.val());
    },

    /**
     * Update the file's code.
     */
    updateCode: function () {
        this.model.file.set('code', this.$textCode.val());
    },

    /**
     * Update the Ace editor theme.
     *
     * @param user
     */
    updateTheme: function (user) {
        this.aceEditor.setTheme('ace/theme/' + user.get('ace_theme'));
    },

    /**
     * Update whether Ace editor displays invisibles (spaces/tabs/hard returns.
     *
     * @param user
     */
    toggleInvisibles: function (user) {
        this.aceEditor.setShowInvisibles('on' === user.get('ace_invisibles'));
    },

    /**
     * Update whether the Ace editor uses tabs.
     *
     * @param user
     */
    toggleTabs: function (user) {
        this.aceEditor.getSession()
            .setUseSoftTabs('on' !== user.get('ace_tabs'));
    }
});
