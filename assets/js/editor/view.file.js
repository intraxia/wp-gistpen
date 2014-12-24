(function($){
	var viewfile = Backbone.View.extend({
		className: 'wpgp-ace',
		template: _.template($("script#wpgpFile").html()),
		events: {
			'click .switch-text': 'switchToText',
			'click .switch-ace': 'switchToAce',
			'click button': 'deleteFile',
			'change select': 'updateLanguage',
			'keyup input.wpgp-file-slug': 'updateSlug',
			'change textarea.wpgp-code': 'updateCode'
		},

		render: function() {
			var that = this;
			this.$el.html( this.template( this.model.toJSON() ) );
			this.$aceDiv = this.$el.find('.ace-editor');
			this.aceDiv = this.$aceDiv.get()[0];
			this.$textCode = this.$el.find('.wpgp-code');
			this.$wrapDiv = this.$el.find('.wp-editor-wrap');
			this.$langSelect = this.$el.find('.wpgp-file-lang');
			this.$slugInput = this.$el.find('.wpgp-file-slug');

			// Activate Ace editor
			this.aceEditor = ace.edit(this.aceDiv);

			this.aceEditor.getSession().on('change', function(event) {
				that.updateTextContent();
			});

			if("" === this.model.get('language')) {
				this.model
			}

			this.$langSelect.val(this.model.get('language'));
			this.updateLanguage();

			this.switchToAce();

			return this;
		},

		switchToAce: function() {
			this.updateAceContent();
			this.$textCode.hide();
			this.$aceDiv.show();
			this.$wrapDiv.addClass('ace-active').removeClass('html-active');
			this.aceEditor.focus();
		},

		updateAceContent: function() {
			this.aceEditor.getSession().setValue(this.$textCode.val());
		},

		switchToText: function() {
			this.$aceDiv.hide();
			this.$textCode.show();
			this.$wrapDiv.removeClass('ace-active').addClass('html-active');
		},

		updateTextContent: function() {
			this.$textCode.val(this.aceEditor.getValue());
			this.updateCode();
		},

		deleteFile: function(e) {
			e.preventDefault();

			this.$el.remove();
			this.model.deleteFile();
		},

		updateLanguage: function() {
			modelLang = this.$langSelect.val();

			// Nothin is set on init, so default to bash
			if ("" === modelLang || null === modelLang) {
				modelLang = 'bash';
			}

			this.model.set('language', modelLang);

			if('js' === modelLang) {
				this.aceEditor.getSession().setMode('ace/mode/javascript');
			} else if( 'bash' === modelLang) {
				this.aceEditor.getSession().setMode('ace/mode/sh');
			} else {
				this.aceEditor.getSession().setMode('ace/mode/' + modelLang);
			}
		},

		updateSlug: function() {
			this.model.set('slug', this.$slugInput.val());
		},

		updateCode: function() {
			this.model.set('code', this.$textCode.val());
		},

		updateTheme: function(theme) {
			this.aceEditor.setTheme('ace/theme/' + theme);
		},
	});

	window.wpgpEditor.Views.File = viewfile;
})(jQuery);
