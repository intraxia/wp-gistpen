<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Collection;
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
 * @property int        $ID
 * @property string     $slug
 * @property string     $prism_slug
 * @property string     $display_name
 * @property Collection $blobs
 */
class Language extends Model implements UsesWordPressTerm {
	/**
	 * Related Blob class for the Language.
	 */
	const BLOB_CLASS = 'Intraxia\Gistpen\Model\Blob';

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
	 * @var array
	 */
	protected $visible = array(
		'ID',
		'display_name',
		'slug',
		'prism_slug',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public static function get_taxonomy() {
		return 'wpgp_language';
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
	 * @return string
	 */
	public function compute_display_name() {
		return array_search( $this->get_attribute( 'slug' ), self::$supported, true );
	}
}
