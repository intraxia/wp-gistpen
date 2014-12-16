<?php
namespace WP_Gistpen\CLI;

use \WP_CLI;
use WP_Gistpen\Account\Gist;
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
			// Code snippets sourced from: https://highlightjs.org/static/demo/
			$code = '';

			$fh = fopen( WP_GISTPEN_DIR . 'test/data/' . $slug,'r' );
			while ( $line = fgets( $fh ) ) {
				$code .= $line;
			}
			fclose( $fh );

			$zip = $this->adapter->build( 'zip' )->blank();

			$zip->set_status( 'publish' );
			$zip->set_description( $lang . ' Example' );

			$file = $this->adapter->build( 'file' )->blank();
			$file->set_code( trim( $code ) );
			$file->set_slug( $slug . '-file' );
			$file->set_language( $this->adapter->build( 'language' )->by_slug( $slug ) );

			$zip->add_file( $file );

			$this->database->persist( 'head' )->by_zip( $zip );
			$this->database->persist( 'commit' )->by_parent_zip( $zip );

			WP_CLI::success( __( "Successfully added language {$lang}", \WP_Gistpen::$plugin_name ) );
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
		$success = false;

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
}
