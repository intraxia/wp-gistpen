<?php
namespace WP_Gistpen\Adapter;

use WP_Gistpen\Model\Zip as ZipModel;
use \stdClass;

/**
 * Builds JSON based on various
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Api {

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
	 * Transforms a Zip to json
	 *
	 * @param  ZipModel $zip Zip to transform
	 * @return string        file data in json
	 */
	public function by_zip( ZipModel $zip ) {
		$api = new stdClass;

		$api->ID = $zip->get_ID();
		$api->description = $zip->get_description();
		$api->status = $zip->get_status();
		$api->password = $zip->get_password();
		$api->gist_id = $zip->get_gist_id();

		$api->files = $this->by_files( $zip->get_files() );

		return $api;
	}

	/**
	 * Transforms an array of files to json
	 *
	 * @param  array  $files array of files to transform
	 * @return string        file data in json
	 */
	public function by_files( $files ) {
		$data = array();

		if ( empty( $files ) ) {
			$api = new stdClass;
			$api->slug = '';
			$api->code = '';
			$api->ID = null;
			$api->language = '';

			$data[] = $api;
		} else {
			foreach ( $files as $file ) {
				$api = new stdClass;
				$api->slug = $file->get_slug();
				$api->code = $file->get_code();
				$api->ID = $file->get_ID();
				$api->language = $file->get_language()->get_slug();

				$data[] = $api;
			}
		}

		return $data;
	}

	public function by_history( $history ) {
		$commits = $history->get_commits();

		$api = array();

		foreach ( $commits as $commit ) {
			$commit_json = new stdClass;

			$commit_json->commit_id = $commit->get_commit_id();
			$commit_json->head_id = $commit->get_head_id();
			$commit_json->create_date = $commit->get_create_date();
			$commit_json->saved_files = $commit->get_saved_files();
			$commit_json->deleted_files = $commit->get_deleted_files();

			$api[] = $commit_json;
		}

		return json_encode( $api );
	}
}
