<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Options\Site;
use InvalidArgumentException;
use WP_REST_Request;
use WP_REST_Response;

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
		$params  = $request->get_params();
		$invalid = array();

		foreach ( $params as $key => $value ) {
			try {
				$this->site->set( $key, $value );
			} catch ( InvalidArgumentException $e ) {
				$invalid[] = $key;
			}
		}

		return new WP_REST_Response( $this->site->all(), 200, array( 'X-Invalid-Keys' => implode( ', ', $invalid ) ) );
	}
}
