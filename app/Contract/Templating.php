<?php
namespace Intraxia\Gistpen\Contract;

/**
 * Interface Templating.
 *
 * Enforces the methods required of a Templating service.
 *
 * @package Intraxia\Gistpen
 * @subpackage Contract
 */
interface Templating {
	/**
	 * Renders a template string for a given partial.
	 *
	 * @param string $partial Partial name to render.
	 * @param array  $data    Data to render into the partial.
	 *
	 * @return string  Rendered template string.
	 */
	public function render( $partial, array $data );
}
