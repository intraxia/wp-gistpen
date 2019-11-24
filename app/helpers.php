<?php
namespace Intraxia\Gistpen;

use Intraxia\Jaxion\Core\Config;
use Intraxia\Jaxion\Core\Container;
use Intraxia\Jaxion\Core\I18n;
use Intraxia\Jaxion\Core\Loader;
use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\HasFilters;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

/**
 * Returns the application container. Bootstraps it if it doesn't exist yet.
 *
 * @param  ConfigType $type The type of application running.
 * @param  string     $file The path of the application.
 * @return \Di\Container    The application container.
 */
function container( $type = null, $file = null ) {
	static $container;

	if ( ! $container ) {
		$builder     = new \DI\ContainerBuilder();
		$config      = new Config( $type, $file );
		$meta        = $config->get_php_config( 'meta' );
		$config_defs = [
			'file'        => $config->file,
			'url'         => $config->url,
			'path'        => $config->path,
			'basename'    => $config->basename,
			'slug'        => $config->slug,
			'version'     => $meta['version'],
			'config'      => $config,
			Config::class => $config,
		];
		$builder->addDefinitions( $config_defs );
		$builder->addDefinitions( $config->get_php_config( 'container' ) );

		// @TODO(mAAdhaTTah) remove when providers are eliminated.
		$old_container = new Container();

		foreach ( $config_defs as $key => $value ) {
			$old_container->share( $key, $value );
		}

		$old_container->share( [ 'loader' => Loader::class ], function () {
			return new Loader();
		} );
		$old_container->share( [ 'i18n' => I18n::class ], function ( Container $app ) {
			return new I18n( $app->fetch( 'basename' ), $app->fetch( 'path' ) );
		} );

		foreach ( $config->get_php_config( 'providers' ) as $provider ) {
			$old_container->register( new $provider() );
		}

		$definitions = [];

		foreach ( $old_container as $key => $value ) {
			$definitions[ $key ] = $value;
			if ( is_object( $value ) ) {
				$class = get_class( $value );

				if ( $class ) {
					$definitions[ $class ] = $value;

					if (
						$value instanceof HasActions ||
						$value instanceof HasFilters ||
						$value instanceof HasShortcode
					) {
						$builder->addDefinitions([
							'loadables' => \DI\add([
								\DI\value( $class ),
							]),
						]);
					}
				}
			}
		}

		$builder->addDefinitions( $definitions );
		// end remove
		$container = $builder->build();
	}

	return $container;
}

/**
 * Boot the application.
 *
 * @param  ConfigType $type The type of application running.
 * @param  string     $file The path of the application.
 */
function boot( $type, $file ) {
	$container = container( $type, $file );
	$loader    = $container->get( Loader::class );
	$loadables = $container->get( 'loadables' );

	foreach ( $loadables as $loadable ) {
		$load = $container->get( $loadable );

		if ( $load instanceof HasActions ) {
			$loader->register_actions( $load );
		}

		if ( $load instanceof HasFilters ) {
			$loader->register_filters( $load );
		}

		if ( $load instanceof HasShortcode ) {
			$loader->register_shortcode( $load );
		}
	}

	add_action( 'plugins_loaded', [ $loader, 'run' ] );
}
