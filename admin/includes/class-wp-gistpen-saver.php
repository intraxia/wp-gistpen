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

	/**
	 * Errors codes
	 *
	 * @var string
	 * @since 0.4.0
	 */
	static $errors;

	/**
	 * save_post action hook callback
	 * to save all the files and
	 * attach them to the Gistpen
	 *
	 * @param  int    $gistpen_id  Gistpen post id
	 * @since  0.4.0
	 */
	public static function save_gistpen( $post_id ) {
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id )  ) {
			// @todo save revision children + autosave
			return;
		}

		if( ! array_key_exists('file_ids', $_POST) ) {
			return;
		}

		$file_ids = explode( ' ', trim( $_POST['file_ids'] ) );

		if ( empty( $file_ids ) ) {
			return;
		}

		$zip = WP_Gistpen::get_instance()->query->get( $post_id );

		if ( is_wp_error( $zip ) ) {
			// @todo create ourselves a blank zip
			return;
		}

		foreach ( $file_ids as $file_id ) {

			if( array_key_exists( $file_id, $zip->files ) ) {
				$file = $zip->files[$file_id];

				$file_id_w_dash = '-' . $file_id;

				$file->slug = $_POST['wp-gistpenfile-slug' . $file_id_w_dash];
				$file->code = $_POST['wp-gistpenfile-code' . $file_id_w_dash];
				$file->language->slug = $_POST['wp-gistpenfile-language' . $file_id_w_dash];

				$zip->files[$file_id] = $file;

			} else {
				// create a blank file
				$file = new stdClass;
				$file->post_type = 'gistpen';
				$file->post_parent = $post_id;
				// check if post exists
				if ( get_post_status( $file_id ) ) {
					// we'll use it if it does
					$file->ID = $file_id;
				}

				$file = new WP_Gistpen_File( new WP_Post( $file ), new WP_Gistpen_Language( new stdClass ) );

				$file_id_w_dash = '-' . $file_id;

				// and fill it with data
				$file->slug = $_POST['wp-gistpenfile-slug' . $file_id_w_dash];
				$file->code = $_POST['wp-gistpenfile-code' . $file_id_w_dash];
				$file->language->slug = $_POST['wp-gistpenfile-language' . $file_id_w_dash];

				$zip->files[] = $file;

			}

			unset($file);
		}

		$zip->update_post();

		self::$errors = '';

		remove_action( 'save_post_gistpen', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );
		foreach ( $zip->files as $file ) {
			$result = WP_Gistpen::get_instance()->query->save( $file );
			if( is_wp_error( $result ) ) {
				self::$errors .= $result->get_error_code() . ',';
			}
		}
		add_action( 'save_post_gistpen', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );

		if ( self::$errors !== '' ) {
			add_filter('redirect_post_location',array( 'WP_Gistpen_Saver', 'return_errors' ) );
		}
	}

	/**
	 * Adds the errors to the url, if any
	 * @param  string $location Current GET params
	 * @return string           Updated GET params
	 */
	public static function return_errors( $location ) {
		return add_query_arg( 'gistpen-errors', rtrim( self::$errors, "," ), $location );
	}
}
