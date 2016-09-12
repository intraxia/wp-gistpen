<?php
namespace Intraxia\Gistpen\Providers;

use Handlebars\Context;
use Handlebars\Handlebars;
use Handlebars\Helpers;
use Handlebars\Loader\FilesystemLoader;
use Handlebars\Template;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;
use Intraxia\Gistpen\Templating\Handlebars as HandlebarsTemplating;

class TemplatingServiceProvider implements ServiceProvider {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container
	 *
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function register( Container $container ) {
		$container->define( 'templating', function ( Container $container ) {
			$client  = $container->fetch( 'path' ) . 'client';
			$options = array(
				'extension' => 'hbs',
			);
			$helpers = new Helpers(array(
				'compare' => function(Template $template, Context $context, $args, $source) {
					$parsedArgs = $template->parseArguments($args);
					$first = $context->get($parsedArgs[0]);
					$second= $context->get($parsedArgs[1]);
					if ( $first === $second ) {
						$template->setStopToken('else');
						$buffer = $template->render($context);
						$template->setStopToken(false);
						$template->discard();
					} else {
						$template->setStopToken('else');
						$template->discard();
						$template->setStopToken(false);
						$buffer = $template->render($context);
					}
					return $buffer;
				}
			));
			$config  = array(
				'helpers'         => $helpers,
				'loader'          => new FilesystemLoader( $client, $options ),
				'partials_loader' => new FilesystemLoader( $client, $options ),
			);

			return new HandlebarsTemplating( new Handlebars( $config ) );
		} );
	}
}
