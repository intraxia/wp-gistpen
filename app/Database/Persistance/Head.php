<?php
namespace WP_Gistpen\Database\Persistance;
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use WP_Gistpen\Model\File;
use WP_Gistpen\Model\Language;

/**
 * This class manipulates the saving of parent Gistpen
 * and all child Gistpens.
 *
 * @package Head
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Head {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Save the Zip to the database
	 *
	 * @param  Zip $post
	 * @return int|WP_Error    post_id on success, WP_Error on failure
	 * @since  0.5.0
	 */
	public function by_zip( $zip ) {
		$data = array(
			'post_title'    => $zip->get_description(),
			'post_status'   => $zip->get_status(),
			'post_password' => $zip->get_password(),
		);

		if ( $zip->get_ID() !== null ) {
			$data['ID'] = $zip->get_ID();
		}

		$result = $this->by_array( $data );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$post_id = $result;
		unset($result);

		$files = $zip->get_files();

		foreach ( $files as $id => $file ) {
			$data = array(
				'post_title'    => $file->get_slug(),
				'post_content'  => $file->get_code(),
				'post_status'   => $zip->get_status(),
				'post_parent'   => $post_id,
				'post_password' => $zip->get_password(),
			);

			if ( $file->get_ID() !== null ) {
				$data['ID'] = $file->get_ID();
			}

			$result = $this->by_array( $data );

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			$result = wp_set_object_terms( $result, $file->get_language()->get_slug(), 'wpgp_language', false );

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return $post_id;
	}

	/**
	 * Save the File to the database with a given $zip_id
	 *
	 * @param  File $file File model object
	 * @param  int $zip_id ID of the zip parent
	 * @return int|WP_Error   post_id on success, WP_Error on failure
	 * @since  0.5.0
	 */
	public function by_file_and_zip_id( $file, $zip_id ) {
		$data = array(
			'post_title'    => $file->get_slug(),
			'post_content'  => $file->get_code(),
			'post_status'   => get_post_status( $zip_id ),
			'post_parent'   => $zip_id,
		);

		if ( $file->get_ID() !== null ) {
			$data['ID'] = $file->get_ID();
		}
		if ( $file->get_language() !== null ) {
			$data['tax_input'] = array( 'wpgp_language' => $file->get_language()->get_slug() );
		}

		$result = $this->by_array( $data );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$post_id = $result;
		unset($result);

		return $post_id;

	}

	/**
	 * Save a Gistpen by array
	 *
	 * @param  array $data Array of Gistpen data
	 * @return int|WP_Error       Saved Gistpen's ID or WP_Error on failure
	 * @since  0.5.0
	 */
	public function by_array( $data ) {
		$defaults = array(
			'post_type'   => 'gistpen',
			'post_status' => 'auto-draft',
		);
		$data = array_merge( $defaults, $data );

		return wp_insert_post( $data, true );
	}

	/**
	 * Save a Gist ID to the Gistpen
	 *
	 * @param  int    $zip_id  post ID of zip to update
	 * @param  string $gist_id Gist ID to save
	 * @since  0.5.0
	 */
	public function set_gist_id( $zip_id, $gist_id ) {
		return update_post_meta( $zip_id, '_wpgp_gist_id', $gist_id );
	}
}
