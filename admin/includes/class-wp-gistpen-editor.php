<?php
/**
 * @package   WP_Gistpen_editor
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class manipulates the
 * editor for the Gistpen edit screen.
 *
 * @package WP_Gistpen_Editor
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Editor {

	/**
	 * All the Ace themes for select box
	 *
	 * @var array
	 * @since    0.4.0
	 */
	public static $ace_themes = array(
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
		'twilight' => 'Twilight'
	);

	/**
	 * Current Gistpen post_id
	 *
	 * @var int
	 * @since 0.4.0
	 */
	public static $gistpen_id;

	/**
	 * Array of all the files
	 * attached to current Gistpen.
	 *
	 * @var array
	 * @since 0.4.0
	 */
	public static $files;

	/**
	 * Returns new Gistpen title placeholder text
	 *
	 * @param  string   $title   default placeholder text
	 * @return string            new placeholder text
	 * @since  0.4.0
	 */
	public static function new_enter_title_here( $title ){
			$screen = get_current_screen();

			if ( 'gistpens' == $screen->post_type ){
				$title = __( 'Gistpen description...', WP_Gistpen::get_instance()->get_plugin_slug() );
			}

			return $title;
		}

	/**
	 * Manage rendering of repeatable Gistfile editor
	 *
	 * @since     0.4.0
	 */
	public static function render_gistfile_editor() {

		$screen = get_current_screen();

		if( 'gistpens' == $screen->id ) {

			self::set_up_gistpen_and_files();

			self::render_theme_selector(); ?>
			<div id="wp-gistfile-editor-wrap"></div><?php
			self::render_hidden_field_file_ids();

			echo submit_button( __('Add Gistfile', WP_Gistpen::get_instance()->get_plugin_slug()), 'primary', 'add-gistfile', true );

		}
	}

	/**
	 * Sets up the Gistpen data for rendering the editor
	 *
	 * @since     0.4.0
	 */
	public static function set_up_gistpen_and_files() {
		global $post;

		self::$gistpen_id = $post->ID;

		self::$files = get_posts( array(
			'posts_per_page'   => -1,
			'post_type'        => 'gistpens',
			'post_parent'      => self::$gistpen_id,
			'post_status'      => 'any',
		) );

	}

	/**
	 * Render the selection box for the ACE editor theme
	 *
	 * @return string   ACE theme selection box
	 * @since  0.4.0
	 */
	public static function render_theme_selector() { ?>
		<div class="_wpgp_ace_theme-wrap">
			<label for="_wpgp_ace_theme">
				<?php _e( 'Ace Editor Theme: ', WP_Gistpen::get_instance()->get_plugin_slug() ); ?>
			</label>
			<select name="_wpgp_ace_theme" id="_wpgp_ace_theme">
			<?php foreach (self::$ace_themes as $slug => $name): ?>
				<?php $selected = get_option( '_wpgp_ace_theme' ) == $slug ? 'selected' : ''; ?>
				<option value="<?php echo $slug; ?>" <?php echo $selected; ?> >
					<?php echo $name; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div><?php
	}

	/**
	 * Renders the hidden field containing all the Gistfile ids
	 *
	 * @return string   input[type="hidden"].gistfile_ids
	 * @since 0.4.0
	 */
	public static function render_hidden_field_file_ids() {
		if( ! empty(self::$files) ) {
			foreach (self::$files as $file ) {
				$ids[] = $file->ID;
			}
		} else {
			$ids = WP_Gistpen_Saver::$file_ids;
			foreach ( $ids as $id ) {
				self::$files[] = get_post( $id );
			}
		}
		$ids_string = implode( ' ', $ids); ?>
		<input type="hidden" id="file_ids" name="file_ids" value="<?php echo $ids_string; ?>"><?php
	}

	/**
	 * Hooks into admin footer to initate ACE editors
	 *
	 * @return string   ACE editor init script
	 * @since 0.4.0
	 */
	public static function add_ace_editor_init_inline() {
		$screen = get_current_screen();

		if( 'gistpens' == $screen->id ): ?>
			<script type="text/javascript">
				jQuery(function() {
					<?php foreach (self::$files as $file) : ?>
						window['gfe<?php echo $file->ID; ?>'] = new FileEditor("<?php echo $file->ID; ?>", "<?php echo $file->post_name; ?>", "<?php echo $file->post_content; ?>");
					<?php endforeach;?>
				});
			</script><?php
		endif;
	}

	/**
	 * Add the ACE editor styles to the Add Gistpen screen
	 *
	 * @since     0.4.0
	 */
	public static function enqueue_editor_styles() {

		$screen = get_current_screen();

		if ('gistpens' == $screen->id ) {
			wp_enqueue_style( WP_Gistpen::get_instance()->get_plugin_slug() .'-editor-styles', WP_GISTPEN_URL . 'admin/assets/css/wp-gistpen-editor.css', array(), WP_Gistpen::VERSION );
		}
	}

	/**
	 * Add the ACE editor scripts to the Add Gistpen screen
	 *
	 * @since     0.4.0
	 */
	public static function enqueue_editor_scripts() {

		$screen = get_current_screen();

		if ('gistpens' == $screen->id ) {
			wp_enqueue_script( WP_Gistpen::get_instance()->get_plugin_slug() . '-ace-script', WP_GISTPEN_URL . 'admin/assets/js/ace/ace.js', array(), WP_Gistpen::VERSION, false );
			wp_enqueue_script( WP_Gistpen::get_instance()->get_plugin_slug() . '-editor-script', WP_GISTPEN_URL . 'admin/assets/js/wp-gistpen-editor.min.js', array( 'jquery', WP_Gistpen::get_instance()->get_plugin_slug() . '-ace-script' ), WP_Gistpen::VERSION, false );
			$terms = get_terms( 'language', 'hide_empty=0' );
			foreach ($terms as $term) {
				$languages[$term->slug] = $term->name;
			}
			wp_localize_script( WP_Gistpen::get_instance()->get_plugin_slug() . '-editor-script', 'gistpenLanguages', $languages );
		}
	}

	/**
	 * Force the Gistpen layout to one column
	 *
	 * @since  0.4.0
	 */
	public static function screen_layout_columns( $columns ) {
		$columns['gistpens'] = 1;
		return $columns;
	}
	public static function screen_layout_gistpens() {
		return 1;
	}

	/**
	 * Remove unessary metaboxes from Gistpens
	 *
	 * @since  0.4.0
	 */
	public static function remove_meta_boxes() {
		remove_meta_box( 'slugdiv', 'gistpens', 'normal' );
		remove_meta_box( 'formatdiv', 'gistpens', 'normal' );
		remove_meta_box( 'postcustom', 'gistpens', 'normal' );
		remove_meta_box( 'postexcerpt', 'gistpens', 'normal' );
		remove_meta_box( 'authordiv', 'gistpens', 'normal' );
	}

	/**
	 * Rearrange remaining metaboxes
	 *
	 * @return array New order for metaboxes
	 * @since 0.4.0
	 */
	public static function gistpens_meta_box_order(){
		return array(
				'normal'   => join( ",", array(
					'gistfile_editor',
					'submitdiv',
					'trackbacksdiv',
					'tagsdiv-post_tag',
					'commentstatusdiv',
					'wpseo_meta'
				) ),
				'side'     => '',
				'advanced' => '',
		);
	}

}
