<?php
namespace WP_Gistpen\CLI;

use \WP_CLI;
use WP_Gistpen\Account\Gist;
use WP_Gistpen\Controller\Save;
use WP_Gistpen\Controller\Sync;
use WP_Gistpen\Model\Language;
use WP_Gistpen\Facade\Adapter;
use WP_Gistpen\Facade\Database;

/**
 * Registers the CLI commands for WP-Gistpen
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Command extends \WP_CLI_Command {

	public function __construct() {
		$this->database = new Database( \WP_Gistpen::$plugin_name, \WP_Gistpen::$version );
		$this->adapter = new Adapter( \WP_Gistpen::$plugin_name, \WP_Gistpen::$version );
		$this->gist = new Gist( \WP_Gistpen::$plugin_name, \WP_Gistpen::$version );
		$this->save = new Save( \WP_Gistpen::$plugin_name, \WP_Gistpen::$version );
		$this->sync = new Sync( \WP_Gistpen::$plugin_name, \WP_Gistpen::$version );
	}

	/**
	 * Adds the test Gistpen data.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wpgp add_test_data
	 */
	function add_test_data( $args, $assoc_args ) {
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_content', 'wptexturize' );
		remove_filter( 'the_content', 'capital_P_dangit' );
		remove_filter( 'the_content', 'convert_chars' );
		remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
		// Normal filtering
		remove_filter( 'title_save_pre', 'wp_filter_kses' );

		// Comment filtering
		remove_filter( 'pre_comment_content', 'wp_filter_post_kses' );
		remove_filter( 'pre_comment_content', 'wp_filter_kses' );

		// Post filtering
		remove_filter( 'content_save_pre', 'wp_filter_post_kses' );
		remove_filter( 'excerpt_save_pre', 'wp_filter_post_kses' );
		remove_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );

		foreach ( Language::$supported as $lang => $slug ) {
			$lang_model = new Language( \WP_Gistpen::$plugin_name, \WP_Gistpen::$version, $slug );
			// Code snippets sourced from: https://highlightjs.org/static/demo/
			$code = '';

			$fh = fopen( WP_GISTPEN_DIR . 'test/data/' . $slug,'r' );
			while ( $line = fgets( $fh ) ) {
				$code .= $line;
			}
			fclose( $fh );

			$zip_data = array();

			$zip_data['status'] = 'publish';
			$zip_data['description'] = $lang . ' Example';
			$zip_data['files'] = array();

			$file = array();
			$file['code'] = trim( $code );
			$file['slug'] = $slug . '-file.' . $lang_model->get_file_ext();
			$file['language'] = $slug;

			$zip_data['files'][] = $file;

			$this->save->update( $zip_data );

			WP_CLI::success( __( "Successfully added example for language {$lang}", \WP_Gistpen::$plugin_name ) );
			sleep( 1 );
		}
	}

	/**
	 * Sets the Gist token.
	 *
	 * Get your Gist token by following
	 * the instructions found here:
	 * http://jamesdigioia.com/wp-gistpen/#gist-token
	 *
	 * ## OPTIONS
	 *
	 * <token>
	 * : Your Gist token.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wpgp set_token 123545678910abcde
	 *
	 * @synopsis <token>
	 */
	function set_token( $args, $assoc_args ) {
		list( $token ) = $args;

		$client = new Gist( \WP_Gistpen::$plugin_name, \WP_Gistpen::$version );

		$client->authenticate( $token );

		if ( is_wp_error( $error = $client->check_token() ) ) {
			WP_CLI::error( __( 'Gist token failed to authenticate. Error: ', \WP_Gistpen::$plugin_name ) . $error->get_error_message() );
		}

		$success = cmb2_update_option( \WP_Gistpen::$plugin_name, '_wpgp_gist_token', $token );

		if ( ! $success ) {
			WP_CLI::error( __( 'Gist token update failed.', \WP_Gistpen::$plugin_name ) );
		}

		WP_CLI::success( __( 'Gist token updated.', \WP_Gistpen::$plugin_name ) );
	}

	/**
	 * Exports Gistpens
	 *
	 * If your Gist account is authorized, exports
	 * all Gistpens not already exported to Gist.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wpgp export_gistpens
	 *
	 */
	function export_gistpens( $args, $assoc_args ) {
		$ids = $this->database->query( 'head' )->missing_gist_id();

		if ( is_wp_error( $ids ) ) {
			WP_CLI::error( __( 'Failed to get post IDs missing Gist IDs. Error: ', $this->plugin_name ) . $ids->get_error_message() );
		}

		if ( empty( $ids ) ) {
			WP_CLI::error( __( 'No Gistpens missing Gist IDs.', \WP_Gistpen::$plugin_name ) );
		}

		foreach ( $ids as $id ) {
			$this->database->persist( 'head' )->set_sync( $id, 'on' );
			$result = $this->sync->export_gistpen( $id );

			if ( is_wp_error( $result ) ){
				WP_CLI::error( __( 'Failed to create Gist. Error: ', \WP_Gistpen::$plugin_name ) . $result->get_error_message() );
			}

			WP_CLI::success( __( 'Successfully exported Gistpen #', \WP_Gistpen::$plugin_name ) . $result );

			sleep( 1 );
		}
	}

	/**
	 * Import Gists
	 *
	 * If your Gist account is authorized, imports
	 * all Gists not already imported to Gistpen.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wpgp import_gists
	 *
	 */
	function import_gists( $args, $assoc_args ) {
		$gists = $this->gist->get_gists();

		if ( is_wp_error( $gists ) ) {
			WP_CLI::error( __( 'Failed to get Gist IDs for your account. Error: ', $this->plugin_name ) . $gists->get_error_message() );
		}

		if ( empty( $gists ) ) {
			WP_CLI::error( __( 'No Gists retrieved.', \WP_Gistpen::$plugin_name ) );
		}

		foreach ( $gists as $gist ) {
			$result = $this->sync->import_gist( $gist );

			if ( $result instanceof \WP_Gistpen\Model\Zip ) {
				continue;
			}

			if ( is_wp_error( $result ) ){
				WP_CLI::error( __( 'Failed to import Gist. Error: ', \WP_Gistpen::$plugin_name ) . $result->get_error_message() );
			}

			WP_CLI::success( __( 'Successfully imported Gist #', \WP_Gistpen::$plugin_name ) . $gist );
		}
	}
}
