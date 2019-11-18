<?php
namespace Intraxia\Gistpen\Contract;

use Intraxia\Jaxion\Contract\Axolotl\Serializes;

interface Translations extends Serializes {
	public function translate( $key );
}
