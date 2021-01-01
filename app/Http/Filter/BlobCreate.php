<?php
namespace Intraxia\Gistpen\Http\Filter;

use Intraxia\Jaxion\Core\Config;
use Intraxia\Gistpen\Model\Language;
use WP_Error;

/**
 * Class Filter\BlobCreate
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filters
 */
class BlobCreate extends BaseFilter {
	/**
	 * Constructor.
	 *
	 * @param Config $config
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
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
		$languages = $this->config->get_json_resource( 'languages' );

		return [
			'filename' => [
				'description'       => __( 'Blob filename.', 'wp-gistpen' ),
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => [ $this, 'sanitize_filename' ],
			],
			'code'     => [
				'description' => __( 'Code for the blob.', 'wp-gistpen' ),
				'required'    => false,
				'default'     => '',
				'type'        => 'string',
			],
			'language' => [
				'description' => __( 'Language the blob code is in.', 'wp-gistpen' ),
				'type'        => 'string',
				'enum'        => array_merge(
					array_keys( $languages['list'] ),
					array_values( $languages['aliases'] )
				),
				'default'     => 'plaintext',
			],
		];
	}

	/**
	 * Sanitize the Blob's filename.
	 *
	 * @param string $filename
	 */
	public function sanitize_filename( $filename ) {
		if ( '' === $filename ) {
			return $this->create_error( sprintf(
				__( 'Param "filename" must be a non-empty string.', 'wp-gistpen' )
			) );
		}

		return $filename;
	}
}
