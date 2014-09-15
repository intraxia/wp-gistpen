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

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			// @todo autosave children
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			// @todo save revision children
			return;
		}

		if( ! array_key_exists('file_ids', $_POST) ) {
			return;
		}

		$file_ids = explode( ' ', trim( $_POST['file_ids'] ) );

		$wpgp_post = WP_Gistpen::get_instance()->query->get( $post_id );

		if ( ! empty( $file_ids ) ) {
			foreach ($file_ids as $file_id) {

				if( array_key_exists($file_id, $wpgp_post->files) ) {
					$file = $wpgp_post->files[$file_id];

					$file_id_w_dash = '-' . $file_id;

					$file->slug = $_POST['wp-gistpenfile-slug' . $file_id_w_dash];
					$file->code = $_POST['wp-gistpenfile-code' . $file_id_w_dash];
					$file->language->slug = $_POST['wp-gistpenfile-language' . $file_id_w_dash];

					$wpgp_post->files[$file_id] = $file;

				} else {
					$file = new stdClass;
					$file->post_type = 'gistpen';
					$file->post_status = $_POST['post_status'];
					$file->post_parent = $post_id;
					$file->post_password = $_POST['post_password'];
					$file = new WP_Post( $file );

					$file = new WP_Gistpen_File( $file, new WP_Gistpen_Language( new stdClass ) );

					$file->language->slug = $_POST['wp-gistpenfile-language' . $file_id];
					$file->slug = $_POST['wp-gistpenfile-slug' . $file_id];
					$file->code = $_POST['wp-gistpenfile-code' . $file_id];
					$file->language->slug = $_POST['wp-gistpenfile-name' . $file_id];

					$wpgp_post->files[] = $file;

				}

				unset($file);

				$wpgp_post->update_post();

				remove_action( 'save_post_gistpen', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );
				foreach ( $wpgp_post->files as $file ) {
					$result = WP_Gistpen::get_instance()->query->save_file( $file );
					if( is_wp_error( $result ) ) {
						// @todo error checking
					}
				}
				add_action( 'save_post_gistpen', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );
			}
		}
	}
}
