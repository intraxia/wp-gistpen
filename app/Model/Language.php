<?php
namespace WP_Gistpen\Model;

/**
 * Manages the Gistpen's file language data
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Language {

	/**
	 * Languages currently supported
	 *
	 * @var      array
	 * @since    0.1.0
	 */
	public static $supported = array(
		'Bash' => 'bash',
		'C' => 'c',
		'Coffeescript' => 'coffeescript',
		'C#' => 'csharp',
		'CSS' => 'css',
		'Groovy' => 'groovy',
		'Java' => 'java',
		'JavaScript' => 'js',
		'PHP' => 'php',
		'PlainText' => 'plaintext',
		'Python' => 'py',
		'Ruby' => 'ruby',
		'Sass' => 'sass',
		'Scala' => 'scala',
		'Sql' => 'sql',
		'Go' => 'go',
		'HTTP' => 'http',
		'ini' => 'ini',
		'HTML/Markup' => 'markup',
		'Objective-C' => 'objectivec',
		'Swift' => 'swift',
		'Twig' => 'twig',
	);

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.5.0
	 * @access   protected
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * The language slug.
	 *
	 * @since    0.5.0
	 * @access   protected
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $slug;

	public function __construct( $plugin_name, $version, $slug = '' ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->validate_slug( $slug );

		$this->slug = $slug;

	}

	/**
	 * Gets the language slug.
	 *
	 * @since  0.4.0
	 * @return string The language slug
	 */
	public function get_slug() {
		return $this->slug;
	}


	/**
	 * Validates & sets the language slug
	 *
	 * @since 0.5.0
	 * @param string $slug language slug
	 */
	public function set_slug( $slug ) {
		$this->validate_slug( $slug );

		$this->slug = $slug;
	}

	/**
	 * Validates the language slug
	 *
	 * @param string $slug  Language slug to validate
	 * @throws Exception If invalid slug
	 */
	public function validate_slug( $slug ) {
		// empty slug is allowed
		if ( '' === $slug ) {
			return;
		}

		// otherwise, the slug needs ot match a supported slug
		if ( ! array_search( $slug, self::$supported ) ) {
			throw new \Exception( __( "Invalid language slug: {$slug}", $this->plugin_name ), 1 );
		}
	}

	/**
	 * Gets the Prism language slug based on the language slug.
	 *
	 * @since  0.4.0
	 * @return string The language slug used by Prism for highlighting
	 */
	public function get_prism_slug() {
		return ( $this->slug == 'js' ? 'javascript' :
			( $this->slug == 'sass' ? 'scss' :
			( $this->slug == 'sh' ? 'bash' :
			$this->slug ) ) );
	}

	/**
	 * Gets the file extension slug based on the language slug.
	 *
	 * @since  0.4.0
	 * @return string The file extension slug
	 */
	public function get_file_ext() {
		return ( $this->slug == 'sass' ? 'scss' :
			( $this->slug == 'bash' ? 'sh' :
			( $this->slug == 'ruby' ? 'rb' :
			$this->slug ) ) );
	}

	/**
	 * Gets the display name based on the language slug.
	 *
	 * @since  0.4.0
	 * @return string The display name
	 */
	public function get_display_name() {
		return array_search( $this->slug, self::$supported );
	}
}
