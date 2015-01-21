<?php
namespace WP_Gistpen\Adapter;

use WP_Gistpen\Model\Language as LanguageModel;

/**
 * Builds language models based on various data inputs
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Language {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Map of Gist to Gistpen languages
	 * @var   array
	 * @since 0.5.0
	 */
	protected $map;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->map = array(
			'scss'        => 'sass',
			'python'      => 'py',
			'text'        => 'plaintext',
			'javascript'  => 'js',
			'c#'          => 'csharp',
			'shell'       => 'bash',
			'objective-c' => 'objectivec',
			'tex'         => 'latex',
			'diff'        => 'git',
			'cucumber'    => 'gherkin',
			'assembly'    => 'nasm',
		);
	}

	/**
	 * Builds the Language object by language slug
	 *
	 * @param  string $slug
	 * @return Language       Language object
	 * @since 0.4.0
	 */
	public function by_slug( $slug ) {
		return new LanguageModel( $this->plugin_name, $this->version, $slug );
	}

	/**
	 * Builds the language object based on Gist's language slug
	 *
	 * @param  string $language Gist's language string
	 * @return LanguageModel
	 * @since  0.5.0
	 */
	public function by_gist( $language ) {
		$slug = strtolower( $language );

		if ( array_key_exists( $slug, $this->map ) ) {
			$slug = $this->map[ $slug ];
		}

		try {
			$language = new LanguageModel( $this->plugin_name, $this->version, $slug );
		} catch ( \Exception $e ) {
			// Default to "plaintext" if we don't support the imported language
			$language = new LanguageModel( $this->plugin_name, $this->version, 'plaintext' );
		}

		return $language;
	}

	/**
	 * Builds a blank Language object
	 *
	 * @return Language Language object
	 * @since 0.5.0
	 */
	public function blank() {
		return new LanguageModel( $this->plugin_name, $this->version );
	}
}
