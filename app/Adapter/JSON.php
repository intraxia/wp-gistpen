<?php
namespace WP_Gistpen\Adapter;

use WP_Gistpen\Model\Zip as ZipModel;
use WP_Gistpen\Adapter\File as FileAdapter;
use WP_Gistpen\Adapter\Language as LanguageAdapter;

/**
 * Builds JSON based on various
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class JSON {

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

		$this->file_adapter = new FileAdapter( $this->plugin_name, $this->version );
		$this->language_adapter = new LanguageAdapter( $this->plugin_name, $this->version );

	}

	/**
	 * Transforms a Zip to json
	 *
	 * @param  ZipModel $zip Zip to transform
	 * @return string        file data in json
	 */
	public function by_zip( ZipModel $zip ) {
		$json = new \stdClass;
		$json->zip = new \stdClass;

		$json->zip->ID = $zip->get_ID();
		$json->zip->description = $zip->get_description();
		$json->zip->status = $zip->get_status();
		$json->zip->password = $zip->get_password();
		$json->zip->gist_id = $zip->get_gist_id();

		$json->files = $this->by_files( $zip->get_files() );

		return json_encode( $json );
	}

	/**
	 * Transforms an array of files to json
	 *
	 * @param  array  $files array of files to transform
	 * @return string        file data in json
	 */
	public function by_files( $files ) {
		if ( empty( $files ) ) {
			$file = $this->file_adapter->blank();
			$file->set_language( $this->language_adapter->blank() );
			$files = array( $file );
		}

		$data = array();

		foreach ( $files as $file ) {

			$json = new \stdClass;
			$json->slug = $file->get_slug();
			$json->code = $file->get_code();
			$json->ID = $file->get_ID();
			$json->language = $file->get_language()->get_slug();

			$data[] = $json;
		}

		return $data;
	}
}
