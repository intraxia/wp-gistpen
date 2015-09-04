<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Facade\Adapter;

/**
 * This class registers all of the settings page views
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Editor {

	/**
	 * Action hooks for the Editor service.
	 *
	 * @var array
	 */
	public $actions = array(
		array(
			'hook' => 'edit_form_after_title',
			'method' => 'render_editor_div',
		),
		array(
			'hook' => 'add_meta_boxes',
			'method' => 'remove_meta_boxes',
		),
		array(
			'hook' => 'manage_gistpen_posts_custom_column',
			'method' => 'manage_posts_custom_column',
		),
	);

	/**
	 * Filter hooks for the Editor service.
	 *
	 * @var array
	 */
	public $filters = array(
		array(
			'hook' => 'screen_layout_columns',
			'method' => 'screen_layout_columns',
		),
		array(
			'hook' => 'get_user_option_screen_layout_gistpen',
			'method' => 'screen_layout_gistpen',
		),
		array(
			'hook' => 'manage_gistpen_posts_columns',
			'method' => 'manage_posts_columns',
		),
		array(
			'hook' => 'posts_orderby',
			'method' => 'edit_screen_orderby',
			'args' => 2,
		),
	);

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
	 * Plugin path
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Database Facade object
	 *
	 * @var Database
	 * @since 0.5.0
	 */
	protected $database;

	/**
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	protected $adapter;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $path ) {
		$this->path = $path;
		$this->database = new Database();
		$this->adapter = new Adapter();
	}

	/**
	 * Manage rendering of repeatable Gistfile editor
	 *
	 * @since     0.4.0
	 */
	public function render_editor_div() {
		if ( 'gistpen' === get_current_screen()->id ) {
			include_once( $this->path . 'partials/editor/main.inc.php' );
			include_once( $this->path . 'partials/editor/zip.inc.php' );
			include_once( $this->path . 'partials/editor/file.inc.php' );
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
			'gistpen_files' => __( 'Files', 'wp-gistpen' )
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
