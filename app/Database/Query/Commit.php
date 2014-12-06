<?php
namespace WP_Gistpen\Database\Query;

/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use WP_Gistpen\Facade\Adapter;
use WP_Gistpen\Database\Query\Head as HeadQuery;

/**
 * This class saves and gets Gistpen commits from the database
 *
 * @package WP_Gistpen_Query
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
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	private $adapter;

	/**
	 * Database Facade object
	 *
	 * @var Database
	 * @since 0.5.0
	 */
	private $database;

	/**
	 * Default query args
	 *
	 * @var  array
	 * @since 0.5.0
	 */
	private $args;

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

		$this->adapter = new Adapter( $plugin_name, $version );
		$this->head = new HeadQuery( $plugin_name, $version );

	}

	/**
	 * Gets and builds an object model based on a post's ID
	 *
	 * @param  int $post_id model's post ID
	 * @return object       WP_Gistpen model object
	 * @since 0.5.0
	 */
	public function all_by_parent_id( $parent_id ) {
		$revisions_meta = get_post_meta( $parent_id, 'wpgp_revisions', true );
		$revisions = array();

		if ( empty( $revisions_meta ) ) {
			return $revisions;
		}

		foreach ( $revisions_meta as $revision_id => $revision_meta ) {
			$zip_post = get_post( $revision_id );

			$zip = $this->adapter->build( 'zip' )->by_post( $zip_post );

			foreach ( $revision_meta['files'] as $file_id ) {
				$file_post = get_post( $file_id );

				$file = $this->adapter->build( 'file' )->by_post( $file_post );

				$file->set_language( $this->head->language_by_post_id( $file_id ) );

				$zip->add_file( $file );
			}

			$revisions[] = $zip;
		}

		return $revisions;
	}

}
