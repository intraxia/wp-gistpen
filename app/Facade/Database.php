<?php
namespace WP_Gistpen\Facade;

use WP_Gistpen\Database\Query\Head as HeadQuery;
use WP_Gistpen\Database\Query\Commit as CommitQuery;
use WP_Gistpen\Database\Persistance\Head as HeadPersistance;
use WP_Gistpen\Database\Persistance\Commit as CommitPersistance;

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
	 * Array containing all query objects
	 *
	 * @var array
	 * @since 0.5.0
	 */
	private $query = array();

	/**
	 * Array containing all persistance objects
	 *
	 * @var array
	 * @since 0.5.0
	 */
	private $persistance = array();

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

		$this->query['head'] = new HeadQuery( $this->plugin_name, $this->version );
		$this->query['commit'] = new CommitQuery( $this->plugin_name, $this->version );

		$this->persistance['head'] = new HeadPersistance( $this->plugin_name, $this->version );
		$this->persistance['commit'] = new CommitPersistance( $this->plugin_name, $this->version );

	}

	/**
	 * Query the database
	 *
	 * @return Query query object
	 * @since 0.5.0
	 */
	public function query( $type = 'head' ) {
		if ( ! array_key_exists( $type, $this->query ) ) {
			throw new \Exception( "Can't query on type {$type}" );
		}

		return $this->query[ $type ];
	}

	/**
	 * Persist to database
	 *
	 * @return Persistance persistance object
	 * @since 0.5.0
	 */
	public function persist( $type = 'head' ) {
		if ( ! array_key_exists( $type, $this->persistance ) ) {
			throw new \Exception( "Can't persist on type {$type}" );
		}

		return $this->persistance[ $type ];
	}
}
