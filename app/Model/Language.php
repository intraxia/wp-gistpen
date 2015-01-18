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
		'Assembly (NASM)' => 'nasm',
		'ActionScript' => 'actionscript',
		'AppleScript' => 'applescript',
		'Bash' => 'bash',
		'C' => 'c',
		'Coffeescript' => 'coffeescript',
		'C#' => 'csharp',
		'CSS' => 'css',
		'Dart' => 'dart',
		'Eiffel' => 'eiffel',
		'Erlang' => 'erlang',
		'Gherkin/Cucumber' => 'gherkin',
		'Git/Diff' => 'git',
		'Go' => 'go',
		'Groovy' => 'groovy',
		'HAML' => 'haml',
		'Handlebars' => 'handlebars',
		'HTML' => 'html',
		'HTTP' => 'http',
		'ini' => 'ini',
		'Jade' => 'jade',
		'Java' => 'java',
		'JavaScript' => 'js',
		'LaTeX' => 'latex',
		'LESS' => 'less',
		'Markdown' => 'markdown',
		'Matlab' => 'matlab',
		'Objective-C' => 'objectivec',
		'Perl' => 'perl',
		'PHP' => 'php',
		'PlainText' => 'plaintext',
		'PowerShell' => 'powershell',
		'Python' => 'py',
		'R' => 'r',
		'Rust' => 'rust',
		'Ruby' => 'ruby',
		'Sass' => 'sass',
		'Scala' => 'scala',
		'Scheme' => 'scheme',
		'Smarty' => 'smarty',
		'Sql' => 'sql',
		'Swift' => 'swift',
		'Twig' => 'twig',
		'XML' => 'xml',
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

		// Convert "Markup" to "HTML"
		if ( 'markup' === $slug ) {
			$slug = 'html';
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
		$map = array(
			'js' => 'javascript',
			'sass' => 'scss',
			'py' => 'python',
			'html' => 'markup',
			'xml' => 'markup',
		);

		$slug = $this->slug;

		if ( array_key_exists( $slug, $map ) ) {
			$slug = $map[ $slug ];
		}

		return $slug;
	}

	/**
	 * Gets the file extension slug based on the language slug.
	 *
	 * @since  0.4.0
	 * @return string The file extension slug
	 */
	public function get_file_ext() {
		$map = 	array(
			'sass' => 'scss',
			'bash' => 'sh',
			'ruby' => 'rb',
			'plaintext' => 'txt',
			'csharp' => 'cs',
			'coffeescript' => 'coffee',
			'objectivec' => 'm',
			'actionscript' => 'as',
			'eiffel' => 'e',
			'erlang' => 'erl',
			'gherkin' => 'feature',
			'git' => 'diff',
			'perl' => 'pl',
			'latex' => 'tex',
			'markdown' => 'md',
			'nasm' => 'asm',
			'powershell' => 'ps1',
			'rust' => 'rs',
			'scheme' => 'scm',
			'smarty' => 'tpl',
		);

		$slug = $this->slug;

		if ( array_key_exists( $slug, $map ) ) {
			$slug = $map[ $slug ];
		}

		return $slug;
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
