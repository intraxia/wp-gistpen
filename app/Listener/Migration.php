<?php
namespace Intraxia\Gistpen\Listener;

/**
 * Class Migration
 *
 * @package   Intraxia\Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */


use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use Intraxia\Jaxion\Contract\Axolotl\UsesCustomTable;
use Intraxia\Jaxion\Contract\Core\HasActions;

/**
 * This class checks the current version and runs any updates necessary.
 *
 * @package    Intraxia\Gistpen
 * @subpackage Listener
 * @author     James DiGioia <jamesorodig@gmail.com>
 */
class Migration implements HasActions {

	/**
	 * Database service.
	 *
	 * @var EntityManager
	 */
	private $em;

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Plugin version number.
	 *
	 * @var string
	 */
	private $version;


	/**
	 * Plugin prefix.
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 *
	 * @param EntityManager $em
	 * @param string        $prefix
	 * @param string        $slug
	 * @param string        $version
	 */
	public function __construct( EntityManager $em, $prefix, $slug, $version ) {
		$this->em      = $em;
		$this->prefix  = $prefix;
		$this->slug    = $slug;
		$this->version = $version;
	}

	/**
	 * Check current version and run migration if necessary.
	 *
	 * @since 0.3.0
	 */
	public function run() {
		// Check if plugin needs to be upgraded
		$version = get_option( 'wp_gistpen_version', '0.0.0' );

		if ( $version !== $this->version ) {
			$this->update( $version );
			update_option( 'wp_gistpen_version', $this->version );
		}
	}

	/**
	 * Checks current version and updates the database accordingly.
	 *
	 * @param  string $version Current version number.
	 * @since  0.3.0
	 */
	public function update( $version ) {
		if ( version_compare( $version, '1.0.0', '<' ) ) {
			$this->update_to_1_0_0();
		}
	}

	/**
	 * Update Database options to new format.
	 *
	 * @since 1.0.0
	 */
	public function update_to_1_0_0() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$runs_table     = $this->em->make_table_name( \Intraxia\Gistpen\Model\Run::class );
		$messages_table = $this->em->make_table_name( \Intraxia\Gistpen\Model\Message::class );

		dbDelta("
			CREATE TABLE {$runs_table} (
			  ID BIGINT(20) UNSIGNED AUTO_INCREMENT,
			  items LONGTEXT,
			  job VARCHAR(64) NOT NULL,
			  status VARCHAR(16) NOT NULL,
			  scheduled_at DATETIME NOT NULL,
			  started_at DATETIME,
			  finished_at DATETIME,
			  PRIMARY KEY  (ID)
			);
		");

		dbDelta("
			CREATE TABLE {$messages_table} (
			  ID BIGINT(20) UNSIGNED AUTO_INCREMENT,
			  run_id BIGINT(20) UNSIGNED NOT NULL,
			  text TINYTEXT NOT NULL,
			  level VARCHAR(32) NOT NULL,
			  logged_at DATETIME NOT NULL,
			  PRIMARY KEY  (ID)
			)
		");

		$old_opts = get_option( 'wp-gistpen' );

		if ( ! $old_opts ) {
			return;
		}

		delete_option( 'wp-gistpen' );

		update_option( $this->slug . '_no_priv', array(
			'prism' => array(
				'theme'           => $old_opts['_wpgp_gistpen_highlighter_theme'],
				'line-numbers'    => $old_opts['_wpgp_gistpen_line_numbers'],
				'show-invisibles' => 'off',
			),
		));
		update_option( $this->slug . '_priv', array( 'gist' => array( 'token' => $old_opts['_wpgp_gist_token'] ) ) );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return array[]
	 */
	public function action_hooks() {
		return array(
			array(
				'hook'   => 'admin_init',
				'method' => 'run',
			),
		);
	}
}
