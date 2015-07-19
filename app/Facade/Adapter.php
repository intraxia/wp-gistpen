<?php
namespace WP_Gistpen\Facade;

use WP_Gistpen\Adapter\Commit as CommitAdapter;
use WP_Gistpen\Adapter\File as FileAdapter;
use WP_Gistpen\Adapter\Gist as GistAdapter;
use WP_Gistpen\Adapter\History as HistoryAdapter;
use WP_Gistpen\Adapter\Api as ApiAdapter;
use WP_Gistpen\Adapter\Language as LanguageAdapter;
use WP_Gistpen\Adapter\State as StateAdapter;
use WP_Gistpen\Adapter\Zip as ZipAdapter;

/**
 * This is the class description.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Adapter {

	/**
	 * WP_Gistpen\Adapter\Commit object
	 *
	 * @var CommitAdapter
	 * @since 0.5.0
	 */
	protected $commit;

	/**
	 * WP_Gistpen\Adapter\File object
	 *
	 * @var FileAdapter
	 * @since 0.5.0
	 */
	protected $file;

	/**
	 * WP_Gistpen\Adapter\Gist object
	 *
	 * @var GistAdapter
	 * @since 0.5.0
	 */
	protected $gist;

	/**
	 * WP_Gistpen\Adapter\History object
	 *
	 * @var   HistoryAdapter
	 * @since 0.5.0
	 */
	protected $history;

	/**
	 * WP_Gistpen\Adapter\Api object
	 *
	 * @var ApiAdapter
	 * @since 0.5.0
	 */
	protected $api;

	/**
	 * WP_Gistpen\Adapter\Language object
	 *
	 * @var LanguageAdapter
	 * @since 0.5.0
	 */
	protected $language;

	/**
	 * WP_Gistpen\Adapter\State object
	 *
	 * @var StateAdapter
	 * @since 0.5.0
	 */
	protected $state;

	/**
	 * WP_Gistpen\Adapter\Zip object
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
		$this->gist = new GistAdapter();
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
	 */
	public function build( $model ) {

		if ( ! property_exists( $this, $model ) ) {
			throw new \Exception( "Can't build model {$model}" );
		}

		return $this->{$model};
	}
}
