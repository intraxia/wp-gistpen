module.exports = (function($) {
	var templates = {};
	var startBtn;
	var wrap;
	var gistpenIDs;
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
		queryDOM();
		fetchTemplates();
		setClickHandlers();
	}

	function queryDOM() {
		startBtn = $('#export-gistpens');
		wrap = $('.wpgp-wrap');
	}

	function fetchTemplates() {
		templates.header = $("script#exportHeaderTemplate");
		templates.status = $("script#statusTemplate");
	}

	function setClickHandlers() {
		if(!startBtn.length) {
			return;
		}

		startBtn.prop("disabled", true);

		getGistpenIDs();

		startBtn.click(function(e) {
			e.preventDefault();

			wrap.html('');

			appendHeader();

			gistpenIDs.forEach(function(id) {
				exportID(id);
			});
		});
	}

	function getGistpenIDs() {
		$.post(window.ajaxurl, {
			action: 'get_gistpens_missing_gist_id',
			nonce: $('#_ajax_wp_gistpen').val()
		}, function(response) {
			if(false === response.success) {
				startBtn.val(response.data.message);
			} else {
				gistpenIDs = response.data.ids;
				startBtn.prop("disabled", false);
			}
		});
	}

	function appendHeader() {
		var template = _.template(templates.header.html());

		header = $(template({}).trim()).appendTo(wrap);
		backLink = header.find("a");
		$progress = header.find("#progressbar");
		$progressLabel = header.find(".progress-label");
		$status = $('#export-status');

		result = $progress.progressbar({
			max: gistpenIDs.length,
			value: 0,
			enable: true
		});
	}

	function exportID(id) {
		$.ajaxq('export', {
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'create_gist_from_gistpen_id',
				nonce: $('#_ajax_wp_gistpen').val(),

				gistpen_id: id
			},
			success: function(response) {
				updateProgress(response);
			},
			error: function(response) {
				$.ajaxq.abort('export');
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
