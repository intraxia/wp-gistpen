<?php
namespace Intraxia\Gistpen\Templating;

use Handlebars\Handlebars as Hbs;
use Intraxia\Gistpen\Contract\Templating;

/**
 * Class Handlebars.
 *
 * Templating Service for Handlebars.
 *
 * @package    Intraxia\Gistpen
 * @subpackage Templating
 */
class Handlebars implements Templating {
	/**
	 * Handlebars PHP implementation.
	 *
	 * @var Hbs
	 */
	protected $hbs;

	/**
	 * Handlebars constructor.
	 *
	 * @param Hbs $hbs
	 */
	public function __construct( Hbs $hbs ) {
		$this->hbs = $hbs;
	}

	/**
	 * Generates a string from the handlebars partials and provided data.
	 *
	 * @param string $partial
	 * @param array $data
	 *
	 * @return string
	 */
	public function render( $partial, array $data ) {
		return $this->hbs->render( $partial, $data );
	}
}
