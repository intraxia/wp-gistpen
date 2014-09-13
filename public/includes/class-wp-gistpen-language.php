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

	protected $prism_slug;

	protected $file_ext;

	protected $display_name;

	protected $term;

	public function __construct( stdClass $language ) {
		$this->term = $language;
	}

	protected function get_prism_slug() {

		if ( ! isset( $this->prism_slug ) ) {
			$this->prism_slug = ( $this->term->slug == 'js' ? 'javascript' :
				( $this->term->slug == 'sass' ? 'scss' :
				$this->term->slug ) );
		}

		return $this->prism_slug;
	}

	protected function get_file_ext() {

		if ( ! isset( $this->prism_slug ) ) {
			$this->prism_slug = ( $this->term->slug == 'sass' ? 'scss' :
				( $this->term->slug == 'bash' ? 'sh' :
				$this->term->slug ) ) ;
		}

		return $this->prism_slug;
	}

	protected function get_display_name() {

		if ( ! isset( $this->display_name ) ) {
			$this->display_name = $this->term->name;;
		}

		return $this->display_name;
	}

}
