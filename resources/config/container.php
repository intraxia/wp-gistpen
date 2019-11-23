<?php
/**
 * Get the definitions for the container.
 *
 * @package Intraxia\Gistpen
 * @var array
 */

namespace Intraxia\Gistpen;

return [
	Lifecycle::class => \DI\object(),
	'loadables' => [
		Lifecycle::class,
	],
];
