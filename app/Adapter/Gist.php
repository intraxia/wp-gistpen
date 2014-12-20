<?php
namespace WP_Gistpen\Adapter;

/**
 * This is the class description.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.dev/wp-gistpen/
 * @since      0.5.0
 */
class Gist {

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
	 * Converts a Zip into API array for Gist
	 *
	 * @param  \WP_Gistpen\Model\Zip $zip Zip to turn into API data
	 * @return array      Gist data
	 */
	public function create_by_zip( $zip ) {
		$gist = array(
			'description' => $zip->get_description(),
		);

		if ( $zip->get_status() === 'publish' ) {
			$gist['public'] = true;
		} else {
			$gist['public'] = false;
		}

		$files = array();

		foreach ( $zip->get_files() as $file ) {
			$files[ $file->get_filename() ] = array( 'content' => $file->get_code() );
		}

		$gist['files'] = $files;

		return $gist;
	}
}
