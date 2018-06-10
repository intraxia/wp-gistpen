<?php
namespace Intraxia\Gistpen\Http\Filter;

use Intraxia\Jaxion\Contract\Http\Filter as FilterContract;
use WP_Error;

/**
 * Class Filter\Repo
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filters
 */
class Repo implements FilterContract {
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
			'blobs' => array(
				'required'          => true,
				'sanitize_callback' => array( $this, 'sanitize_blobs' ),
			),
		);
	}

	/**
	 * Ensure the blobs passed to the request are valid.
	 *
	 * @param  array $blobs   Blobs parameter.
	 * @return WP_Errpr|array Sanitized blobs.
	 */
	public function sanitize_blobs( $blobs ) {
		if ( ! is_array( $blobs ) ) {
			return new WP_Error;
		}

		foreach ( $blobs as $blob ) {
			if ( is_wp_error( $error = $this->sanitize_blob( $blob ) ) ) {
				return $error;
			}
		}

		return $blobs;
	}

	/**
	 * Ensures the individual blob passed to blobs is valid.
	 *
	 * @param  array          $blob Blob to sanitize.
	 * @return WP_Error|array       Sanitized blob.
	 */
	public function sanitize_blob( $blob ) {
		if ( ! is_array( $blob ) ) {
			return new WP_Error;
		}

		if (
			// Make sure it's set
			! isset( $blob['filename'] ) ||
			// is a string
			! is_string( $blob['filename'] ) ||
			// and is not empty.
			$blob['filename'] === ''
			// @todo simplyify logic?
		) {
			return new WP_Error;
		}

		return $blob;
	}
}
