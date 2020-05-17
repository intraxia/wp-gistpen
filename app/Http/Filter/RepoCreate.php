<?php
namespace Intraxia\Gistpen\Http\Filter;

use WP_Error;

/**
 * Class Filter\Repo
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filters
 */
class RepoCreate extends BaseFilter {
	/**
	 * BlobCreate filter.
	 *
	 * @var BlobCreate
	 */
	protected $blob;

	/**
	 * Constructor.
	 *
	 * @param BlobCreate $blob
	 */
	public function __construct( BlobCreate $blob ) {
		$this->blob = $blob;
	}

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
				'description' => __( 'A description of the Repo.', 'wp-gistpen' ),
				'required'    => true,
				'type'        => 'string',
			],
			'status'      => [
				'description' => __( 'Status of the Repo.', 'wp-gistpen' ),
				'required'    => false,
				'default'     => 'draft',
				'type'        => 'string',
				'enum'        => array_keys( get_post_statuses() ),
			],
			'password'    => [
				'description' => __( 'Password for the Repo.', 'wp-gistpen' ),
				'required'    => false,
				'default'     => '',
			],
			'sync'        => [
				'description' => __( 'Whether the Repo should be synced to Gist.', 'wp-gistpen' ),
				'required'    => false,
				'default'     => 'off',
				'type'        => 'string',
				'enum'        => [ 'on', 'off' ],
			],
			'blobs'       => [
				'description' => __( 'Individual code snippets attached to the repo.', 'wp-gistpen' ),
				'required'    => false,
				'default'     => [],
				'type'        => 'array',
				'items'       => [
					'type'                 => 'object',
					'properties'           => $this->blob->rules(),
					'additionalProperties' => false,
				],
			],
		];
	}
}
