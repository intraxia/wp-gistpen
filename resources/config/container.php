<?php
/**
 * Get the definitions for the container.
 *
 * @package Intraxia\Gistpen
 * @var array
 */

namespace Intraxia\Gistpen;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EM;
use Psr\Container\ContainerInterface as Container;
use Intraxia\Jaxion\Core\I18n;
use Intraxia\Jaxion\Core\Config;
use Intraxia\Jaxion\Core\Loader;
use Intraxia\Jaxion\Assets\Register as AssetsRegister;
use Intraxia\Jaxion\Http\Router;

$config = new Config(
	\Intraxia\Jaxion\Core\ConfigType::PLUGIN,
	dirname( dirname( __DIR__ ) ) . '/wp-gistpen.php'
);
$meta   = $config->get_php_config( 'meta' );

return [
	'file'                       => $config->file,
	'url'                        => $config->url,
	'path'                       => $config->path,
	'basename'                   => $config->basename,
	'slug'                       => $config->slug,
	'version'                    => $meta['version'],
	'prefix'                     => $meta['prefix'],
	'config'                     => $config,
	Config::class                => $config,
	I18n::class                  => function ( Container $container ) {
		return new I18n( $container->get( 'basename' ), $container->get( 'path' ) );
	},
	EM::class                    => \DI\object( Database\EntityManager::class )
		->constructorParameter( 'prefix', \DI\get( 'prefix' ) ),
	'database'                   => \DI\get( EM::class ),
	Jobs\Manager::class          => \DI\object()
		->method( 'add_job', 'export', \DI\get( Jobs\ExportJob::class ) )
		->method( 'add_job', 'import', \DI\get( Jobs\ImportJob::class ) ),
	Listener\Migration::class    => \DI\object( Listener\Migration::class )
		->constructor(
			\DI\get( EM::class ),
			\DI\get( 'prefix' ),
			\DI\get( 'slug' ),
			\DI\get( 'version' )
		),
	Contract\Translations::class => \DI\get( View\Translations::class ),
	Contract\Templating::class   => function ( Container $container ) {
		return new Templating\File(
			$container->get( Config::class ),
			$container->get( Contract\Translations::class ),
			$container->get( 'path' ) . 'resources/views/'
		);
	},
	View\Button::class           => function ( Container $container ) {
		return new View\Button(
			$container->get( Contract\Templating::class ),
			$container->get( Params\Repository::class ),
			$container->get( 'url' )
		);
	},
	View\Content::class          => function ( Container $container ) {
		return new View\Content(
			$container->get( Params\Repository::class ),
			$container->get( Contract\Templating::class ),
			// @TODO(mAAdhaTTah) depend on interface & autowire
			$container->get( \Intraxia\Jaxion\Assets\Register::class )
		);
	},
	View\Edit::class             => function ( Container $container ) {
		return new View\Edit(
			$container->get( EM::class ),
			$container->get( Params\Repository::class ),
			$container->get( Contract\Templating::class ),
			$container->get( 'path' ),
			$container->get( 'url' )
		);
	},
	View\Settings::class         => function ( Container $container ) {
		return new View\Settings(
			$container->get( Params\Repository::class ),
			$container->get( Contract\Templating::class ),
			$container->get( 'basename' ),
			$container->get( 'url' )
		);
	},
	Options\Site::class          => \DI\object( Options\Site::class )
		->constructor( \DI\get( 'slug' ) ),
	AssetsRegister::class        => \DI\object( AssetsRegister::class )
		->constructor( \DI\get( 'url' ), \DI\get( 'version' ) ),
	Router::class                => \DI\object( Router::class ),
	Register\Assets::class       => \DI\object( Register\Assets::class )
		->method( 'add_assets', \DI\get( AssetsRegister::class ) ),
	Register\Router::class       => \DI\object( Register\Router::class )
		->method( 'add_routes', \DI\get( Router::class ) ),
	Loader::class                => \DI\object( Loader::class )
		->method( 'register', \DI\get( Router::class ) )
		->method( 'register', \DI\get( AssetsRegister::class ) )
		->method( 'register', \DI\get( I18n::class ) )
		->method( 'register', \DI\get( Lifecycle::class ) )
		->method( 'register', \DI\get( Console\Binding::class ) )
		->method( 'register', \DI\get( Http\StrictParams::class ) )
		->method( 'register', \DI\get( Listener\Database::class ) )
		->method( 'register', \DI\get( Listener\Migration::class ) )
		->method( 'register', \DI\get( Listener\Sync::class ) )
		->method( 'register', \DI\get( Params\Blob::class ) )
		->method( 'register', \DI\get( Params\Editor::class ) )
		->method( 'register', \DI\get( Params\Gist::class ) )
		->method( 'register', \DI\get( Params\Globals::class ) )
		->method( 'register', \DI\get( Params\Jobs::class ) )
		->method( 'register', \DI\get( Params\Prism::class ) )
		->method( 'register', \DI\get( Params\Repo::class ) )
		->method( 'register', \DI\get( Params\Route::class ) )
		->method( 'register', \DI\get( Register\Assets::class ) )
		->method( 'register', \DI\get( Register\Data::class ) )
		->method( 'register', \DI\get( Register\Router::class ) )
		->method( 'register', \DI\get( View\Button::class ) )
		->method( 'register', \DI\get( View\Content::class ) )
		->method( 'register', \DI\get( View\Edit::class ) )
		->method( 'register', \DI\get( View\Settings::class ) )
		->method( 'register', \DI\get( View\Translations::class ) ),
];
