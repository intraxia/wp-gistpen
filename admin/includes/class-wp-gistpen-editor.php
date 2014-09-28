<?php
/**
 * @package   WP_Gistpen
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
	 * Returns new Gistpen title placeholder text
	 *
	 * @param  string   $title   default placeholder text
	 * @return string            new placeholder text
	 * @since  0.4.0
	 */
	public static function new_enter_title_here( $title ){
			$screen = get_current_screen();

			if ( 'gistpen' == $screen->post_type ){
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

		if( 'gistpen' == $screen->id ) {

			self::render_theme_selector(); ?>
			<div id="wp-gistfile-wrap"></div>
			<input type="hidden" id="file_ids" name="file_ids" value=""><?php

			echo submit_button( __('Add Gistfile', WP_Gistpen::get_instance()->get_plugin_slug()), 'primary', 'add-gistfile', true );

		}
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
	public static function add_ace_editor_init_inline() {
		$screen = get_current_screen();

		if( 'gistpen' == $screen->id ):

			$zip = WP_Gistpen::get_instance()->query->get( get_the_ID() );

			foreach ($zip->files as $index => $file) {
				$files[] = $file;
			}

			$jsFiles = json_encode( $files ); ?>
			<script type="text/javascript">
				jQuery(function() {
					GistpenEditor.init(<?php echo $jsFiles; ?>);
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
		wp_enqueue_style( WP_Gistpen::get_instance()->get_plugin_slug() .'-editor-styles', WP_GISTPEN_URL . 'admin/assets/css/wp-gistpen-editor.css', array(), WP_Gistpen::VERSION );
	}

	/**
	 * Add the ACE editor scripts to the Add Gistpen screen
	 *
	 * @since     0.4.0
	 */
	public static function enqueue_editor_scripts() {
		wp_enqueue_script( WP_Gistpen::get_instance()->get_plugin_slug() . '-ace-script', WP_GISTPEN_URL . 'admin/assets/js/ace/ace.js', array(), WP_Gistpen::VERSION, false );
		wp_enqueue_script( WP_Gistpen::get_instance()->get_plugin_slug() . '-editor-script', WP_GISTPEN_URL . 'admin/assets/js/wp-gistpen-editor.min.js', array( 'jquery', WP_Gistpen::get_instance()->get_plugin_slug() . '-ace-script' ), WP_Gistpen::VERSION, false );
		$terms = get_terms( 'language', 'hide_empty=0' );
		foreach ($terms as $term) {
			$languages[$term->slug] = $term->name;
		}
		wp_localize_script( WP_Gistpen::get_instance()->get_plugin_slug() . '-editor-script', 'gistpenLanguages', $languages );
	}

	/**
	 * Force the Gistpen layout to one column
	 *
	 * @since  0.4.0
	 */
	public static function screen_layout_columns( $columns ) {
		$columns['gistpen'] = 1;
		return $columns;
	}
	public static function screen_layout_gistpen() {
		return 1;
	}

	/**
	 * Remove unessary metaboxes from Gistpens
	 *
	 * @since  0.4.0
	 */
	public static function remove_meta_boxes() {
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
	public static function gistpen_meta_box_order(){
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
