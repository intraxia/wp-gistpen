<?php
namespace Intraxia\Gistpen\CLI;

use Intraxia\Gistpen\App;
use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Http\ZipController;
use Intraxia\Gistpen\Save;
use Intraxia\Gistpen\Sync;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Facade\Database;
use WP_CLI;
use WP_REST_Request;

/**
 * Registers the CLI commands for WP-Gistpen
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Command {
	/**
	 * Database facade
	 *
	 * @var Database
	 */
	protected $database;

	/**
	 * Adapter facade
	 *
	 * @var Adapter
	 */
	protected $adapter;

	/**
	 * Gist client
	 *
	 * @var Gist
	 */
	protected $gist;

	/**
	 * Sync controller
	 *
	 * @var Sync
	 */
	protected $sync;

	/**
	 * Plugin path
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Command constructor.
	 */
	public function __construct() {
		$this->database = App::instance()->fetch( 'facade.database' );
		$this->adapter  = App::instance()->fetch( 'facade.adapter' );
		$this->gist     = App::instance()->fetch( 'account.gist' );
		$this->sync     = App::instance()->fetch( 'sync' );
		$this->path     = App::instance()->fetch( 'path' );
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
			$lang_model = new Language( $slug );
			// Code snippets sourced from: https://highlightjs.org/static/demo/
			$code = '';

			$fh = fopen( $this->path . 'test/data/' . $slug, 'r' );
			while ( $line = fgets( $fh ) ) {
				$code .= $line;
			}
			fclose( $fh );

			$zip_data = array();

			$zip_data['status']      = 'publish';
			$zip_data['description'] = $lang . ' Example';
			$zip_data['files']       = array();

			$file             = array();
			$file['code']     = trim( $code );
			$file['slug']     = $slug . '-file.' . $lang_model->get_file_ext();
			$file['language'] = $slug;

			$zip_data['files'][] = $file;

			$request = new WP_REST_Request( 'POST', rest_url() . 'intraxia/v1/gistpen/zip', $zip_data );
			$request->set_body_params( $zip_data );

			$response = $this->zip->create( $request );

			if ( is_wp_error( $response ) ) {
				WP_CLI::error( __( "Failed to add example for language {$lang}", 'wp-gistpen' ) );
			}

			$data = $response->get_data();
			$result = $this->database->persist( 'head' )->set_gist_id( $data->ID, 'none' );

			if ( is_wp_error( $result ) ) {
				WP_CLI::error( __( "Failed to add gist id for language {$lang}", 'wp-gistpen' ) );
			}

			WP_CLI::success( __( "Successfully added example for language {$lang}", 'wp-gistpen' ) );
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

		$this->gist->set_token( $token );

		if ( ! $this->gist->is_token_valid() ) {
			WP_CLI::error( __( 'Gist token failed to authenticate. Error: ', 'wp-gistpen' ) . $this->gist->get_error()->get_error_message() );
		}

		$success = cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', $token );

		if ( ! $success ) {
			WP_CLI::error( __( 'Gist token update failed.', 'wp-gistpen' ) );
		}

		WP_CLI::success( __( 'Gist token updated.', 'wp-gistpen' ) );
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
			WP_CLI::error( __( 'Failed to get post IDs missing Gist IDs. Error: ', 'wp-gistpen' ) . $ids->get_error_message() );
		}

		if ( ! $ids ) {
			WP_CLI::error( __( 'No Gistpens missing Gist IDs.', 'wp-gistpen' ) );
		}

		foreach ( $ids as $id ) {
			$this->database->persist( 'head' )->set_sync( $id, 'on' );
			$zip    = $this->database->query( 'head' )->by_id( $id );
			$result = $this->sync->export_gistpen( $zip );

			if ( is_wp_error( $result ) ) {
				WP_CLI::error( __( 'Failed to create Gist. Error: ', 'wp-gistpen' ) . $result->get_error_message() );
			}

			WP_CLI::success( __( 'Successfully exported Gistpen #', 'wp-gistpen' ) . $result->get_ID() );

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
		$gists = $this->gist->all();

		if ( ! $gists ) {
			if ( is_wp_error( $error = $this->gist->get_error() ) ) {
				WP_CLI::error( __( 'Failed to get Gist IDs for your account. Error: ', 'wp-gistpen' ) . $gists->get_error_message() );
			}

			WP_CLI::error( __( 'No Gists retrieved.', 'wp-gistpen' ) );
		}

		foreach ( $gists as $gist ) {
			$result = $this->sync->import_gist( $gist );

			if ( $result instanceof \Intraxia\Gistpen\Model\Zip ) {
				continue;
			}

			if ( is_wp_error( $result ) ) {
				WP_CLI::error( __( 'Failed to import Gist. Error: ', 'wp-gistpen' ) . $result->get_error_message() );
			}

			WP_CLI::success( __( 'Successfully imported Gist #', 'wp-gistpen' ) . $gist );
		}
	}
}
