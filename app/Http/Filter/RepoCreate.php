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
class RepoCreate implements FilterContract {
	/**
	 * Generates argument rules.
	 *
	 * Returns an array matching the WP-API format for argument rules,
	 * including sanitization, validation, required, or defaults.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'description' => [
				'description'       => __( 'A description of the Repo.', 'wp-gistpen' ),
				'required'          => true,
				'sanitize_callback' => [ $this, 'sanitize_description' ],
			],
			'status'      => [
				'description'       => __( 'Status of the Repo.', 'wp-gistpen' ),
				'required'          => false,
				'default'           => 'draft',
				'sanitize_callback' => [ $this, 'sanitize_status' ],
			],
			'sync'      => [
				'description'       => __( 'Whether the Repo should be synced to Gist.', 'wp-gistpen' ),
				'required'          => false,
				'default'           => 'off',
				'sanitize_callback' => [ $this, 'sanitize_sync' ],
			],
			'blobs' => [
				'description'       => __( 'Individual code snippets attached to the repo.', 'wp-gistpen' ),
				'required'          => false,
				'default'           => [],
				'sanitize_callback' => [ $this, 'sanitize_blobs' ],
			],
		];
	}

	/**
	 * Sanitize the description value.
	 *
	 * @param  string $description The description starting value.
	 * @return string|WP_Error     Sanitized value, or error if invalid.
	 */
	public function sanitize_description( $description ) {
		if ( ! is_string( $description ) ) {
			return $this->create_error( __( 'Param "description" must be a string.', 'wp-gistpen' ) );
		}

		return $description;
	}

	/**
	 * Santize the status value.
	 *
	 * @param  string $status  The status starting value.
	 * @return string|WP_Error The status, or an error if invalid.
	 */
	public function sanitize_status( $status ) {
		if ( ! in_array( $status, array_keys( get_post_statuses() ) ) ) {
			return $this->create_error( __( 'Param "status" must be a valid post status.', 'wp-gistpen' ) );
		}

		return $status;
	}

	/**
	 * Santizie the sync value.
	 *
	 * @param  string $sync    The provided sync value.
	 * @return string|WP_Error The sync value, or error if invalid.
	 */
	public function sanitize_sync( $sync ) {
		if ( ! in_array( $sync, [ 'on', 'off' ] ) ) {
			return $this->create_error( __( 'Param "sync" must be one of: on, off.', 'wp-gistpen' ) );
		}

		return $sync;
	}

	/**
	 * Ensure the blobs passed to the request are valid.
	 *
	 * @param  array $blobs   Blobs parameter.
	 * @return WP_Errpr|array Sanitized blobs.
	 */
	public function sanitize_blobs( $blobs ) {
		if ( ! is_array( $blobs ) ) {
			return $this->create_error( __( 'Param "blob" must be an array.', 'wp-gistpen' ) );
		}

		$new_blobs = [];

		foreach ( $blobs as $index => $blob ) {
			$new_blob = $this->sanitize_blob( $blob, $index );

			if ( is_wp_error( $new_blob ) ) {
				return $new_blob;
			}

			$new_blobs[] = $new_blob;
		}

		return $blobs;
	}

	/**
	 * Ensures the individual blob passed to blobs is valid.
	 *
	 * @param  array $blob Blob to sanitize.
	 * @param  int   $index Current loop index.
	 * @return WP_Error|array       Sanitized blob.
	 */
	public function sanitize_blob( $blob, $index ) {
		if ( ! is_array( $blob ) ) {
			return $this->create_error( sprintf(
				__( 'Param "blob[%d]" must be an object.', 'wp-gistpen' ),
				$index
			) );
		}

		if (
			// Make sure it's set
			! isset( $blob['filename'] ) ||
			// is a string
			! is_string( $blob['filename'] ) ||
			// and is not empty.
			'' === $blob['filename']
			// @todo simplyify logic?
		) {
			return $this->create_error( sprintf(
				__( 'Param "blob[%d].filename" must be a non-empty string.', 'wp-gistpen' ),
				$index
			) );
		}

		if (
			! isset( $blob['code'] ) ||
			! is_string( $blob['code'] )
		) {
			return $this->create_error( sprintf(
				__( 'Param "blob[%d].code" must be a string.', 'wp-gistpen' ),
				$index
			) );
		}

		return [
			'filename' => $blob['filename'],
			'code'     => $blob['code'],
			'language' => isset( $blob['language'] ) ? $blob['language'] : null,
		];
	}

	/**
	 * Create validation error to return.
	 *
	 * @param  string $message Validation message.
	 * @return WP_Error        Validation error.
	 */
	private function create_error( $message ) {
		return new WP_Error( 'rest_invalid_param', $message );
	}
}
