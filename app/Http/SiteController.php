<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Options\Site;
use InvalidArgumentException;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Controller for managing the site options.
 */
class SiteController {
	/**
	 * User options.
	 *
	 * @var Site
	 */
	protected $site;

	/**
	 * SiteController constructor.
	 *
	 * @param Site $site Site options manager.
	 */
	public function __construct( Site $site ) {
		$this->site = $site;
	}

	/**
	 * View all of the site options.
	 *
	 * @return WP_REST_Response
	 */
	public function view() {
		return new WP_REST_Response( $this->site->all() );
	}

	/**
	 * Update the site's options.
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_REST_Response
	 */
	public function update( WP_REST_Request $request ) {
		$this->site->patch( $request->get_params() );
		return new WP_REST_Response( $this->site->all(), 200 );
	}
}
