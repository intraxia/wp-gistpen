<?php
namespace WP_Gistpen\Facade;

/**
 * This is the class description.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class App {

	/**
	 * Retrieve a global object
	 *
	 * @param  string $obj Global object name
	 * @return obj         Global object
	 */
	public static function get( $obj ) {
		$app = wp_gistpen();

		return $app->$obj;
	}
}
