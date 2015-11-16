module.exports =(function($) {
	var startBtn;
	var wrap;
	var templates = {};
	var gistIDs;
	var header;
	var backLink;
	var $progress;
	var $progressLabel;
	var $status;
	var result;

	return {
		init: init
	};

	function init() {
		startBtn = jQuery('#import-gists');
		wrap = jQuery('.wpgp-wrap');

		templates.header = jQuery("script#importHeaderTemplate");
		templates.status = jQuery("script#statusTemplate");

		setClickHandlers();
	}

	function setClickHandlers() {
		if(!startBtn.length) {
			return;
		}

		startBtn.prop("disabled", true);

		getGistIDs();

		startBtn.click(function(e) {
			e.preventDefault();

			wrap.html('');

			appendHeader();

			gistIDs.forEach(function(id) {
				importID(id);
			});
		});
	}

	function getGistIDs() {
		$.post(ajaxurl, {
			action: 'get_new_user_gists',
			nonce: $('#_ajax_wp_gistpen').val()
		}, function(response) {
			if(false === response.success) {
				startBtn.val(response.data.message);
			} else {
				gistIDs = response.data.gist_ids;
				startBtn.prop("disabled", false);
			}
		});
	}

	function appendHeader() {
		var template = _.template(templates.header.html());

		header = jQuery(template({}).trim()).appendTo(wrap);
		backLink = header.find("a");
		$progress = header.find("#progressbar");
		$progressLabel = header.find(".progress-label");
		$status = jQuery('#import-status');

		result = $progress.progressbar({
			max: gistIDs.length,
			value: 0,
			enable: true
		});
	}

	function importID(id) {
		$.ajaxq('import', {
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'import_gist',
				nonce: jQuery('#_ajax_wp_gistpen').val(),

				gist_id: id
			},
			success: function(response) {
				updateProgress(response);
			},
			error: function(response) {
				$.ajaxq.abort('import');
				updateProgress(response);
			}
		});
	}

	function updateProgress(response) {
		var template = _.template(templates.status.html());

		$progress.progressbar( 'value', $progress.progressbar("value") + 1);

		$status.append(template({
			status_code: response.data.code,
			status_message: response.data.message
		}).trim());
	}
})(window.jQuery);
