<?php
namespace Intraxia\Gistpen\Facade;

use Intraxia\Gistpen\Adapter\Commit as CommitAdapter;
use Intraxia\Gistpen\Adapter\File as FileAdapter;
use Intraxia\Gistpen\Adapter\Gist as GistAdapter;
use Intraxia\Gistpen\Adapter\History as HistoryAdapter;
use Intraxia\Gistpen\Adapter\Api as ApiAdapter;
use Intraxia\Gistpen\Adapter\Language as LanguageAdapter;
use Intraxia\Gistpen\Adapter\State as StateAdapter;
use Intraxia\Gistpen\Adapter\Zip as ZipAdapter;

/**
 * This is the class description.
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Adapter {

	/**
	 * Intraxia\Gistpen\Adapter\Commit object
	 *
	 * @var CommitAdapter
	 * @since 0.5.0
	 */
	protected $commit;

	/**
	 * Intraxia\Gistpen\Adapter\File object
	 *
	 * @var FileAdapter
	 * @since 0.5.0
	 */
	protected $file;

	/**
	 * Intraxia\Gistpen\Adapter\History object
	 *
	 * @var   HistoryAdapter
	 * @since 0.5.0
	 */
	protected $history;

	/**
	 * Intraxia\Gistpen\Adapter\Api object
	 *
	 * @var ApiAdapter
	 * @since 0.5.0
	 */
	protected $api;

	/**
	 * Intraxia\Gistpen\Adapter\Language object
	 *
	 * @var LanguageAdapter
	 * @since 0.5.0
	 */
	protected $language;

	/**
	 * Intraxia\Gistpen\Adapter\State object
	 *
	 * @var StateAdapter
	 * @since 0.5.0
	 */
	protected $state;

	/**
	 * Intraxia\Gistpen\Adapter\Zip object
	 *
	 * @var ZipAdapter
	 * @since 0.5.0
	 */
	protected $zip;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		$this->commit = new CommitAdapter();
		$this->file = new FileAdapter();
		$this->history = new HistoryAdapter();
		$this->api = new ApiAdapter();
		$this->language = new LanguageAdapter();
		$this->state = new StateAdapter();
		$this->zip = new ZipAdapter();

	}

	/**
	 * Return the Adapter object for the specified model.
	 *
	 * @since    0.5.0
	 * @var      string    $model       The model type to prepare to build.
	 * @return   mixed
	 */
	public function build( $model ) {

		if ( ! property_exists( $this, $model ) ) {
			throw new \Exception( "Can't build model {$model}" );
		}

		return $this->{$model};
	}
}
