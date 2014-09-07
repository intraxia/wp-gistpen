<?php
/**
 * @package   WP_Gistpen
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
	public static function save_gistpen( $gistpen_id = '' ) {
		if ( '' === $gistpen_id ) {
			remove_action( 'save_post_gistpen', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );
			$gistpen_id = wp_insert_post( array( 'post_title' => $_POST['wp-gistfile-description'], 'post_type' => 'gistpen' ) );
			add_action( 'save_post_gistpen', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );
		}

		self::$post_id = $gistpen_id;

		// Autosave, do nothing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check user permissions
		if ( ! current_user_can( 'edit_post', self::$post_id ) ) {
			return;
		}

		if( array_key_exists('file_ids', $_POST) ) {
			$file_ids = explode( ' ', trim( $_POST['file_ids'] ) );

			if ( ! empty( $file_ids ) ) {
				foreach ($file_ids as $file_id) {
					self::save_gistfile( self::set_up_args( $file_id ) );
				}
			}
		} else {
			self::save_gistfile( self::set_up_args() );
		}

		return self::$file_ids;
	}

	/**
	 * Sets up and return the save_gistfile args
	 * from the currently sent post data
	 *
	 * @param int $file_id (optional) ID to update
	 * @return array arguments to past toe save_gistfile
	 */
	public static function set_up_args( $file_id = '' ) {
		$args = array();

		if ( '' !== $file_id ) {
			$args['ID'] = $file_id;
			$file_id = '-' . $file_id;
		}

		if( array_key_exists('wp-gistpenfile-name' . $file_id, $_POST) ) {
			$args['post_title'] = str_replace(" ", "-", $_POST['wp-gistpenfile-name' . $file_id]);
			$args['post_name'] = $_POST['wp-gistpenfile-name' . $file_id];
		}

		if( array_key_exists('wp-gistpenfile-content' . $file_id, $_POST) ) {
			$args['post_content'] = $_POST['wp-gistpenfile-content' . $file_id];
		}

		if( array_key_exists('wp-gistpenfile-language' . $file_id, $_POST) ) {
			$args['tax_input']['language'] = $_POST['wp-gistpenfile-language' . $file_id];
		}

		return $args;
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
			'post_type' => 'gistpen',
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

		remove_action( 'save_post_gistpen', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );
		$result = wp_insert_post( $post, true );
		add_action( 'save_post_gistpen', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );

		if( !is_wp_error( $result ) ) {
			self::$file_ids[] = $result;
		}

		return $result;
	}
}
