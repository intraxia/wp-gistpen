<?php
namespace Intraxia\Gistpen\Templating;

use Exception;
use Intraxia\Jaxion\Core\Config;
use Intraxia\Gistpen\Contract\Translator;
use Intraxia\Gistpen\Contract\Templating;

/**
 * Class Handlebars.
 *
 * Templating Service for Handlebars.
 *
 * @package    Intraxia\Gistpen
 * @subpackage Templating
 */
class File implements Templating {
	/**
	 * App path.
	 *
	 * @var string
	 */
	protected $views;

	/**
	 * Translation service.
	 *
	 * @var Translator
	 */
	protected $translator;

	/**
	 * App Config service.
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Handlebars constructor.
	 *
	 * @param Config     $config
	 * @param Translator $translator
	 * @param string     $views
	 */
	public function __construct( Config $config, Translator $translator, $views ) {
		$this->config     = $config;
		$this->views        = $views;
		$this->translator = $translator;
	}

	/**
	 * Generates a string from the handlebars partials and provided data.
	 *
	 * @param string $partial
	 * @param array $data
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function render( $partial, array $data ) {
		return require $this->views . '/' . $partial . '.php';
	}

	/**
	 * Convert language slug to prism slug.
	 *
	 * @param  string $slug Language slug.
	 * @return string       Prism language slug.
	 */
	public function prism_slug( $slug ) {
		$languages = $this->config->get_config_json( 'languages' );
		$map       = $languages['aliases'];

		if ( array_key_exists( $slug, $map ) ) {
			$slug = $map[ $slug ];
		}

		return $slug;
	}
}
