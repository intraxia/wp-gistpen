<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class checks the current version and runs any updates necessary.
 *
 * @package WP_Gistpen_Updater
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Updater {

	public static $removed_langs_0_3_0 = array(
		'AppleScript' => 'applescript',
		'ActionScript3' => 'as3',
		'ColdFusion' => 'cf',
		'CPP' => 'cpp',
		'Delphi' => 'delphi',
		'Diff' => 'diff',
		'Erlang' => 'erl',
		'JavaFX' => 'jfx',
		'Perl' => 'perl',
		'Vb' => 'vb',
		'Xml' => 'xml',
	);

	public static $added_langs_0_3_0 = array(
		'C' => 'c',
		'Coffeescript' => 'coffeescript',
		'C#' => 'csharp',
		'Go' => 'go',
		'HTTP' => 'http',
		'ini' => 'ini',
		'HTML/Markup' => 'markup',
		'Objective-C' => 'objectivec',
		'Swift' => 'swift',
		'Twig' => 'twig'
	);

	public static function run() {
		// Check if plugin needs to be upgraded
		$version = get_option( 'wp_gistpen_version', '0.0.0' );

		if( $version !== WP_Gistpen::VERSION ) {
			WP_Gistpen_Updater::update( $version );
			update_option( 'wp_gistpen_version', WP_Gistpen::VERSION );
		}
	}

	/**
	 * Checks current version and manages database changes
	 *
	 * @param  string $version Current version number
	 * @since  0.3.0
	 */
	public static function update( $version ) {

		if( version_compare( $version, '0.3.0', '<' ) ) {
			self::update_to_0_3_0();
		}

		if( version_compare( $version, '0.4.0', '<' ) ) {
			self::update_to_0_4_0();
		}

	}

	/**
	 * Update the database to version 0.3.0
	 *
	 * @return bool true if successful
	 * @since 0.3.0
	 */
	public static function update_to_0_3_0() {

		foreach( self::$added_langs_0_3_0 as $lang => $slug ) {
			$result = wp_insert_term( $lang, 'language', array( 'slug' => $slug ) );
			if ( is_wp_error( $result ) ) {
				// @todo write error message?
			}

		}

		foreach( self::$removed_langs_0_3_0 as $lang => $slug ) {
			// check if there are any gistpens in the db for this language
			$query = new WP_Query(
				array(
					'language' => $slug,
					'post_type' => 'gistpens'
				));

			// Migrate XML to Markup
			if ( 'xml' == $slug && $query->have_posts() ) {

				while( $query->have_posts() ) {
					$query->the_post();
					wp_delete_object_term_relationships( get_the_id(), 'language' );
					wp_set_object_terms( get_the_id(), 'markup', 'language', false );
				}

				wp_reset_postdata();
			}

			if( !$query->have_posts() ) {
				// only delete language if it's got no Gistpens
				$term = get_term_by( 'slug', $slug, 'language', 'ARRAY_A' );
				$result = wp_delete_term( $term['term_id'], 'language' );
				if ( is_wp_error( $result ) ) {
					// @todo write error message?
				}
			}

		}

	}

	/**
	 * Update the database to version 0.4.0
	 *
	 * @return bool true if successful
	 * @since 0.4.0
	 */
	public static function update_to_0_4_0() {
		$posts = get_posts( array(
			'post_type' => 'gistpens',
			'posts_per_page' => -1,
			'post_status' => 'any'
		) );

		foreach ( $posts as $post ) {
			$terms = get_the_terms( $post->ID, 'language' );

			if( $terms ) {
				$lang = array_pop( $terms );
			}

			$_POST['wp-gistpenfile-language'] = $lang->slug;

			wp_set_object_terms( $post->ID, '', 'language' );

			// Get and clear content
			$_POST['wp-gistpenfile-content'] = $post->post_content;
			$post->post_content = '';

			// Migrate titles and descriptions
			$_POST['wp-gistpenfile-name'] = $post->post_title;
			$post->post_name = get_post_meta( $post->ID, '_wpgp_gistpen_description', true );
			$post->post_title = $post->post_name;
			delete_post_meta( $post->ID, '_wpgp_gistpen_description' );

			$result = wp_update_post( $post );

			if ( is_wp_error( $result ) ) {
				// @todo error message?
			}

			$children = get_children(array( 'post_parent' => $post->ID ) );
			if( null !== $children ) {
				foreach ( $children as $key => $child_post) {
					wp_delete_post( $child_post->ID, 'true' );
				}
			}

			WP_Gistpen_Saver::save_gistpen( $post->ID );
		}
	}

}
