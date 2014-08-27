<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class handles all of the AJAX responses
 *
 * @package WP_Gistpen_AJAX
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_AJAX {

	/**
	 * Embed the nonce in the head of the editor
	 *
	 * @return string    AJAX nonce
	 * @since  0.2.0
	 */
	public static function embed_nonce() {
		wp_nonce_field( 'create_gistpen_ajax', '_ajax_wp_gistpen', false );
	}

		/**
	 * Dialog for adding shortcode
	 *
	 * @return  string   HTML for shortcode dialog
	 * @since   0.2.0
	 */
	public static function insert_gistpen_dialog() {

		die( include WP_GISTPEN_DIR . 'admin/views/insert-gistpen.php' );

	}

	/**
	 * Responds to AJAX request to search Gistpens
	 *
	 * @return  string   HTML for found gistpens
	 * @since 0.2.0
	 */
	public static function search_gistpen_ajax() {
		if ( !wp_verify_nonce( $_POST['gistpen_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", WP_Gistpen::get_instance()->get_plugin_slug() ) );
		}

		$args = array(
			'post_type'      => 'gistpens',
			'post_status'    => 'publish',
			'order'          => 'DESC',
			'orderby'        => 'date',
			'posts_per_page' => 5,
		);

		if( isset( $_POST['gistpen_search_term'] ) ) {
			$args['s'] = $_POST['gistpen_search_term'];
		}

		$recent_gistpen_query = new WP_Query( $args );

		$output = '';
		if ( $recent_gistpen_query->have_posts() ) {
			while ( $recent_gistpen_query->have_posts() ) {
				$recent_gistpen_query->the_post();

				$output .= '<li>';
					$output .= '<div class="gistpen-radio"><input type="radio" name="gistpen_id" value="' . get_the_ID() . '"></div>';
					$output .= '<div class="gistpen-title">' . get_the_title() . '</div>';
				$output .= '</li>';

			}
		} else {
			$output .= '<li>';
				$output .= 'No Gistpens found.';
			$output .= '</li>';
		}

		die( $output );
	}

	/**
	 * Responds to AJAX request to create new Gistpen
	 *
	 * @return string $post_id the id of the created Gistpen
	 * @since  0.2.0
	 */
	public static function create_gistpen_ajax() {

		if ( !wp_verify_nonce( $_POST['gistpen_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", 'wp-gistpen' ) );
		}

		$args = array(
			'post_title'   => $_POST['gistpen_title'],
			'post_content' => $_POST['gistpen_content'],
			'post_type'    => 'gistpens',
			'post_status'  => 'publish',
			'tax_input'    => array(
				'language'   => $_POST['gistpen_language'],
			),
		);
		$post_id = wp_insert_post( $args, false );

		if( $post_id === 0 ) {
			die( "Failed to insert post. ");
		}

		if( $_POST['gistpen_description'] !== "" ) {
			update_post_meta( $post_id, '_wpgp_gistpen_description', $_POST['gistpen_description'] );
		}

		die( $post_id );

	}

	/**
	 * AJAX hook to save ACE editor theme
	 *
	 * @since     0.4.0
	 */
	public static function save_ace_theme() {
		if ( !wp_verify_nonce( $_POST['theme_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", 'wp-gistpen' ) );
		}

		$result = update_option( '_wpgp_ace_theme', $_POST['theme'] );
		die( $result );
	}


	/**
	 * AJAX hook to get a new ACE editor
	 *
	 * @since     0.4.0
	 */
	public static function add_gistfile_editor() {
		if ( !wp_verify_nonce( $_POST['add_editor_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", WP_Gistpen::get_instance()->get_plugin_slug() ) );
		}

		$id = WP_Gistpen_Saver::save_gistfile();

		die( $id );
	}

	/**
	 * AJAX hook to delete an ACE editor
	 *
	 * @since     0.4.0
	 */
	public static function delete_gistfile_editor() {
		if ( !wp_verify_nonce( $_POST['delete_editor_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", WP_Gistpen::get_instance()->get_plugin_slug() ) );
		}

		$result = wp_delete_post( $_POST['gistfileID'] );
		if( $result !== false ) {
			$result = true;
		}
		die( $result );
	}
}
