<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Contract\Templating;
use Intraxia\Gistpen\Params\Repository as Params;
use Intraxia\Jaxion\Contract\Core\HasFilters;

/**
 * Registers the Global state of the plugin.
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 * @todo       Push this class into Jaxion.
 */
class Globals implements HasFilters {

	/**
	 * Template service.
	 *
	 * @var Templating
	 */
	private $tmpl;

	/**
	 * Params service.
	 *
	 * @var Params
	 */
	private $params;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 *
	 * @param Templating $tmpl
	 * @param Params     $params
	 */
	public function __construct( Templating $tmpl, Params $params ) {
		$this->tmpl   = $tmpl;
		$this->params = $params;
	}

	/**
	 * Output the default state used by the TinyMCE plugin in a script tag.
	 */
	public function output_globals() {
		echo $this->tmpl->render( 'globals', $this->params->state( 'globals' ) );  // @codingStandardsIgnoreLine
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		foreach ( array( 'post.php', 'post-new.php' ) as $hook ) {
			$hooks[] = array(
				'hook'   => "admin_head-$hook",
				'method' => 'output_globals',
			);
		}

		return $hooks;
	}
}
