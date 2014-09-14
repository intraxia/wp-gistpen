<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class saves and gets Gistpens from the database
 *
 * @package WP_Gistpen_Query
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Query {

	/**
	 * Creates a WP_Gistpen object from a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @param  string $language           language slug
	 * @return WP_Gistpen_Post|File       WP_Gistpen object
	 */
	public function create( WP_Post $post, $language = '' ) {

		if ( 'gistpen' !== $post->post_type ) {
			return new WP_Error( 'not_gistpen', __( "WP_Gistpen_Query::save can only create gistpens", WP_Gistpen::get_instance()->get_plugin_slug() ) );
		}

		if ( 0 === $post->post_parent ) {
			return new WP_Gistpen_Post( $post );
		} else {

			if( '' === $language ) {
				return new WP_Error( 'no_language_set', __( "Post with a parent needs a language, no language set", WP_Gistpen::get_instance()->get_plugin_slug() ) );
			}

			$term = get_terms( 'language', array( 'slug' => $language ) );

			if ( is_wp_error( $term ) ) {
				return $term;
			}
			if( empty( $term ) ) {
				return new WP_Error( 'nonexistent_language', __( "Language ${language} does not exist", WP_Gistpen::get_instance()->get_plugin_slug() ) );
			}

			$language = new WP_Gistpen_Language( array_pop( $term ) );

			return new WP_Gistpen_File( $post, $language );
		}
	}

	/**
	 * Retrieves the correct WP_Gistpen object from the database
	 *
	 * @param  WP_Post|int $post Accepts a WP_Post object or a post ID
	 * @return WP_Gistpen_Post|File       WP_Gistpen object
	 * @since 0.4.0
	 */
	public function get( $post ) {

		if( ! is_object( $post ) ) {

			if ( is_numeric( $post ) ) {
				$post = get_post( $post );

				if ( is_wp_error( $post ) ) {
					$error = $post;
					return $error;
				}
			} else {
				return new WP_Error( 'wrong_construct_args', __( "WP_Gistpen_Query::get() needs an ID or object", WP_Gistpen::get_instance()->get_plugin_slug() ) );
			}

		}

		if ( $post->post_type !== 'gistpen' ) {
			return new WP_Error( 'wrong_post_type', __( "WP_Gistpen_Query::get() didn't get a Gistpen", WP_Gistpen::get_instance()->get_plugin_slug() ) );
		}

		if( 0 !== $post->post_parent ) {
			return $this->get_file( $post );
		} else {
			return $this->get_gistpen( $post );
		}

	}

	/**
	 * Retrieves the WP_Gistpen_File from the WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return WP_Gistpen_File
	 * @since 0.4.0
	 */
	protected function get_file( $post ) {
		$language = new WP_Gistpen_Language( $this->get_language( $post ) );
		if ( is_wp_error( $language ) ) {
			// @todo error out
			return null;
		}

		return new WP_Gistpen_File( $post, $language );
	}

	/**
	 * Retrieves the WP_Gistpen_Language for a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return WP_Gistpen_Language|WP_Error       language object or Error
	 * @since 0.4.0
	 */
	protected function get_language( $post ) {
		$terms = get_the_terms( $post->ID, 'language' );

		if( $terms ) {
			$language = array_pop( $terms );
		} else {
			$language = new WP_Error( 'no_language', __( 'The file has no language', WP_Gistpen::get_instance()->get_plugin_slug() ) );
		}

		return $language;
	}

	/**
	 * Retrieves the WP_Gistpen_Post for a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return WP_Gistpen_Language|WP_Error       language object or Error
	 * @since 0.4.0
	 */
	protected function get_gistpen( $post ) {
		$files = $this->get_files( $post );

		return new WP_Gistpen_Post( $post, $files );
	}

	/**
	 * Retrieves the all the WP_Gistpen_File's for a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return array|WP_Error       array of WP_Gistpen_Files or Error
	 * @since 0.4.0
	 */
	protected function get_files( $post ) {
		$files_obj = get_children( array(
			'post_type' => 'gistpen',
			'post_parent' => $post->ID
		) );

		foreach ( $files_obj as $index => $file ) {
			$files[] = $this->get_file( $file );
		}

		return $files;
	}

}
