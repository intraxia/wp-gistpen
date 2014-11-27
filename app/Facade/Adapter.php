<?php
namespace WP_Gistpen\Facade;

use WP_Gistpen\Adapter\File as FileAdapter;
use WP_Gistpen\Adapter\Language as LanguageAdapter;
use WP_Gistpen\Adapter\Zip as ZipAdapter;

/**
 * This is the class description.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Adapter {

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
	 * Adapter\File object
	 *
	 * @var Adapter\File
	 * @since 0.5.0
	 */
	private $file;

	/**
	 * Adapter\Language object
	 *
	 * @var Adapter\Language
	 * @since 0.5.0
	 */
	private $language;

	/**
	 * Adapter\Zip object
	 *
	 * @var Adapter\Zip
	 * @since 0.5.0
	 */
	private $zip;

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

		$this->file = new FileAdapter( $plugin_name, $version );
		$this->language = new LanguageAdapter( $plugin_name, $version );
		$this->zip = new ZipAdapter( $plugin_name, $version );

	}

	/**
	 * Return the Adapter object for the specified model.
	 *
	 * @since    0.5.0
	 * @var      string    $model       The model type to prepare to build.
	 */
	public function build( $model ) {

		if ( ! property_exists( $this, $model ) ) {
			throw new \Exception( "Can't build model {$model}" );
		}

		return $this->{$model};
	}

	/**
	 * Build an array of models using an array of posts as input
	 *
	 * @param  array $posts Array of WP_Post objects
	 * @return array        Array of Gistpen model objects
	 * @since 0.5.0
	 */
	public function build_by_array_of_posts( $posts ) {
		$models = array();

		foreach ( $posts as $post ) {
			if ( 0 === $post->post_parent ) {
				$models[] = $this->build( 'zip' )->by_post( $post );
			} else {
				$models[] = $this->build( 'file' )->by_post( $post );
			}
		}

		return $models;
	}
}
