<?php
/**
 * Get the providers for the old container.
 *
 * @package Intraxia\Gistpen
 * @var array
 */

namespace Intraxia\Gistpen;

return [
	Providers\ClientServiceProvider::class,
	Providers\ViewServiceProvider::class,
	Providers\TranslationsServiceProvider::class,
	Providers\TemplatingServiceProvider::class,
	Providers\OptionsServiceProvider::class,
	Providers\AssetsServiceProvider::class,
	Providers\DatabaseServiceProvider::class,
	Providers\JobsServiceProvider::class,
	Providers\ControllerServiceProvider::class,
	Providers\CoreServiceProvider::class,
	Providers\RouterServiceProvider::class,
	Providers\ParamsServiceProvider::class,
	Providers\ListenerServiceProvider::class,
];
