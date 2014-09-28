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
	 * Search for recent Files
	 *
	 * @param  int|null $search Search term, or null for recent 5
	 * @return array         search results
	 * @since 0.4.0
	 */
	public function search( $search, $number = 5 ) {
		$args = array(
			'post_type'      => 'gistpen',
			'order'          => 'DESC',
			'orderby'        => 'date',
			'numberposts'    => $number,
			'post_parent'    => array( 'publish', 'pending', 'draft', 'future', 'private' )
		);

		if( $search !== null ) {
			$args['s'] = $search;
		}

		$result = get_posts( $args );

		if ( empty( $result ) ) {
			return new WP_Error('no_results', __("Search returned no results") );
		}

		foreach ( $result as $gistpen ) {
			if ( 0 === $gistpen->post_parent ) {
				$results[] = $this->create( $gistpen );
			} else {
				$term = $this->get_language_term_by_post( $gistpen );

				if ( is_wp_error( $term ) ) {
					$term = new stdClass;
					$term->slug = 'bash';
				}

				$language = $term->slug;

				$results[] = $this->create( $gistpen, $language );
			}
		}

		return $results;
	}

	/**
	 * Creates a WP_Gistpen object from a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @param  string $language           language slug
	 * @return WP_Gistpen_Post|File       WP_Gistpen object
	 * @since 0.4.0
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

			$term = $this->get_language_term_by_slug( $language );

			if ( is_wp_error( $term ) || empty( $term ) ) {
				$term = new stdCLass;
				$term->slug = 'bash';
			}

			$language = new WP_Gistpen_Language( $term );

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
	public function get_file( $post ) {
		$term = $this->get_language_term_by_post( $post );

		if ( is_wp_error( $term ) ) {
			// @todo error out
			return $term;
		}

		$language = new WP_Gistpen_Language( $term );

		return new WP_Gistpen_File( $post, $language );
	}

	/**
	 * Retrieves the term stdCLass object for a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return stdClass|WP_Error       term object or Error
	 * @since 0.4.0
	 */
	public function get_language_term_by_post( $post ) {
		$terms = get_the_terms( $post->ID, 'language' );

		if( $terms ) {
			$language = array_pop( $terms );
		} else {
			$language = new WP_Error( 'no_term_for_post', __( 'The file has no language', WP_Gistpen::get_instance()->get_plugin_slug() ) );
		}

		return $language;
	}

	/**
	 * Retrieves the term stdCLass object for a language slug
	 *
	 * @param  string $slug
	 * @return stdClass|WP_Error       term object or Error
	 * @since 0.4.0
	 */
	public function get_language_term_by_slug( $slug ) {
		$terms = get_terms( 'language', array( 'slug' => $slug, 'hide_empty' => false ) );

		if( is_wp_error( $terms ) ) {
			return $terms;
		}

		if( empty( $terms ) ) {
			return new WP_Error( 'no_term_for_slug', __( "No language term was found with that slug", WP_Gistpen::get_instance()->get_plugin_slug() ) );
		}

		return array_pop( $terms );
	}

	/**
	 * Retrieves the WP_Gistpen_Post for a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return WP_Gistpen_Language|WP_Error       language object or Error
	 * @since 0.4.0
	 */
	public function get_gistpen( $post ) {
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
	public function get_files( $post ) {
		$files_arr = get_children( array(
			'post_type' => 'gistpen',
			'post_parent' => $post->ID,
			'post_status' => $post->post_status
		) );

		foreach ( $files_arr as $file ) {
			$files[$file->ID] = $this->get_file( $file );
		}

		return $files;
	}

	/**
	 * Save the WP_Gistpen object to the database
	 *
	 * @param  WP_Gistpen_Post|File $post WP_Gistpen object
	 * @return int|WP_Error       post_id on success, WP_Error on failure
	 */
	public function save( $post ) {
		if ( ! $post instanceof WP_Gistpen_Post && ! $post instanceof WP_Gistpen_File ) {
			return new WP_Error( 'wrong_object', __( "Query only save WP_Gistpen_Posts or Files", WP_Gistpen::get_instance()->get_plugin_slug() ) );
		}

		$post->update_post();

		if ( $post instanceof WP_Gistpen_Post ) {
			$result = $this->save_post( $post );
		} elseif ( $post instanceof WP_Gistpen_File ) {
			$result = $this->save_file( $post );
		}

		return $result;
	}

	/**
	 * Save the WP_Gistpen_Post to the database
	 *
	 * @param  WP_Gistpen_Post $post
	 * @return true|WP_Error       true on success, WP_Error on failure
	 */
	public function save_post( $post ) {
		$result = wp_insert_post( (array) $post->post, true );

		if( is_wp_error( $result ) ) {
			return $result;
		}

		$post_id = $result;

		foreach ( $post->files as $file ) {
			$file->update_parent( $post_id );
			$result = $this->save_file( $file );

			if( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return $post_id;
	}

	/**
	 * Save the WP_Gistpen_File to the database
	 *
	 * @param  WP_Gistpen_File $post
	 * @return true|WP_Error       true on success, WP_Error on failure
	 */
	public function save_file( $file ) {
		$file_arr = (array) $file->file;

		if( null === $file_arr['ID'] ) {
			unset( $file_arr['ID'] );
		}

		$result = wp_insert_post( $file_arr, true );

		if( is_wp_error( $result ) ) {
			return $result;
		}

		$post_id = $result;

		$result = wp_set_object_terms( $result, $file->language->slug, 'language', false );

		if( is_wp_error( $result ) ) {
			return $result;
		}

		return $post_id;

	}
}
