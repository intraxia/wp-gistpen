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
	protected $gistfile_id;
	protected $gistfile_name = '';
	protected $gistfile_content = '';
	protected $gistfile_language = '';

	/**
	 * Array of all the Gistfiles
	 * attached to current Gistpen.
	 *
	 * @var array
	 * @since 0.4.0
	 */
	protected $gistfile_ids = array();

	/**
	 * Current Gistpen post_id
	 *
	 * @var int
	 * @since 0.4.0
	 */
	protected $gistpen_id;

	/**
	 * Initialize the editor enhancements by loading the metaboxes
	 * and the new TinyMCE button.
	 *
	 * @since     0.2.0
	 */
	private function __construct() {

		// Edit the placeholder text in the Gistpen title box
		add_filter( 'enter_title_here', array( $this, 'change_gistpen_title_box' ) );

		// Hook in repeatable Gistfile editor
		add_action( 'edit_form_after_title', array( $this, 'render_gistfile_editor' ) );

		// Init all the rendered editors
		add_action( 'admin_print_footer_scripts', array( $this, 'add_ace_editor_init_inline' ), 99 );

		// Save the Gistfiles and attach to Gistpen
		add_action( 'save_post', array( $this, 'save_gistpen' ) );

		// Load editor style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_editor_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );

		// Add AJAX hooks for Ace theme
		add_action( 'wp_ajax_gistpen_save_ace_theme', array( 'WP_Gistpen_AJAX', 'save_ace_theme' ) );

		// Add AJAX hooks to add and delete Gistfile editors
		add_action( 'wp_ajax_add_gistfile_editor', array( 'WP_Gistpen_AJAX', 'add_gistfile_editor' ) );
		add_action( 'wp_ajax_delete_gistfile_editor', array( 'WP_Gistpen_AJAX', 'delete_gistfile_editor' ) );

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
			$title = __( 'Gistpen description...', WP_Gistpen::get_instance()->get_plugin_slug() );
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
			$this->gistpen_id = $post->ID;

			wp_nonce_field( 'save_gistfile', 'save_gistfile_nonce' );
			$this->render_theme_selector();

			$this->gistfile_ids = get_post_meta( $this->gistpen_id, 'gistfile_ids', true );

			if( !empty( $this->gistfile_ids ) ) {

				foreach( $this->gistfile_ids as $gistfile_id ) {
					$this->gistfile_id = $gistfile_id;

					$gistfile = get_post( $this->gistfile_id );

					$this->gistfile_name = $gistfile->post_name;
					$this->gistfile_content = $gistfile->post_content;

					$terms = get_the_terms( $this->gistfile_id, 'language' );
					$lang = array_pop($terms);
					$this->gistfile_language = $lang->slug;

					$this->render_editor();

				}

			} else {

				$this->save_gistfile();
				$this->render_editor();

			}

			$this->render_hidden_field_gistfile_ids();

			echo submit_button( __('Add Gistfile', WP_Gistpen::get_instance()->get_plugin_slug()), 'primary', 'add-gistfile', true );

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
				<?php _e( 'Ace Editor Theme: ', WP_Gistpen::get_instance()->get_plugin_slug() ); ?>
			</label>
			<select name="_wpgp_ace_theme" id="_wpgp_ace_theme">
			<?php foreach ($this->ace_themes as $slug => $name): ?>
				<?php $selected = get_option( '_wpgp_ace_theme' ) == $slug ? 'selected' : ''; ?>
				<option value="<?php echo $slug; ?>" <?php echo $selected; ?> >
					<?php echo $name; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div><?php
	}

	/**
	 * Renders an individual Gistfile editor
	 *
	 * @return string HTML for individual Gistfile editor
	 * @since  0.4.0
	 */
	public function render_editor() {?>
		<div class="wp-core-ui wp-editor-wrap wp-gistfile-content-wrap" id="wp-gistfile-content-<?php echo $this->gistfile_id; ?>-wrap">
			<div class="wp-editor-tools hide-if-no-js" id="wp-gistfile-content-<?php echo $this->gistfile_id; ?>-editor-tools">

				<div class="wp-media-buttons" id="wp-gistfile-content-<?php echo $this->gistfile_id; ?>-media-buttons">
					<?php $this->render_filename_input(); ?>
					<?php $this->render_language_selector(); ?>
					<?php $this->render_delete_button(); ?>
				</div>

				<div class="wp-editor-tabs" id="wp-editor-tabs-<?php echo $this->gistfile_id; ?>">
					<a class="hide-if-no-js wp-switch-editor switch-html" id="content-html-<?php echo $this->gistfile_id; ?>">Text</a>
					<a class="hide-if-no-js wp-switch-editor switch-ace" id="content-ace-<?php echo $this->gistfile_id; ?>">Ace</a>
				</div>

			</div>

			<div class="wp-editor-container" id="wp-gistfile-content-<?php echo $this->gistfile_id; ?>-editor-container">
				<textarea class="wp-editor-area" cols="40" id="gistfile-content-<?php echo $this->gistfile_id; ?>" name="gistfile-content-<?php echo $this->gistfile_id; ?>" rows="20"><?php echo $this->gistfile_content; ?></textarea>
				<div class="ace-editor" id="ace-editor-<?php echo $this->gistfile_id; ?>"></div>
			</div>

			<input type="hidden" name="gistfile-id" id="gistfile-id" value="<?php echo $this->gistfile_id; ?>">

		</div><?php
	}

	/**
	 * Render the input for the Gistfilename
	 *
	 * @return string  Gistfilename input box
	 * @since  0.4.0
	 */
	public function render_filename_input() {?>
		<label for="gistfile-name-<?php echo $this->gistfile_id; ?>" style="display: none;">Gistfilename</label>
		<input type="text" name="gistfile-name-<?php echo $this->gistfile_id; ?>" size="20" class="gistfile-name" id="gistfile-name-<?php echo $this->gistfile_id; ?>" value="<?php echo $this->gistfile_name; ?>" placeholder="Gistfilename (no ext)" autocomplete="off" /><?php
	}

	/**
	 * Render the selection box for the Gistfile language
	 *
	 * @return string   Gistfile selection box
	 * @since  0.4.0
	 */
	public function render_language_selector() {
		$terms = get_terms( 'language', 'hide_empty=0' ); ?>

		<select name="gistfile-language-<?php echo $this->gistfile_id; ?>" id="gistfile-language-<?php echo $this->gistfile_id; ?>" class="gistfile-language">
		<?php foreach ($terms as $term): ?>
			<?php $selected = $this->gistfile_language == $term->slug ? 'selected' : ''; ?>
			<option value="<?php echo $term->slug; ?>" <?php echo $selected; ?> >
				<?php echo $term->name; ?>
			</option>
		<?php endforeach; ?>
		</select><?php
	}

	/**
	 * Render the delete button for the Gistfile
	 *
	 * @return string   Gistfile delete button
	 * @since 0.4.0
	 */
	public function render_delete_button() {
		submit_button( __('Delete This Gistfile', WP_Gistpen::get_instance()->get_plugin_slug()), 'delete', 'delete-gistfile-' . $this->gistfile_id, false );
	}

	/**
	 * Renders the hidden field containing all the Gistfile ids
	 *
	 * @return string   input[type="hidden"].gistfile_ids
	 * @since 0.4.0
	 */
	public function render_hidden_field_gistfile_ids() {
		$gistfile_ids_string = implode( ' ', $this->gistfile_ids); ?>
		<input type="hidden" id="gistfile_ids" name="gistfile_ids" value="<?php echo $gistfile_ids_string; ?>"><?php
	}

	/**
	 * Hooks into admin footer to initate ACE editors
	 *
	 * @return string   ACE editor init script
	 * @since 0.4.0
	 */
	public function add_ace_editor_init_inline() {?>
		<script type="text/javascript">
			jQuery(function() {
				<?php foreach ($this->gistfile_ids as $gistfile_id) : ?>
					window['gfe<?php echo $gistfile_id; ?>'] = new GistfileEditor("<?php echo $gistfile_id; ?>");
				<?php endforeach;?>
			});
		</script><?php
	}

	/**
	 * save_post action hook callback
	 * to save all the Gistfiles and
	 * attach them to the Gistpen
	 *
	 * @param  int    $gistpen_id  Gistpen post id
	 * @since  0.4.0
	 */
	public function save_gistpen( $gistpen_id ) {

		// @todo checks for user caps

		$gistfile_ids = explode( ' ', $_POST['gistfile_ids'] );

		foreach ($gistfile_ids as $gistfile_id) {

			$args = array();

			$args['ID'] = $gistfile_id;
			$args['post_name'] = $_POST['gistfile-name-' . $gistfile_id];
			$args['post_title'] = $_POST['gistfile-name-' . $gistfile_id];
			$args['post_content'] = $_POST['gistfile-content-' . $gistfile_id];
			$args['tax_input']['language'] = $_POST['gistfile-language-' . $gistfile_id];
			$args['post_status'] = $_POST['post_status'];

			$this->save_gistfile( $args );

		}
		$this->gistpen_id = $_POST['post_ID'];
		$this->attach_gistfile_ids_to_gistpen();
	}

	/**
	 * Saves the current Gistfile
	 *
	 * @param  array  $args  Gistfile post args
	 * @since  0.4.0
	 */
	public function save_gistfile( $args = array() ) {
		// @todo do uniqueness check on $args['name']
		$post = array(
			'post_content' => '',
			'post_name' => 'new-file',
			'post_title' => 'new-file',
			'post_type' => 'gistfile',
			'post_status' => 'auto-draft',
			'post_password' => '',
			'tax_input' => array(
				'language' => ''
			)
		);

		foreach ($args as $key => $value) {
			$post[$key] = $value;
		}

		remove_action( 'save_post', array( $this, 'save_gistpen' ) );
		$result = wp_insert_post( $post, true );
		add_action( 'save_post', array( $this, 'save_gistpen' ) );

		if( !is_wp_error( $result ) ) {

			$this->gistfile_id = $result;
			if( $post['post_name'] !== 'new-file') {
				$this->gistfile_name = $post['post_name'];
			}
			$this->gistfile_content = $post['post_content'];
			$this->gistfile_language = $post['tax_input']['language'];

			$this->gistfile_ids[] = $this->gistfile_id;

		} else {
			// do something on failure
		}

	}

	/**
	 * Updates the post_meta of the current Gistpen
	 * to attach all the current Gistfile_ids
	 *
	 * @since  0.4.0
	 */
	public function attach_gistfile_ids_to_gistpen() {
		update_post_meta( $this->gistpen_id, 'gistfile_ids', $this->gistfile_ids );
	}

	/**
	 * Add the ACE editor styles to the Add Gistpen screen
	 *
	 * @since     0.4.0
	 */
	public function enqueue_editor_styles() {

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
	public function enqueue_editor_scripts() {

		$screen = get_current_screen();

		if ('gistpens' == $screen->id ) {
			wp_enqueue_script( WP_Gistpen::get_instance()->get_plugin_slug() . '-ace-script', WP_GISTPEN_URL . 'admin/assets/js/ace/ace.js', array(), WP_Gistpen::VERSION, false );
			wp_enqueue_script( WP_Gistpen::get_instance()->get_plugin_slug() . '-editor-script', WP_GISTPEN_URL . 'admin/assets/js/wp-gistpen-editor.min.js', array( 'jquery', WP_Gistpen::get_instance()->get_plugin_slug() . '-ace-script' ), WP_Gistpen::VERSION, false );
		}
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
