<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\UsesCustomTable;

/**
 * Class Message
 *
 * @package    Intraxia\Gistpen
 * @subpackage Model
 *
 * @property int    $ID
 * @property int    $run_id
 * @property string $text
 * @property string $level
 * @property string $logged_at
 */
class Message extends Model implements UsesCustomTable {
	/**
	 * Returns the custom table name used by the model.
	 *
	 * @return string
	 */
	public static function get_table_name() {
		return 'messages';
	}

	/**
	 * Get the attribute used as the primary key.
	 *
	 * @return string
	 */
	public static function get_primary_key() {
		return 'ID';
	}
}
