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
	 * Instance of this class.
	 *
	 * @var      object
	 * @since    0.2.0
	 */
	protected static $instance = null;

	/**
	 * All the Ace themes for select box
	 *
	 * @var array
	 * @since    0.4.0
	 */
	protected $ace_themes = array(
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
	 * Info about current gistfile
	 *
	 * @var strings
	 * @since 0.4.0
	 */
	protected $gistfile_name = '';
	protected $gistfile_content = '';
	protected $gistfile_language = '';

	/**
	 * Initialize the editor enhancements by loading the metaboxes
	 * and the new TinyMCE button.
	 *
	 * @since     0.2.0
	 */
	private function __construct() {

		// Call $plugin_slug from public plugin class.
		$plugin = WP_Gistpen::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Edit the placeholder text in the Gistpen title box
		add_filter( 'enter_title_here', array( $this, 'change_gistpen_title_box' ) );

		// Hook in repeatable Gistfile editor
		add_action( 'edit_form_after_title', array( $this, 'render_gistfile_editor' ) );

		// Save the Gistfiles and attach to Gistpen
		add_action( 'save_post', array( $this, 'save_gistpen' ) );

		// Load editor style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_editor_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );

		// Add AJAX hooks for Ace theme
		add_action( 'wp_ajax_gistpen_save_ace_theme', array( $this, 'save_ace_theme' ) );
		add_action( 'wp_ajax_gistpen_get_ace_theme', array( $this, 'get_ace_theme' ) );

		// Rearrange Gistpen layout
		add_filter( 'screen_layout_columns', array( $this, 'screen_layout_columns' ) );
		add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
		add_filter( 'get_user_option_screen_layout_gistpens', array( $this, 'screen_layout_gistpens' ) );
		add_filter( 'get_user_option_meta-box-order_gistpens', array( $this, 'gistpens_meta_box_order') );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @return    object    A single instance of this class.
	 * @since     0.2.0
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Changes the placeholder text for Gistpen titles
	 *
	 * @param  string $title Current placeholder text
	 * @return string        New placeholder text
	 * @since 0.4.0
	 */
	function change_gistpen_title_box( $title ){

		$screen = get_current_screen();

		if ( 'gistpens' == $screen->post_type ){
			$title = __( 'Gistpen description...', $this->plugin_slug );
		}

		return $title;
	}

	/**
	 * Manage rendering of repeatable Gistfile editor
	 *
	 * @since     0.4.0
	 */
	public function render_gistfile_editor() {
		global $post;

		$screen = get_current_screen();


		if( 'gistpens' == $screen->id ) {

			wp_nonce_field( 'save_gistfile', 'save_gistfile_nonce' );

			$gistfile_ids = get_post_meta( $post->ID, 'gistfile_ids', true );

			if( !empty( $gistfile_ids ) ) {

				foreach( $gistfile_ids as $gistfile_id ) {

					$gistfile = get_post( $gistfile_id );

					$this->gistfile_name = $gistfile->post_name;
					$this->gistfile_content = $gistfile->post_content;

					$terms = get_the_terms( $gistfile_id, 'language' );
					$lang = array_pop($terms);
					$this->gistfile_language = $lang->slug;

					$this->render_editor();
				}

			} else {
				$this->render_editor();
			}

			// submit_button( __('Add Gistfile', $this->plugin_slug), 'primary', 'add-gistfile', false );
			echo '<p></p>';
		}
	}

	/**
	 * Renders an individual Gistfile editor
	 *
	 * @return string HTML for individual Gistfile editor
	 * @since  0.4.0
	 */
	public function render_editor() {?>
		<div class="wp-core-ui wp-editor-wrap" id="wp-gistfile-content-new-wrap">
			<div class="wp-editor-tools hide-if-no-js" id="wp-gistfile-content-new-editor-tools">

				<div class="wp-media-buttons" id="wp-gistfile-content-new-media-buttons">
					<?php $this->render_filename_input(); ?>
					<?php $this->render_language_selector(); ?>
					<?php $this->render_theme_selector(); ?>
				</div>

				<div class="wp-editor-tabs">
					<a class="hide-if-no-js wp-switch-editor switch-html" id="content-html">Text</a>
					<a class="hide-if-no-js wp-switch-editor switch-ace" id="content-ace">Ace</a>
				</div>
			</div>

			<div class="wp-editor-container" id="wp-gistfile-content-new-editor-container">
				<textarea class="wp-editor-area" cols="40" id="gistfile-content-new" name="gistfile-content-new" rows="20"><?php echo $this->gistfile_content; ?></textarea>
				<div id="ace-editor"></div>
			</div>

		</div><?php
	}

	/**
	 * Render the input for the Gistfilename
	 *
	 * @return string  Gistfilename input box
	 * @since  0.4.0
	 */
	public function render_filename_input() {?>
		<label for="gistfile-name-new" style="display: none;">Gistfilename</label>
		<input type="text" name="gistfile-name-new" size="20" id="gistfile-name-new" value="<?php echo $this->gistfile_name; ?>" placeholder="Gistfilename" autocomplete="off" />
		<?php
	}

	/**
	 * Render the selection box for the Gistfile language
	 *
	 * @return string   Gistfile selection box
	 * @since  0.4.0
	 */
	public function render_language_selector() {
		$terms = get_terms( 'language', 'hide_empty=0' );

		echo '<select name="gistfile-language-new" id="gistfile-language-new">';
		foreach ($terms as $term) {
			$selected = $this->gistfile_language == $term->slug ? 'selected' : '';
			echo '<option value="' . $term->slug .'" ' . $selected . '>' . $term->name . '</option>';
		}
		echo '</select>';
	}

	/**
	 * Render the selection box for the ACE editor theme
	 *
	 * @return string   ACE theme selection box
	 * @since  0.4.0
	 */
	public function render_theme_selector() {
			echo '<select name="_wpgp_ace_theme" id="_wpgp_ace_theme">';
			foreach ($this->ace_themes as $slug => $name) {
				$selected = get_option( '_wpgp_ace_theme' ) == $slug ? 'selected' : '';
				echo '<option value="' . $slug . '" ' . $selected . '>' . $name . '</option>';
			}
			echo '</select>';
	}

	/**
	 * Action hook callback to save all the Gistfiles and
	 * attach them to the Gistpen
	 *
	 * @param  int    $gistpen_id  Gistpen post id
	 * @since  0.4.0
	 */
	public function save_gistpen( $gistpen_id ) {

		if( !isset( $_POST['gistfile-content-new'] ) ||
		    !isset( $_POST['gistfile-language-new'] ) ) {
			return;
		}

		// @todo do uniqueness check
		$postname = $_POST['gistfile-name-new'];

		$post = array(
			'post_content' => $_POST['gistfile-content-new'],
			'post_name' => $postname,
			'post_title' => $postname,
			'post_type' => 'gistfile',
			'post_status' => '',
			'post_password' => '',
			'tax_input' => array(
				'language' => $_POST['gistfile-language-new']
			)
		);

		remove_action( 'save_post', array( $this, 'save_gistpen' ) );
		$result = wp_insert_post( $post, true );
		add_action( 'save_post', array( $this, 'save_gistpen' ) );

		if( !is_wp_error( $result ) ) {
			$gistfile_ids[] = $result;
		} else {
			// do something on failure
		}

		update_post_meta( $gistpen_id, 'gistfile_ids', $gistfile_ids );
	}

	/**
	 * Add the ACE editor styles to the Add Gistpen screen
	 *
	 * @since     0.4.0
	 */
	public function enqueue_editor_styles() {

		$screen = get_current_screen();

		if ('gistpens' == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-editor-styles', WP_GISTPEN_URL . 'admin/assets/css/wp-gistpen-editor.css', array(), WP_Gistpen::VERSION );
		}
	}

	/**
	 * Add the ACE editor scripts to the Add Gistpen screen
	 *
	 * @since     0.4.0
	 */
	public function enqueue_editor_scripts() {

		$screen = get_current_screen();

		if ('gistpens' == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-ace-script', WP_GISTPEN_URL . 'admin/assets/js/ace/ace.js', array(), WP_Gistpen::VERSION, true );
			wp_enqueue_script( $this->plugin_slug . '-editor-script', WP_GISTPEN_URL . 'admin/assets/js/wp-gistpen-editor.min.js', array( 'jquery', $this->plugin_slug . '-ace-script' ), WP_Gistpen::VERSION, true );
		}
	}

	/**
	 * AJAX hook to save ACE editor theme
	 *
	 * @since     0.4.0
	 */
	public function save_ace_theme() {
		if ( !wp_verify_nonce( $_POST['theme_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", 'wp-gistpen' ) );
		}

		$result = update_option( '_wpgp_ace_theme', $_POST['theme'] );
		die( $result );
	}

	/**
	 * Force the Gistpen layout to one column
	 *
	 * @since  0.4.0
	 */
	public function screen_layout_columns( $columns ) {
		$columns['gistpens'] = 1;
		return $columns;
	}
	public function screen_layout_gistpens() {
		return 1;
	}

	/**
	 * Remove unessary metaboxes from Gistpens
	 *
	 * @since  0.4.0
	 */
	public function remove_meta_boxes() {
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
	public function gistpens_meta_box_order(){
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
