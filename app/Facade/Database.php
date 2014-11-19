<?php
namespace WP_Gistpen\Facade;

use WP_Gistpen\Database\Query;
use WP_Gistpen\Database\Persistance;
use WP_Gistpen\Database\Migration;

/**
 * This class handles all of the AJAX responses
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Database {

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

		$this->query = new Query( $this->plugin_name, $this->version );
		$this->persistance = new Persistance( $this->plugin_name, $this->version );
		$this->migration = new Migration( $this->plugin_name, $this->version );

	}

	public function query() {
		return $this->query;
	}

	public function persist() {
		return $this->persistance;
	}
}
