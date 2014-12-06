<?php
namespace WP_Gistpen\Register\View;

use WP_Gistpen\Facade\Database;
use WP_Gistpen\Facade\Adapter;

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
		'kr' => 'KR',
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
	 * Display any errors from the save hooks in the admin dashboard
	 *
	 * @since 0.4.0
	 */
	public function add_admin_errors() {
		if ( array_key_exists( 'gistpen-errors', $_GET ) ) { ?>
			<div class="error">
				<p><?php
					_e( 'The post saved with error codes: ', $this->plugin_name );
					echo $_GET['gistpen-errors'];
				?></p>
			</div><?php
		}
	}

	/**
	 * Returns new Gistpen title placeholder text
	 *
	 * @param  string   $title   default placeholder text
	 * @return string            new placeholder text
	 * @since  0.4.0
	 */
	public function new_enter_title_here( $title ){
		$screen = get_current_screen();

		if ( 'gistpen' == $screen->post_type ){
			$title = __( 'Gistpen description...', $this->plugin_name );
		}

		return $title;
	}

	/**
	 * Manage rendering of repeatable Gistfile editor
	 *
	 * @since     0.4.0
	 */
	public function render_gistfile_editor() {

		$screen = get_current_screen();

		if ( 'gistpen' == $screen->id ) {

			$this->render_theme_selector(); ?>
			<div id="wp-gistfile-wrap"></div>
			<input type="hidden" id="file_ids" name="file_ids" value=""><?php

			echo submit_button( __( 'Add Gistfile', $this->plugin_name ), 'primary', 'add-gistfile', true );

		}
	}

	/**
	 * Render the selection box for the ACE editor theme
	 *
	 * @return string   ACE theme selection box
	 * @since  0.4.0
	 */
	public function render_theme_selector() { ?>
		<div class="_wpgp_ace_theme-wrap">
			<label for="_wpgp_ace_theme">
				<?php _e( 'Ace Editor Theme: ', $this->plugin_name ); ?>
			</label>
			<select name="_wpgp_ace_theme" id="_wpgp_ace_theme">
			<?php foreach ( $this->ace_themes as $slug => $name ) : ?>
				<?php $selected = get_user_meta( get_current_user_id(), '_wpgp_ace_theme', true ) == $slug ? 'selected' : ''; ?>
				<option value="<?php echo $slug; ?>" <?php echo $selected; ?> >
					<?php echo $name; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div><?php
	}

	/**
	 * Hooks into admin footer to initate ACE editors
	 *
	 * @return string   ACE editor init script
	 * @since 0.4.0
	 */
	public function add_ace_editor_init_inline() {
		$screen = get_current_screen();

		if ( 'gistpen' == $screen->id ):

			$zip = $this->database->query()->by_id( get_the_ID() );

			if ( is_wp_error( $zip ) ) {?>
				<script>
					console.log(<?php echo $zip->get_error_message();  ?>);
				</script>
				<?php
				return;
			}

			$files = $zip->get_files();

			$jsFiles = $this->adapter->build( 'file' )->to_json( $files ); ?>

			<script type="text/javascript">
				jQuery(function() {
					GistpenEditor.init(<?php echo $jsFiles; ?>);
				});
			</script>
		<?php endif;
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
		remove_meta_box( 'slugdiv', 'gistpen', 'normal' );
		remove_meta_box( 'formatdiv', 'gistpen', 'normal' );
		remove_meta_box( 'postcustom', 'gistpen', 'normal' );
		remove_meta_box( 'postexcerpt', 'gistpen', 'normal' );
		remove_meta_box( 'authordiv', 'gistpen', 'normal' );
	}

	/**
	 * Rearrange remaining metaboxes
	 *
	 * @return array New order for metaboxes
	 * @since 0.4.0
	 */
	public function gistpen_meta_box_order(){
		return array(
				'normal'   => join( ',', array(
					'gistfile_editor',
					'submitdiv',
					'trackbacksdiv',
					'tagsdiv-post_tag',
					'commentstatusdiv',
					'wpseo_meta',
				) ),
				'side'     => '',
				'advanced' => '',
		);
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
			$orderby = 'wp_posts.post_date DESC';
		}

		return $orderby;
	}

}
