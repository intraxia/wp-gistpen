<?php
namespace Intraxia\Gistpen\Http\Filter;

use Intraxia\Jaxion\Contract\Http\Filter as FilterContract;
use WP_Error;

/**
 * Class Filter\RepoCollection
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filters
 */
class RepoCollection implements FilterContract {
	/**
	 * Generates argument rules.
	 *
	 * Returns an array matching the WP-API format for argument rules,
	 * including sanitization, validation, required, or defaults.
	 *
	 * @return array
	 */
	public function rules() {
		return array(
			'page' => array(
				'required' => false,
				'default' => 1,
				'sanitize_callback' => array( $this, 'sanitize_page' ),
			),
		);
	}

	/**
	 * Ensure the blobs passed to the request are valid.
	 *
	 * @param  array $blobs   Blobs parameter.
	 * @return WP_Errpr|array Sanitized blobs.
	 */
	public function sanitize_page( $page ) {
		if ( is_numeric( $page ) ) {
			return (int) $page;
		}

		return new WP_Error(
			'page_not_number',
			'Param "page" is not a number, received ' . $page,
			array( 'status' => 400 )
		);
	}
}
