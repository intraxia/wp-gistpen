<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class manipulates the Gistpen post content.
 *
 * @package WP_Gistpen_Language
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Language extends WP_Gistpen_Abtract {

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
		'JScript' => 'js',
		'PHP' => 'php',
		'PlainText' => 'plaintext',
		'Python' => 'py',
		'Ruby' => 'ruby',
		'Sass' => 'sass',
		'Scala' => 'scala',
		'Sql' => 'sql',
		'C' => 'c',
		'Go' => 'go',
		'HTTP' => 'http',
		'ini' => 'ini',
		'HTML/Markup' => 'markup',
		'Objective-C' => 'objectivec',
		'Swift' => 'swift',
		'Twig' => 'twig'
	);

	public $slug;

	protected $prism_slug;

	protected $file_ext;

	protected $display_name;

	protected $term;

	public function __construct( stdClass $language ) {
		$this->term = $language;

		if ( isset( $this->term->slug ) ) {
			$this->slug = $this->term->slug;
		}
	}

	/**
	 * Functions to get protected properties
	 *
	 * @since  0.4.0
	 */
	protected function get_term() {
		return $this->term;
	}
	protected function get_prism_slug() {
		$this->prism_slug = ( $this->slug == 'js' ? 'javascript' :
			( $this->slug == 'sass' ? 'scss' :
			( $this->slug == 'sh' ? 'bash' :
			$this->slug ) ) );

		return $this->prism_slug;
	}
	protected function get_file_ext() {

		if ( ! isset( $this->prism_slug ) ) {
			$this->prism_slug = ( $this->slug == 'sass' ? 'scss' :
				( $this->slug == 'bash' ? 'sh' :
				$this->slug ) ) ;
		}

		return $this->prism_slug;
	}
	protected function get_display_name() {
		$this->display_name = array_search( $this->slug, self::$supported );

		return $this->display_name;
	}

	/**
	 * Updates the post object with new language slug
	 *
	 * @since 0.4.0
	 */
	public function update_post() {
		$result = WP_Gistpen::get_instance()->query->get_language_term_by_slug( $this->slug );

		if( !is_wp_error( $result ) ) {
			$this->term  = $result;
		}
	}

}
