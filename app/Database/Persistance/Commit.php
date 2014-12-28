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
 * @package Commit
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Commit {

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
	 * @param  Zip              $parent_zip   Zip model of the parent to save
	 * @return array|\WP_Error                revision meta saved, WP_Error if failed
	 * @since  0.4.0
	 */
	public function by_parent_zip( $parent_zip ) {
		$revisions_meta = get_post_meta( $parent_zip->get_ID(), 'wpgp_revisions', true );

		if ( empty( $revisions_meta ) ) {
			$revisions_meta = array();
		}

		$result = wp_save_post_revision( $parent_zip->get_ID() );

		if ( 0 === $result || null === $result ) {
			return new \WP_Error( 'revision_save_fail', __( 'Failed to save revision for ', $this->plugin_name ) . $parent_zip->get_ID() );
		}

		$revision_id = $result;

		$files = $parent_zip->get_files();
		$file_ids = array();

		foreach ( $files as $file ) {
			$file_id = $file->get_ID();

			wp_save_post_revision( $file_id );

			$file_revisions = wp_get_post_revisions( $file_id );
			$file_ids[] = array_shift( $file_revisions );
		}

		$revisions_meta[ $revision_id ]['files'] = $file_ids;

		update_post_meta( $parent_zip->get_ID(), 'wpgp_revisions', $revisions_meta );

		return $revisions_meta;
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
			'post_type'   => 'revision',
			'post_status' => 'inherit',
		);
		$data = array_merge( $defaults, $data );

		return wp_insert_post( $data, true );
	}
}
