<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Gistpen\App;
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
	 * Get the display name for the Language.
	 *
	 * @return string
	 */
	public function compute_display_name() {
		// @todo move serialization out of the model into a serializer?
		$languages = App::instance()->fetch( 'config' )->get_config_json( 'languages' );

		return isset( $languages['list'][ $this->get_attribute( 'slug' ) ] ) ?
			$languages['list'][ $this->get_attribute( 'slug' ) ] :
			$languages['list']['plaintext'];
	}
}
