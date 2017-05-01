<?php

namespace Intraxia\Gistpen\Http;

use WP_REST_Response;

class CommitController {

	/**
	 * CommitController constructor.
	 */
	public function __construct() {
	}

	public function index() {
		return new WP_REST_Response(array(

		));
	}
}
