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
	 * Array containing all query objects
	 *
	 * @var array
	 * @since 0.5.0
	 */
	protected $query = array();

	/**
	 * Array containing all persistence objects
	 *
	 * @var array
	 * @since 0.5.0
	 */
	protected $persistence = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		$this->query['head'] = new HeadQuery();
		$this->query['commit'] = new CommitQuery();

		$this->persistence['head'] = new HeadPersistance();
		$this->persistence['commit'] = new CommitPersistance();
	}

	/**
	 * Query the database
	 *
	 * @param string $type
	 * @return mixed
	 * @throws \Exception
	 * @since 0.5.0
	 */
	public function query( $type = 'head' ) {
		if ( ! array_key_exists( $type, $this->query ) ) {
			throw new \Exception( "Can't query on type {$type}" );
		}

		return $this->query[ $type ];
	}

	/**
	 * Persist to database.
	 *
	 * @param string $type
	 * @return mixed persistence object
	 * @throws \Exception
	 * @since 0.5.0
	 */
	public function persist( $type = 'head' ) {
		if ( ! array_key_exists( $type, $this->persistence ) ) {
			throw new \Exception( "Can't persist on type {$type}" );
		}

		return $this->persistence[ $type ];
	}
}
