<?php
namespace WP_Gistpen;
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use WP_Gistpen\Facade\Adapter;
use WP_Gistpen\Facade\Database;
use \WP_Query;

/**
 * This class checks the current version and runs any updates necessary.
 *
 * @package Migration
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Migration {

	/**
	 * Languages removed from version 0.3.0
	 * Support for these languages needs to be readded in the future
	 *
	 * @var array
	 * @since 0.3.0
	 */
	public $removed_langs_0_3_0 = array(
		'AppleScript' => 'applescript',
		'ActionScript3' => 'as3',
		'ColdFusion' => 'cf',
		'CPP' => 'cpp',
		'Delphi' => 'delphi',
		'Diff' => 'diff',
		'Erlang' => 'erl',
		'JavaFX' => 'jfx',
		'Perl' => 'perl',
		'Vb' => 'vb',
	);

	/**
	 * Languages added in version 0.3.0
	 *
	 * @var array
	 * @since 0.3.0
	 */
	public $added_langs_0_3_0 = array(
		'C' => 'c',
		'Coffeescript' => 'coffeescript',
		'C#' => 'csharp',
		'Go' => 'go',
		'HTTP' => 'http',
		'ini' => 'ini',
		'HTML/Markup' => 'markup',
		'Objective-C' => 'objectivec',
		'Swift' => 'swift',
		'Twig' => 'twig',
	);

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
	 * Database Facade object
	 *
	 * @var Facade\Database
	 * @since 0.5.0
	 */
	private $database;

	/**
	 * Adapter Facade object
	 *
	 * @var Facade\Adapter
	 * @since  0.5.0
	 */
	private $adapter;

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

		$this->database = new Database( $plugin_name, $version );
		$this->adapter = new Adapter( $plugin_name, $version );

	}

	/**
	 * Check current version and run migration if necessary
	 *
	 * @since 0.3.0
	 */
	public function run() {
		// Check if plugin needs to be upgraded
		$version = get_option( 'wp_gistpen_version', '0.0.0' );

		if ( $version !== $this->version ) {
			$this->update( $version );
			update_option( 'wp_gistpen_version', $this->version );
		}
	}

	/**
	 * Checks current version and updates the database accordingly
	 *
	 * @param  string $version Current version number
	 * @since  0.3.0
	 */
	public function update( $version ) {

		if ( version_compare( $version, '0.3.0', '<' ) ) {
			$this->update_to_0_3_0();
		}

		if ( version_compare( $version, '0.4.0', '<' ) ) {
			$this->update_to_0_4_0();
		}

		if ( version_compare( $version, '0.5.2', '<' ) ) {
			$this->update_to_0_5_0();
			$this->update_to_0_5_1();
		}

	}

	/**
	 * Update the database to version 0.3.0
	 *
	 * @since 0.3.0
	 */
	public function update_to_0_3_0() {

		foreach ( $this->added_langs_0_3_0 as $lang => $slug ) {
			$result = wp_insert_term( $lang, 'language', array( 'slug' => $slug ) );
			if ( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( 'WP-Gistpen' );
				// and provide an error
				print ( __( "Failed to successfully insert {$slug}. Error: " . $result->get_error_message(), $this->plugin_name ) );
			}
		}

		foreach ( $this->removed_langs_0_3_0 as $lang => $slug ) {
			// check if there are any gistpens in the db for this language
			$query = new WP_Query(
				array(
					'language' => $slug,
					'post_type' => 'gistpens',
				));

			if ( ! $query->have_posts() ) {
				// only delete language if it's got no Gistpens
				$term = get_term_by( 'slug', $slug, 'language', 'ARRAY_A' );
				$result = wp_delete_term( $term['term_id'], 'language' );
				if ( is_wp_error( $result ) ) {
					// Deactivate and quit
					deactivate_plugins( 'WP-Gistpen' );
					// and provide an error
					print ( __( "Failed to successfully delete {$slug}. Error: " . $result->get_error_message(), $this->plugin_name ) );
				}
			}
		}

	}

	/**
	 * Update the database to version 0.4.0
	 *
	 * @since 0.4.0
	 */
	public function update_to_0_4_0() {
		// We removed this post_type and taxonomy, so we need to add them to use them
		register_post_type( 'gistpens', array() );
		register_taxonomy( 'language', array( 'gistpens' ) );

		// Need to remove these filters first
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_content', 'wptexturize' );
		remove_filter( 'the_content', 'capital_P_dangit' );
		remove_filter( 'the_content', 'convert_chars' );
		remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );

		$terms = get_terms( 'language', 'hide_empty=0' );
		foreach ( $terms as $term ) {
			// We're going to move the current term to a holdover
			$result = wp_update_term( $term->term_id, 'language', array(
				'slug' => $term->slug . '-old',
				'name' => $term->name . '-old',
			) );
			if ( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( 'WP-Gistpen' );
				// and provide an error
				print ( __( "Failed to successfully set holdover for language {$term->slug}. Error: " . $result->get_error_message(), $this->plugin_name ) );
			}

			// So we can create new terms with the old slug/name combo
			$result = wp_insert_term( $term->name, 'wpgp_language', array(
				'slug' => $term->slug,
				'name' => $term->name,
			) );
			if ( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( 'WP-Gistpen' );
				// and provide an error
				print ( __( "Failed to successfully insert language {$term->slug}. Error: " . $result->get_error_message(), $this->plugin_name ) );
			}
		}

		// Get all the Gistpens to work with
		$posts = get_posts( array(
			'post_type' => 'gistpens',
			'posts_per_page' => -1,
			'post_status' => 'any',
		) );

		foreach ( $posts as $post ) {
			// Cache and clear content
			$content = $post->post_content;
			$post->post_content = '';

			// Update post type to remove the 's'
			$post->post_type = 'gistpen';

			$zip = $this->adapter->build( 'zip' )->by_post( $post );
			$file = $this->adapter->build( 'file' )->blank();

			// Migrate title to file's name
			$file->set_slug( $post->post_title );

			// Migrate description to Gistpen title and remove post_meta
			$zip->set_description( get_post_meta( $post->ID, '_wpgp_gistpen_description', true ) );
			$result = delete_post_meta( $post->ID, '_wpgp_gistpen_description' );

			if ( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( 'WP-Gistpen' );
				// and provide an error
				print ( __( "Failed to successfully delete description meta from {$post->ID}. Error: " . $result->get_error_message(), $this->plugin_name ) );
			}

			// Set content
			$file->set_code( $content );

			// Migrate Gistpen's language and remove
			// @todo move this into helper function?
			$terms = get_the_terms( $post->ID, 'language' );
			if ( $terms ) {
				$lang = array_pop( $terms );
			} else {
				$lang = '';
			}

			// Don't forget to remove that holdover!
			$file->set_language( $this->adapter->build( 'language' )->by_slug( str_replace( '-old', '', $lang->slug ) ) );

			$result = wp_set_object_terms( $post->ID, array(), 'language', false );

			if ( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( 'WP-Gistpen' );
				// and provide an error
				print ( __( "Failed to successfully delete language from {$post->ID}. Error: " . $result->get_error_message(), $this->plugin_name ) );
			}

			$zip->add_file( $file );

			$result = $this->database->persist()->by_zip( $zip );

			if ( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( __FILE__);
				// and provide an error
				print ( __( "Failed to successfully save {$post->ID} in new format. Error: " . $result->get_error_message(), $this->plugin_name ) );
			}
		}

		$terms = get_terms( 'language', 'hide_empty=0' );

		foreach ( $terms as $term ) {
			wp_delete_term( $term->term_id, 'language' );
		}

		flush_rewrite_rules( true );
	}

	/**
	 * Update the database to version 0.5.0
	 *
	 * Takes care of:
	 * * Deleting all the current (useless) post revisions
	 * * Add a new revision at the current layout
	 * * Add gist_id = none post_meta to all Gistpens
	 *
	 * @since 0.5.0
	 */
	public function update_to_0_5_0() {
		delete_option( 'wp_gistpens_languages_installed' );
		delete_option( 'wp_gistpen_langs_installed' );

		// Need to remove these filters first
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_content', 'wptexturize' );
		remove_filter( 'the_content', 'capital_P_dangit' );
		remove_filter( 'the_content', 'convert_chars' );
		remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );

		$posts = get_posts( array(
			'post_type' => 'revision',
			'post_status' => 'any',
			'nopaging' => 'true',
		));

		foreach ( $posts as $post ) {
			if ( get_post_type( $post->post_parent ) === 'gistpen' ) {
				wp_delete_post( $post->ID, true );
			}
		}

		$posts = get_posts( array(
			'post_type'   => 'gistpen',
			'post_status' => 'any',
			'nopaging'    => 'true',
			'post_parent' => 0,
		));

		foreach ( $posts as $post ) {
			$ids = array();

			$zip = $this->database->query( 'head' )->by_post( $post );

			$ids['zip'] = $zip->get_ID();
			$ids['files'] = array();
			$files = $zip->get_files();

			foreach ( $files as $file ) {
				$ids['files'][] = $file->get_ID();
			}

			$this->database->persist( 'commit' )->by_ids( $ids );

			update_post_meta( $post->ID, '_wpgp_gist_id', 'none' );
		}
	}

	/**
	 * Fixes a database bug with the filename
	 *
	 * @since 0.5.1
	 */
	public function update_to_0_5_1() {
		$posts = get_posts( array(
			'post_type' => 'revision',
			'post_status' => 'any',
			'nopaging' => 'true',
		));

		foreach ( $posts as $post ) {
			if ( get_post_type( $post->post_parent ) !== 'gistpen' ) {
				continue;
			}

			$head_post = get_post( $post->post_parent );

			if ( 0 !== $head_post->post_parent ) {
				wp_update_post( array(
					'ID' => $post->ID,
					'post_title' => ! empty( $head_post->post_title ) ? $head_post->post_title : $head_post->post_name,
				) );
			}
		}
	}

}
