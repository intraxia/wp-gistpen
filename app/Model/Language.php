<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\UsesWordPressTerm;

/**
 * Manages the Gistpen's file language data
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 *
 * @property int    $ID
 * @property string $slug
 */
class Language extends Model implements UsesWordPressTerm {
	/**
	 * Languages currently supported
	 *
	 * @var      array
	 * @since    0.1.0
	 */
	public static $supported = array(
		'Assembly (NASM)'  => 'nasm',
		'ActionScript'     => 'actionscript',
		'AppleScript'      => 'applescript',
		'Bash'             => 'bash',
		'C'                => 'c',
		'Coffeescript'     => 'coffeescript',
		'C#'               => 'csharp',
		'CSS'              => 'css',
		'Dart'             => 'dart',
		'Eiffel'           => 'eiffel',
		'Erlang'           => 'erlang',
		'Gherkin/Cucumber' => 'gherkin',
		'Git/Diff'         => 'git',
		'Go'               => 'go',
		'Groovy'           => 'groovy',
		'HAML'             => 'haml',
		'Handlebars'       => 'handlebars',
		'HTML'             => 'html',
		'HTTP'             => 'http',
		'ini'              => 'ini',
		'Jade'             => 'jade',
		'Java'             => 'java',
		'JavaScript'       => 'js',
		'LaTeX'            => 'latex',
		'LESS'             => 'less',
		'Markdown'         => 'markdown',
		'Matlab'           => 'matlab',
		'Objective-C'      => 'objectivec',
		'Perl'             => 'perl',
		'PHP'              => 'php',
		'PlainText'        => 'plaintext',
		'PowerShell'       => 'powershell',
		'Python'           => 'py',
		'R'                => 'r',
		'Rust'             => 'rust',
		'Ruby'             => 'ruby',
		'Sass'             => 'sass',
		'Scala'            => 'scala',
		'Scheme'           => 'scheme',
		'Smarty'           => 'smarty',
		'Sql'              => 'sql',
		'Swift'            => 'swift',
		'Twig'             => 'twig',
		'XML'              => 'xml',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $fillable = array( 'slug' );

	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $guarded = array( 'ID' );

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public static function get_taxonomy() {
		return 'wpgp_language';
	}

	/**
	 * Language constructor.
	 *
	 * @param string|array $attributes
	 */
	public function __construct( $attributes = '' ) {
		if ( is_array( $attributes ) ) {
			parent::__construct( $attributes );
		} else {
			$this->validate_slug( $attributes );
			$this->set_attribute( 'slug', $attributes );
		}
	}

	/**
	 * Maps the Language's ID to the WP_Term term_id.
	 *
	 * @return string
	 */
	public function map_ID() {
		return 'term_id';
	}

	/**
	 * Maps the Language's ID to the WP_Term slug.
	 *
	 * @return string
	 */
	public function map_slug() {
		return 'slug';
	}

	/**
	 * Gets the language slug.
	 *
	 * @since  0.4.0
	 * @return string The language slug
	 * @deprecated
	 */
	public function get_slug() {
		return $this->get_attribute( 'slug' );
	}

	/**
	 * Validates & sets the language slug
	 *
	 * @since 0.5.0
	 *
	 * @param string $slug language slug
	 *
	 * @deprecated
	 */
	public function set_slug( $slug ) {
		$this->validate_slug( $slug );

		$this->set_attribute( 'slug', $slug );
	}

	/**
	 * Validates the language slug
	 *
	 * @param string $slug Language slug to validate
	 *
	 * @throws \Exception If invalid slug
	 * @deprecated
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
			throw new \Exception( __( "Invalid language slug: {$slug}", 'wp-gistpen' ), 1 );
		}
	}

	/**
	 * Gets the Prism language slug based on the language slug.
	 *
	 * @since  0.4.0
	 * @return string The language slug used by Prism for highlighting
	 * @deprecated
	 */
	public function get_prism_slug() {
		return $this->get_attribute( 'prism_slug' );
	}

	/**
	 * @return string
	 */
	public function compute_prism_slug() {
		$map = array(
			'js'   => 'javascript',
			'sass' => 'scss',
			'py'   => 'python',
			'html' => 'markup',
			'xml'  => 'markup',
		);

		$slug = $this->get_attribute( 'slug' );

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
	 * @deprecated
	 */
	public function get_file_ext() {
		return $this->get_attribute( 'file_ext' );
	}

	/**
	 * @return string
	 */
	public function compute_file_ext() {
		$map = array(
			'sass'         => 'scss',
			'bash'         => 'sh',
			'ruby'         => 'rb',
			'plaintext'    => 'txt',
			'csharp'       => 'cs',
			'coffeescript' => 'coffee',
			'objectivec'   => 'm',
			'actionscript' => 'as',
			'eiffel'       => 'e',
			'erlang'       => 'erl',
			'gherkin'      => 'feature',
			'git'          => 'diff',
			'perl'         => 'pl',
			'latex'        => 'tex',
			'markdown'     => 'md',
			'nasm'         => 'asm',
			'powershell'   => 'ps1',
			'rust'         => 'rs',
			'scheme'       => 'scm',
			'smarty'       => 'tpl',
		);

		$slug = $this->get_attribute( 'slug' );

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
	 * @deprecated
	 */
	public function get_display_name() {
		return $this->get_attribute( 'display_name' );
	}

	public function related_blobs() {
		return $this->has_many(
			self::BLOB_CLASS,
			'object',
			'post_id'
		);
	}

	/**
	 * @return string
	 */
	public function compute_display_name() {
		return array_search( $this->get_attribute( 'slug' ), self::$supported );
	}

	/**
	 * Override the built-in serialize method so a string is output
	 * when the Blob serializes into an array.
	 *
	 * @return string
	 */
	public function serialize() {
		return $this->get_attribute( 'slug' );
	}
}
