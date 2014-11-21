<?php
namespace WP_Gistpen\Adapter;

use WP_Gistpen\Model\Language as Model;

/**
 * This is the class description.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      [current version]
 */
class Language {

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
	 * Retrieves the term stdCLass object for a language slug
	 *
	 * @param  string $slug
	 * @return stdClass|WP_Error       term object or Error
	 * @since 0.4.0
	 */
	public function by_slug( $slug ) {
		return new Model( $this->plugin_name, $this->version, $slug );
	}

	public function blank() {
		return new Model( $this->plugin_name, $this->version );
	}
}
