<?php
/**
 * @package   WP_Gistpen_editor
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class manipulates the saving of parent Gistpen
 * and all child Gistpens.
 *
 * @package WP_Gistpen_Saver
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Saver {

	public static $post_id;

	public static $file_ids = array();

	/**
	 * save_post action hook callback
	 * to save all the files and
	 * attach them to the Gistpen
	 *
	 * @param  int    $gistpen_id  Gistpen post id
	 * @since  0.4.0
	 */
	public static function save_gistpen( $gistpen_id ) {
		self::$post_id = $gistpen_id;

		// Autosave, do nothing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check user permissions
		if ( ! current_user_can( 'edit_post', self::$post_id ) ) {
			return;
		}

		if( !array_key_exists('file_ids', $_POST) ) {
			self::save_gistfile();
		} else {

			$file_ids = explode( ' ', trim( $_POST['file_ids'] ) );

			foreach ($file_ids as $file_id) {
				$args = array();

				$args['ID'] = $file_id;
				$args['post_name'] = str_replace(" ", "-", $_POST['wp-gistpenfile-name-' . $file_id]);
				$args['post_title'] = $_POST['wp-gistpenfile-name-' . $file_id];
				$args['post_content'] = $_POST['wp-gistpenfile-content-' . $file_id];
				$args['tax_input']['language'] = $_POST['wp-gistpenfile-language-' . $file_id];

				self::save_gistfile( $args );

			}
		}
	}

	/**
	 * Saves the current Gistfile
	 *
	 * @param  array  $args  Gistfile post args
	 * @since  0.4.0
	 */
	public static function save_gistfile( $args = array() ) {
		// @todo do uniqueness check on $args['name']
		$post = array(
			'post_content' => '',
			'post_name' => '',
			'post_title' => '',
			'post_type' => 'gistpens',
			'post_status' => 'inherit',
			'post_password' => '',
			'post_parent' => self::$post_id,
			'tax_input' => array(
				'language' => ''
			)
		);

		foreach ($args as $key => $value) {
			$post[$key] = $value;
		}

		remove_action( 'save_post_gistpens', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );
		$result = wp_insert_post( $post, true );
		add_action( 'save_post_gistpens', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );

		if( !is_wp_error( $result ) ) {
			self::$file_ids[] = $result;
		} else {
			// do something on failure
		}
		return $result;
	}
}
