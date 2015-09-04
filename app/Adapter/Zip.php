<?php
namespace Intraxia\Gistpen\Adapter;

use Intraxia\Gistpen\Model\Zip as ZipModel;

/**
 * Builds zip models based on various data inputs
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Zip {

	/**
	 * Build a Zip model by Gist API data
	 *
	 * @param  array $gist Gist API data
	 * @return Zip
	 * @since  0.5.0
	 */
	public function by_gist( $gist ) {
		$zip = $this->blank();

		$zip->set_description( $gist['description'] );

		if ( $gist['public'] ) {
			$status = 'publish';
		} else {
			$status = 'private';
		}
		$zip->set_status( $status );

		$zip->set_gist_id( $gist['id'] );

		$time = str_replace( 'Z', '', str_replace( 'T', ' ', $gist['created_at'] ) );
		$zip->set_create_date( $time );

		return $zip;
	}


	/**
	 * Builds a blank zip model
	 *
	 * @return ZipModel
	 * @since 0.5.0
	 */
	public function blank() {
		return new ZipModel();
	}

}
