<?php
namespace Intraxia\Gistpen\Contract;

use Intraxia\Jaxion\Contract\Axolotl\Serializes;

interface Translator extends Serializes {
	public function translate  ( $key );
}
