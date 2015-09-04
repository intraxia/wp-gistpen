<?php
namespace Intraxia\Gistpen\Adapter;

use Intraxia\Gistpen\Model\Language as LanguageModel;

/**
 * Builds language models based on various data inputs
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Language {

	/**
	 * Map of Gist to Gistpen languages
	 * @var   array
	 * @since 0.5.0
	 */
	protected $map = array(
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

	/**
	 * Builds the Language object by language slug
	 *
	 * @param  string $slug
	 * @return Language       Language object
	 * @since 0.4.0
	 */
	public function by_slug( $slug ) {
		return new LanguageModel( $slug );
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
			$language = new LanguageModel( $slug );
		} catch ( \Exception $e ) {
			// Default to "plaintext" if we don't support the imported language
			$language = new LanguageModel( 'plaintext' );
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
		return new LanguageModel();
	}
}
