<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * Default functions for the object wrappers
 *
 * @package WP_Gistpen_Post_Abstract
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
abstract class WP_Gistpen_Abtract  {

	/**
	 * User a default getter
	 * @param  string $name variable to get
	 * @return $name|null       Returns the variable if a function exists
	 */
	public function __get( $name ) {
		$function = 'get_' . $name;

		if ( method_exists( $this, $function ) ) {
			return $this->$function();
		}

		// lifted from here: http://php.net/manual/en/language.oop5.overloading.php#object.get
		$trace = debug_backtrace();
		trigger_error(
				'Undefined property via __get(): ' . $name .
				' in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE);
		return null;
	}

	abstract public function update_post();
}
