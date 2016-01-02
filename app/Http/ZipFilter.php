<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Jaxion\Contract\Http\Filter as FilterContract;
use Intraxia\Jaxion\Http\Filter;
use WP_Error;

/**
 * Class ZipFilter
 *
 * @package    Intraxia\Gistpen
 * @subpackage Http
 */
class ZipFilter implements FilterContract {
	/**
	 * Keys required for file.
	 *
	 * @var array
	 */
	protected static $file_keys = array( 'code', 'slug', 'language' );

	/**
	 * Default rules for the filter.
	 *
	 * @var array
	 */
	private $defaults = array(
		'description' => 'required',
		'status'      => 'required',
		'files'       => 'required',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function rules() {
		$rules = call_user_func( array( new Filter( $this->defaults ), 'rules' ) );

		$rules['files']['validate_callback'] = array( $this, 'validate_files' );
		$rules['files']['sanitize_callback'] = array( $this, 'sanitize_files' );

		return $rules;
	}

	/**
	 * Validate the files property.
	 *
	 * @param mixed $files
	 *
	 * @return WP_Error|true
	 */
	public function validate_files( $files ) {
		if ( ! is_array( $files ) ) {
			return new WP_Error( 'files_not_array', __( '"files" property not an array.' ) );
		}

		foreach ( $files as $index => $file ) {
			if ( $this->invalid_file( $file ) ) {
				return new WP_Error(
					'invalid_file',
					sprintf(
						__( 'File %d is invalid', 'wp-gistpen' ),
						$index
					)
				);
			}
		}

		return true;
	}

	/**
	 * Validates whether the provided array contains all the required keys.
	 *
	 * @param array $file
	 *
	 * @return bool
	 */
	private function invalid_file( array $file ) {
		foreach ( static::$file_keys as $key ) {
			if ( ! isset( $file[ $key ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Sanitizes the file array keys.
	 *
	 * @param array $files
	 *
	 * @return array
	 */
	public function sanitize_files( array $files ) {
		$result = array();

		foreach ( $files as $file ) {
			$result[] = $this->sanitize_file( $file );
		}

		return $result;
	}

	/**
	 * Sanitizes the file keys.
	 *
	 * @param array $file
	 *
	 * @return array
	 */
	private function sanitize_file( array $file ) {
		$result = array();

		foreach ( static::$file_keys as $key ) {
			$result[ $key ] = $file[ $key ];
		}

		return $result;
	}
}
