<?php
namespace Intraxia\Gistpen\Adapter;

use Intraxia\Gistpen\Collection\History as HistoryCollection;
/**
 * Adapts data to build a Commit History.
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class History {

	/**
	 * Builds a blank History collection
	 *
	 * @return HistoryCollection
	 * @since 0.5.0
	 */
	public function blank() {
		return new HistoryCollection();
	}
}
