<?php
namespace Intraxia\Gistpen\Contract;

use Intraxia\Jaxion\Contract\Axolotl\Serializes;

interface Translations extends Serializes {
	/**
	 * Translate the provided key into the current language.
	 *
	 * @param  string $key Key to translate.
	 * @return string      Translation.
	 */
	public function translate( $key );
}
