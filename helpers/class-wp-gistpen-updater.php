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

}
