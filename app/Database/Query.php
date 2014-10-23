<?php
namespace WP_Gistpen\Database;

/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use \stdClass;
use \WP_Post;
use \WP_Error;
use WP_Gistpen\Gistpen\Language;
use WP_Gistpen\Gistpen\File;
use WP_Gistpen\Gistpen\Zip;

/**
 * This class saves and gets Gistpens from the database
 *
 * @package WP_Gistpen_Query
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Query {

	/**
	 * Search for recent Files
	 *
	 * @param  int|null $search Search term, or null for recent 5
	 * @return array|WP_Error         search results, or error if no results
	 * @since 0.4.0
	 */
	public static function search( $search, $number = 5 ) {
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
			return new WP_Error('no_results', __("Search returned no results.") );
		}

		foreach ( $result as $gistpen ) {
			if ( 0 === $gistpen->post_parent ) {
				$result = self::create( $gistpen );
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				$results[] = $result;
			} else {
				$term = self::get_language_term_by_post( $gistpen );

				if ( is_wp_error( $term ) ) {
					$term = new stdClass;
					$term->slug = 'bash';
				}

				$result = self::create( $gistpen, $term->slug );
				if ( is_wp_error( $result ) ) {
					return $result;
				}

				$results[] = $result;
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
	public static function create( WP_Post $post, $language = '' ) {

		if ( 'gistpen' !== $post->post_type ) {
			return new WP_Error( 'not_gistpen', __( "WP_Gistpen_Query::save can only create gistpens", \WP_Gistpen::$plugin_name ) );
		}

		if ( 0 === $post->post_parent ) {
			return new Zip( $post );
		} else {

			if( '' === $language ) {
				return new WP_Error( 'no_language_set', __( "Post with a parent needs a language, no language set", \WP_Gistpen::$plugin_name ) );
			}

			$term = self::get_language_term_by_slug( $language );

			if ( is_wp_error( $term ) || empty( $term ) ) {
				$term = new \stdCLass;
				$term->slug = 'bash';
			}

			$language = new Language( $term );

			return new File( $post, $language );
		}
	}

	/**
	 * Retrieves the correct WP_Gistpen object from the database
	 *
	 * @param  WP_Post|int $post Accepts a WP_Post object or a post ID
	 * @return WP_Gistpen_Post|File       WP_Gistpen object
	 * @since 0.4.0
	 */
	public static function get( $post ) {

		if( ! is_object( $post ) ) {

			if ( is_numeric( $post ) ) {
				$post = get_post( $post );

				if ( $post === null ) {
					return new WP_Error( 'get_post_failed', __( "get_post failed for ID {$post}", \WP_Gistpen::$plugin_name ) );
				}
			} else {
				return new WP_Error( 'wrong_construct_args', __( "WP_Gistpen_Query::get() needs an ID or object", \WP_Gistpen::$plugin_name ) );
			}

		}

		if ( $post->post_type !== 'gistpen' ) {
			return new WP_Error( 'wrong_post_type', __( "WP_Gistpen_Query::get() didn't get a Gistpen", \WP_Gistpen::$plugin_name ) );
		}

		if( 0 !== $post->post_parent ) {
			return self::get_file( $post );
		} else {
			return self::get_gistpen( $post );
		}

	}

	/**
	 * Retrieves the WP_Gistpen_File from the WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return WP_Gistpen_File|WP_Error
	 * @since 0.4.0
	 */
	protected static function get_file( WP_Post $post ) {
		$term = self::get_language_term_by_post( $post );

		if ( is_wp_error( $term ) ) {
			return $term;
		}

		return new File( $post, new Language( $term ) );
	}

	/**
	 * Retrieves the term stdCLass object for a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return stdClass|WP_Error       term object or Error
	 * @since 0.4.0
	 */
	public static function get_language_term_by_post( WP_Post $post ) {
		$terms = get_the_terms( $post->ID, 'wpgp_language' );

		if( $terms ) {
			$language = array_pop( $terms );
		} else {
			$language = new WP_Error( 'no_term_for_post', __( "The file {$post->ID} has no language", \WP_Gistpen::$plugin_name ) );
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
	public static function get_language_term_by_slug( $slug ) {
		$terms = get_terms( 'wpgp_language', array(
			'slug' => $slug,
			'hide_empty' => false
		) );

		if( is_wp_error( $terms ) ) {
			return $terms;
		}

		if( empty( $terms ) ) {
			return new WP_Error( 'no_term_for_slug', __( "No language term was found with that slug", \WP_Gistpen::$plugin_name ) );
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
	protected static function get_gistpen( WP_Post $post ) {
		$files = self::get_files( $post );

		if ( is_wp_error( $files ) ) {
			return $files;
		}

		return new Zip( $post, $files );
	}

	/**
	 * Retrieves the all the WP_Gistpen_File's for a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return array|WP_Error       array of WP_Gistpen_Files or Error
	 * @since  0.4.0
	 */
	protected static function get_files( WP_Post $post ) {
		$files_arr = get_children( array(
			'post_type' => 'gistpen',
			'post_parent' => $post->ID,
			'post_status' => $post->post_status,
			'order' => 'ASC',
			'orderby' => 'date',
		) );

		if( empty( $files_arr ) ) {
			return array();
		}

		foreach ( $files_arr as $file ) {
			$result = self::get_file( $file );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			$files[$file->ID] = $result;
		}

		return $files;
	}

	/**
	 * Save the WP_Gistpen object to the database
	 *
	 * @param  WP_Gistpen_Post|File $post WP_Gistpen object
	 * @return int|WP_Error         post_id on success, WP_Error on failure
	 * @since  0.4.0
	 */
	public static function save( $post ) {
		if ( ! $post instanceof Zip && ! $post instanceof File ) {
			return new WP_Error( 'wrong_object', __( "Query only saves WP_Gistpen_Posts or Files", \WP_Gistpen::$plugin_name ) );
		}

		$post->update_post();

		if ( $post instanceof Zip ) {
			return self::save_post( $post );
		} elseif ( $post instanceof File ) {
			return self::save_file( $post );
		}

	}

	/**
	 * Save the WP_Gistpen_Post to the database
	 *
	 * @param  WP_Gistpen_Post $post
	 * @return int|WP_Error    post_id on success, WP_Error on failure
	 * @since  0.4.0
	 */
	protected static function save_post( Zip $zip ) {
		if ( $zip->post->post_type !== 'gistpen' ) {
			$zip->post->post_type = 'gistpen';
		}

		$result = wp_insert_post( (array) $zip->post, true );

		if( is_wp_error( $result ) ) {
			return $result;
		}

		$post_id = $result;

		foreach ( $zip->files as $file ) {
			$file->update_parent( $post_id );

			$result = self::save_file( $file );

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
	 * @return true|WP_Error   post_id on success, WP_Error on failure
	 * @since  0.4.0
	 */
	protected static function save_file( File $file ) {
		if ( $file->file->post_type !== 'gistpen' ) {
			$file->file->post_type = 'gistpen';
		}
		$file_arr = (array) $file->file;

		if( null === $file_arr['ID'] ) {
			unset( $file_arr['ID'] );
		}

		$result = wp_insert_post( $file_arr, true );

		if( is_wp_error( $result ) ) {
			return $result;
		}

		$post_id = $result;

		$result = wp_set_object_terms( $result, $file->language->slug, 'wpgp_language', false );

		if( is_wp_error( $result ) ) {
			return $result;
		}

		if ( is_string( $result ) ) {
			return new WP_Error( 'wrong_slug', __( "{$file->language->slug} is named incorrectly.", \WP_Gistpen::$plugin_name ) );
		}

		return $post_id;

	}
}
