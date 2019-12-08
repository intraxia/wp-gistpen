<?php
/**
 * Get the definitions for the container.
 *
 * @package Intraxia\Gistpen
 * @var array
 */

namespace Intraxia\Gistpen;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EM;

return [
	EM::class   => \DI\get( 'database' ),
	'loadables' => [
		Lifecycle::class,
		Console\Binding::class,
		Http\StrictParams::class,
	],
];
