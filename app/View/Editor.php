<?php
namespace WP_Gistpen\View;

use WP_Gistpen\Facade\Database;
use WP_Gistpen\Facade\Adapter;
use WP_Gistpen\Model\Language;

/**
 * This class registers all of the settings page views
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Editor {

	/**
	 * All the Ace themes for select box
	 *
	 * @var array
	 * @since    0.4.0
	 */
	public $ace_themes = array(
		'ambiance' => 'Ambiance',
		'chaos' => 'Chaos',
		'chrome' => 'Chrome',
		'clouds' => 'Clouds',
		'clouds_midnight' => 'Clouds Midnight',
		'cobalt' => 'Cobalt',
		'crimson_editor' => 'Crimson Editor',
		'dawn' => 'Dawn',
		'dreamweaver' => 'Dreamweaver',
		'eclipse' => 'Eclipse',
		'github' => 'GitHub',
		'idle_fingers' => 'Idle Fingers',
		'katzenmilch' => 'Katzenmilch',
		'kr_theme' => 'KR',
		'kuroir' => 'Kuroir',
		'merbivore' => 'Merbivore',
		'monokai' => 'Monokai',
		'solarized_dark' => 'Solarized Dark',
		'solarized_light' => 'Solarized Light',
		'twilight' => 'Twilight',
	);

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
	 * Database Facade object
	 *
	 * @var Database
	 * @since 0.5.0
	 */
	private $database;

	/**
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	private $adapter;

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

		$this->database = new Database( $this->plugin_name, $this->version );
		$this->adapter = new Adapter( $this->plugin_name, $this->version );

	}

	/**
	 * Manage rendering of repeatable Gistfile editor
	 *
	 * @since     0.4.0
	 */
	public function render_editor_div() {
		if ( 'gistpen' == get_current_screen()->id ) {
			include_once( WP_GISTPEN_DIR . 'partials/editor/main.inc.php' );
			include_once( WP_GISTPEN_DIR . 'partials/editor/zip.inc.php' );
			include_once( WP_GISTPEN_DIR . 'partials/editor/file.inc.php' );
		}
	}

	/**
	 * Force the Gistpen layout to one column
	 *
	 * @since  0.4.0
	 */
	public function screen_layout_columns( $columns ) {
		$columns['gistpen'] = 1;
		return $columns;
	}
	public function screen_layout_gistpen() {
		return 1;
	}

	/**
	 * Remove unessary metaboxes from Gistpens
	 *
	 * @since  0.4.0
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'submitdiv', 'gistpen', 'side' );
		remove_meta_box( 'commentsdiv', 'gistpen', 'normal' );
		remove_meta_box( 'revisionsdiv', 'gistpen', 'normal' );
		remove_meta_box( 'authordiv', 'gistpen', 'normal' );
		remove_meta_box( 'slugdiv', 'gistpen', 'normal' );
		remove_meta_box( 'tagsdiv-post_tag', 'gistpen', 'side' );
		remove_meta_box( 'categorydiv', 'gistpen', 'side' );
		remove_meta_box( 'gistpenexcerpt', 'gistpen', 'normal' );
		remove_meta_box( 'formatdiv', 'gistpen', 'normal' );
		remove_meta_box( 'trackbacksdiv', 'gistpen', 'normal' );
		remove_meta_box( 'gistpencustom', 'gistpen', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'gistpen', 'normal' );
		remove_meta_box( 'postimagediv', 'gistpen', 'side' );
	}

	/**
	 * Adds the file column to the Gistpen edit screen
	 *
	 * @param  array $columns Array of the columns
	 * @return array          Array with new column added
	 * @since  0.4.0
	 */
	public function manage_posts_columns( $columns ) {
		return array_merge( $columns, array(
			'gistpen_files' => __( 'Files', $this->plugin_name )
		) );
	}

	/**
	 * Render the file column on the Gistpen edit screen
	 *
	 * @param  string $column_name the custom column name
	 * @param  int    $post_id     the ID of the current post
	 * @since  0.4.0
	 */
	public function manage_posts_custom_column( $column_name, $post_id ) {
		if ( 'gistpen_files' === $column_name ) {
			$zip = $this->database->query()->by_id( $post_id );

			foreach ( $zip->get_files() as $file ) {
				echo $file->get_filename();
				echo '<br>';
			}
		}
	}

	/**
	 * Reorders in reverse chron on the Gistpen edit screen
	 *
	 * @param  string   $orderby  the query's orderby statement
	 * @param  WP_Query $query    current query obj
	 * @return string          new orderby statement
	 * @since  0.4.0
	 */
	public function edit_screen_orderby( $orderby, $query ) {
		if ( is_admin() && $query->query_vars['post_type'] === 'gistpen' ) {
			global $wpdb;
			$orderby = $wpdb->posts . '.post_date DESC';
		}

		return $orderby;
	}

}
