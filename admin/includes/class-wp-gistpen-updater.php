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
				// Deactivate and quit
				deactivate_plugins( 'WP-Gistpen');
				// and provide an error
				print ( "Failed to successfully insert {$slug}. Error: " . $result->get_error_message() );
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

			if( ! $query->have_posts() ) {
				// only delete language if it's got no Gistpens
				$term = get_term_by( 'slug', $slug, 'language', 'ARRAY_A' );
				$result = wp_delete_term( $term['term_id'], 'language' );
				if ( is_wp_error( $result ) ) {
					// Deactivate and quit
					deactivate_plugins( 'WP-Gistpen');
					// and provide an error
					print ( "Failed to successfully delete {$slug}. Error: " . $result->get_error_message() );
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
		// We removed this post_type and taxonomy, so we need to add them to use them
		register_post_type( 'gistpens', array() );
		register_taxonomy( 'language', array( 'gistpens' ) );

		// Need to remove these filters first
		remove_filter( 'the_content', 'wpautop' );
		remove_filter( 'the_content', 'wptexturize' );
		remove_filter( 'the_content', 'capital_P_dangit' );
		remove_filter( 'the_content', 'convert_chars' );
		remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );

		$terms = get_terms( 'language', 'hide_empty=0' );
		foreach ( $terms as $term ) {
			// We're going to move the current term to a holdover
			$result = wp_update_term( $term->term_id, 'language', array(
				'slug' => $term->slug . '-old',
				'name' => $term->name . '-old'
			) );
			if( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( __FILE__);
				// and provide an error
				print ( "Failed to successfully set holdover for language {$term->slug}. Error: " . $result->get_error_message() );
			}

			// So we can create new terms with the old slug/name combo
			$result = wp_insert_term( $term->name, 'wpgp_language', array(
				'slug' => $term->slug,
				'name' => $term->name
			) );
			if( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( __FILE__);
				// and provide an error
				print ( "Failed to successfully insert language {$term->slug}. Error: " . $result->get_error_message() );
			}
		}

		// Get all the Gistpens to work with
		$posts = get_posts( array(
			'post_type' => 'gistpens',
			'posts_per_page' => -1,
			'post_status' => 'any'
		) );

		foreach ( $posts as $post ) {
			// Cache and clear content
			$content = $post->post_content;
			$post->post_content = '';

			// Update post type to remove the 's'
			$post->post_type = 'gistpen';

			$wpgp_post = WP_Gistpen::get_instance()->query->create( $post );
			$wpgp_post->files[] = new WP_Gistpen_File( new WP_Post( new stdClass ), new WP_Gistpen_Language( new stdClass  ) );

			// Migrate title to file's name
			$wpgp_post->files[0]->slug = $post->post_title;

			// Migrate description to Gistpen title and remove post_meta
			$wpgp_post->description = get_post_meta( $post->ID, '_wpgp_gistpen_description', true );
			$result = delete_post_meta( $post->ID, '_wpgp_gistpen_description' );

			if ( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( 'WP-Gistpen');
				// and provide an error
				print ( "Failed to successfully delete description meta from {$post->ID}. Error: " . $result->get_error_message() );
			}

			// Set content
			$wpgp_post->files[0]->code = $content;

			// Migrate Gistpen's language and remove
			// @todo move this into helper function?
			$terms = get_the_terms( $post->ID, 'language' );
			if( $terms ) {
				$lang = array_pop( $terms );
			}

			// Don't forget to remove that holdover!
			$wpgp_post->files[0]->language->slug = str_replace( "-old", "", $lang->slug );

			$result = wp_set_object_terms( $post->ID, array(), 'language', false );

			if ( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( __FILE__);
				// and provide an error
				print ( "Failed to successfully delete language from {$post->ID}. Error: " . $result->get_error_message() );
			}

			$wpgp_post->files[0]->update_timestamps( $post->post_date, $post->post_date_gmt );

			$result = WP_Gistpen::get_instance()->query->save( $wpgp_post );

			if ( is_wp_error( $result ) ) {
				// Deactivate and quit
				deactivate_plugins( __FILE__);
				// and provide an error
				print ( "Failed to successfully save {$post->ID} in new format. Error: " . $result->get_error_message() );
			}

		}

		$terms = get_terms( 'language', 'hide_empty=0' );

		foreach ( $terms as $term ) {
			wp_delete_term( $term->term_id, 'language' );
		}

		flush_rewrite_rules( true );
	}

}
