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

	}
}
